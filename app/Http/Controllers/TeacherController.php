<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Quest;
use App\Models\StudentFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function dashboard() {
        $teacherId = (int) Auth::id();

        $stats = [
            'pending_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->where('status', 'pending')->count(),
            'approved_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->where('status', 'approved')->count(),
            'quests_created' => Quest::ownedByTeacher($teacherId)->count(),
            'lessons_created' => Lesson::ownedByTeacher($teacherId)->count(),
            'quizzes_created' => Quiz::where('teacher_id', $teacherId)->count(),
            'pending_quizzes' => Quiz::where('teacher_id', $teacherId)->where('status', 'pending')->count(),
            'active_quizzes' => Quiz::where('teacher_id', $teacherId)->where('status', 'active')->count(),
        ];

        return view('teacher.dashboard', compact('stats'));
    }

    public function registration()
{
    $students = User::where('role', 'student')->registeredByTeacher(Auth::id())->get();
    return view('teacher.index', compact('students')); // Using index.blade.php
}

    public function lessons() {
        return view('teacher.lessons');
    }

    public function quizzes() {
        return view('teacher.quizzes');
    }

    public function gamification() {
        return view('teacher.gamification');
    }

    public function aiTrack() {
        return view('teacher.ai-track');
    }

    public function performance() {
        $teacherId = Auth::id();

        $students = User::where('role', 'student')->registeredByTeacher($teacherId)->get();

        $quizStats = QuizAttempt::select(
            'quiz_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as total_attempts'),
            DB::raw('MAX(score) as highest_score'),
            DB::raw('MIN(score) as lowest_score')
        )
            ->whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))
            ->groupBy('quiz_id')
            ->with('quiz')
            ->get();

        $studentRankings = QuizAttempt::select(
            'student_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as quizzes_taken'),
            DB::raw('SUM(xp_earned) as total_xp')
        )
            ->whereHas('student', fn ($q) => $q->where('registered_by_teacher_id', $teacherId))
            ->groupBy('student_id')
            ->with('student')
            ->orderBy('average_score', 'desc')
            ->get();

        $recentAttempts = QuizAttempt::with(['student', 'quiz'])
            ->whereHas('student', fn ($q) => $q->where('registered_by_teacher_id', $teacherId))
            ->latest()
            ->take(10)
            ->get();

        $overallStats = [
            'total_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->count(),
            'total_quizzes' => Quiz::where('teacher_id', $teacherId)->count(),
            'total_lessons' => Lesson::where('status', 'approved')->where('teacher_id', $teacherId)->count(),
            'total_attempts' => QuizAttempt::whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))->count(),
            'class_average' => QuizAttempt::whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))->avg('score') ?? 0,
        ];

        return view('teacher.performance', compact('students', 'quizStats', 'studentRankings', 'recentAttempts', 'overallStats'));
    }

    public function feedback() {
        $teacherId = (int) Auth::id();

        $students = User::where('role', 'student')
            ->registeredByTeacher($teacherId)
            ->with(['quizAttempts' => function ($query) use ($teacherId) {
                $query->whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))
                    ->with('quiz')
                    ->latest();
            }])
            ->get()
            ->map(function($student) {
                $attempts = $student->quizAttempts;
                $student->average_score = $attempts->avg('score') ?? 0;
                $student->quizzes_taken = $attempts->count();
                $student->total_xp = $attempts->sum('xp_earned');
                $student->last_attempt = $attempts->first();
                return $student;
            })
            ->sortByDesc('average_score');
            
        return view('teacher.feedback', compact('students'));
    }

    public function sendFeedback(Request $request) {
        $request->validate([
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'student')->where('registered_by_teacher_id', Auth::id());
                }),
            ],
            'type' => 'required|in:praise,improvement,concern',
            'message' => 'required|string|min:10|max:1000',
        ]);

        StudentFeedback::create([
            'teacher_id' => (int) Auth::id(),
            'student_id' => (int) $request->input('student_id'),
            'type' => $request->input('type'),
            'message' => $request->input('message'),
        ]);

        return redirect()->route('teacher.feedback')
            ->with('success', 'Feedback sent successfully to the student. They can read it under Feedback in their account.');
    }

    public function reports() {
        return view('teacher.reports');
    }

    public function aiSupport() {
        return view('teacher.ai-support.index');
    }

    public function contentReview() {
        return view('teacher.content-review');
    }

    public function quest()
    {
        // Add your logic for the quest page here.
        return view('teacher.quest');  // Make sure the view exists.
    }
}
