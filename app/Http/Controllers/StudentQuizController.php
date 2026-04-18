<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;

class StudentQuizController extends Controller
{
    // Display available quizzes for student
    public function index()
    {
        $user = auth()->user();

        $quizzes = Quiz::query()
            ->with('questions')
            ->active()
            ->where(function ($q) {
                $q->where('due_date', '>=', now())->orWhereNull('due_date');
            })
            ->get()
            ->filter(fn (Quiz $quiz) => $quiz->isVisibleToStudent($user))
            ->values();

        return view('student.quizzes.index', compact('quizzes'));
    }

    // Take quiz
    public function take($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        
        // Check if quiz is active
        if ($quiz->status !== 'active') {
            abort(403, 'This quiz is not currently available.');
        }

        if (! $quiz->isVisibleToStudent(auth()->user())) {
            abort(403, 'This quiz is not assigned to your class.');
        }

        // Check if student already attempted this quiz
        $existingAttempt = QuizAttempt::where('quiz_id', $id)
            ->where('student_id', auth()->id())
            ->first();
            
        if ($existingAttempt) {
            return redirect()->route('student.quizzes.result', $quiz->id)
                ->with('info', 'You have already completed this quiz.');
        }

        return view('student.quizzes.take', compact('quiz'));
    }

    // Submit quiz and show AI feedback
    public function submit(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        if ($quiz->status !== 'active') {
            abort(403, 'This quiz is not currently available.');
        }

        if (! $quiz->isVisibleToStudent(auth()->user())) {
            abort(403, 'This quiz is not assigned to your class.');
        }
        
        // Check if student already attempted this quiz
        $existingAttempt = QuizAttempt::where('quiz_id', $id)
            ->where('student_id', auth()->id())
            ->first();
            
        if ($existingAttempt) {
            return redirect()->route('student.quizzes.result', $quiz->id)
                ->with('info', 'You have already completed this quiz.');
        }

        // Validate answers
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        // Calculate score
        $questions = Question::where('quiz_id', $id)->get();
        $correctCount = 0;
        $totalQuestions = $questions->count();
        $answers = [];

        foreach ($questions as $question) {
            $studentAnswer = $request->answers[$question->id] ?? null;
            $answers[$question->id] = [
                'answer' => $studentAnswer,
                'correct' => $studentAnswer == $question->correct_answer,
                'correct_answer' => $question->correct_answer,
            ];
            if ($studentAnswer && $studentAnswer == $question->correct_answer) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Generate feedback based on score
        if ($score >= 90) {
            $feedback = "Excellent work! You have mastered this topic. Keep up the great performance!";
        } elseif ($score >= 75) {
            $feedback = "Good job! You have a solid understanding. Review the few mistakes to improve further.";
        } elseif ($score >= 60) {
            $feedback = "Fair attempt. You understand the basics but need more practice on some concepts.";
        } else {
            $feedback = "Keep trying! Review the lesson materials and attempt the quiz again after more study.";
        }

        // Award XP to student
        $xpEarned = 0;
        if ($score >= 90) {
            $xpEarned = 100;
        } elseif ($score >= 75) {
            $xpEarned = 75;
        } elseif ($score >= 60) {
            $xpEarned = 50;
        } else {
            $xpEarned = 25;
        }

        // Update student XP (User model — not Authenticatable interface)
        $user = User::findOrFail(auth()->id());
        $user->xp += $xpEarned;
        $user->save();

        // Save quiz attempt to database
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => auth()->id(),
            'score' => $score,
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions,
            'xp_earned' => $xpEarned,
            'answers' => $answers,
        ]);

        // Store result in session and redirect (POST-Redirect-GET pattern)
        return redirect()->route('student.quizzes.result', $quiz->id)
            ->with([
                'feedback' => $feedback,
                'score' => $score,
                'correctCount' => $correctCount,
                'totalQuestions' => $totalQuestions,
                'xpEarned' => $xpEarned,
            ]);
    }

    // Show quiz result
    public function result($id)
    {
        $quiz = Quiz::findOrFail($id);
        
        // Try to get attempt from database first
        $attempt = QuizAttempt::where('quiz_id', $id)
            ->where('student_id', auth()->id())
            ->first();

        if ($attempt) {
            // Generate feedback
            $score = $attempt->score;
            if ($score >= 90) {
                $feedback = "Excellent work! You have mastered this topic.";
            } elseif ($score >= 75) {
                $feedback = "Good job! You have a solid understanding.";
            } elseif ($score >= 60) {
                $feedback = "Fair attempt. You understand the basics.";
            } else {
                $feedback = "Keep trying! Review the lesson materials.";
            }

            return view('student.quizzes.result', [
                'feedback' => $feedback,
                'score' => $attempt->score,
                'correctCount' => $attempt->correct_answers,
                'totalQuestions' => $attempt->total_questions,
                'xpEarned' => $attempt->xp_earned,
                'quiz' => $quiz,
                'attempt' => $attempt,
            ]);
        }

        // Check if there's a result in session
        if (!session()->has('score')) {
            return redirect()->route('student.quizzes')
                ->with('error', 'No quiz result found. Please take a quiz first.');
        }

        $feedback = session('feedback');
        $score = session('score');
        $correctCount = session('correctCount');
        $totalQuestions = session('totalQuestions');
        $xpEarned = session('xpEarned');

        return view('student.quizzes.result', compact('feedback', 'score', 'correctCount', 'totalQuestions', 'xpEarned', 'quiz'));
    }

    // Show student's quiz history
    public function history()
    {
        $attempts = QuizAttempt::with('quiz')
            ->where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.quizzes.history', compact('attempts'));
    }
}
