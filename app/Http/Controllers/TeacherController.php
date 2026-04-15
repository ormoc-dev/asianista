<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Quest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function dashboard() {
        $teacherId = Auth::id();

        $stats = [
            'pending_students' => User::where('role', 'student')->where('status', 'pending')->count(),
            'approved_students' => User::where('role', 'student')->where('status', 'approved')->count(),
            'quests_created' => Quest::where('teacher_id', $teacherId)->count(),
            'lessons_created' => Lesson::where('teacher_id', $teacherId)->count(),
            'quizzes_created' => Quiz::where('teacher_id', $teacherId)->count(),
            'active_quizzes' => Quiz::where('teacher_id', $teacherId)->where('status', 'active')->count(),
        ];

        return view('teacher.dashboard', compact('stats'));
    }

    public function registration()
{
    // Get all students assigned to this teacher
    $students = User::where('role', 'student')->get();
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
        // Get all students
        $students = User::where('role', 'student')->get();
        
        // Get quiz statistics
        $quizStats = QuizAttempt::select(
            'quiz_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as total_attempts'),
            DB::raw('MAX(score) as highest_score'),
            DB::raw('MIN(score) as lowest_score')
        )->groupBy('quiz_id')->with('quiz')->get();
        
        // Get student performance rankings
        $studentRankings = QuizAttempt::select(
            'student_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as quizzes_taken'),
            DB::raw('SUM(xp_earned) as total_xp')
        )->groupBy('student_id')->with('student')->orderBy('average_score', 'desc')->get();
        
        // Get recent quiz attempts
        $recentAttempts = QuizAttempt::with(['student', 'quiz'])->latest()->take(10)->get();
        
        // Overall statistics
        $overallStats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_quizzes' => Quiz::count(),
            'total_lessons' => Lesson::where('status', 'approved')->count(),
            'total_attempts' => QuizAttempt::count(),
            'class_average' => QuizAttempt::avg('score') ?? 0,
        ];
        
        return view('teacher.performance', compact('students', 'quizStats', 'studentRankings', 'recentAttempts', 'overallStats'));
    }

    public function feedback() {
        // Get students with their performance data for feedback
        $students = User::where('role', 'student')
            ->with(['quizAttempts' => function($query) {
                $query->latest();
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
            'student_id' => 'required|exists:users,id',
            'type' => 'required|in:praise,improvement,concern',
            'message' => 'required|string|min:10|max:1000',
        ]);

        // Here you would typically save the feedback to a database
        // For now, we'll just return with success message
        
        return redirect()->route('teacher.feedback')
            ->with('success', 'Feedback sent successfully to student!');
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
