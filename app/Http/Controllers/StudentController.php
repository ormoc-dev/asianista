<?php

namespace App\Http\Controllers;

use App\Models\QuestAttempt;
use App\Models\QuizAttempt;
use App\Models\RegistrationCode;
use App\Models\StudentFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard() {
        $activeQuest = \App\Models\Quest::latest()->first();
        $activeAttempt = null;
        
        if ($activeQuest) {
            $activeAttempt = \App\Models\QuestAttempt::where('user_id', \Illuminate\Support\Facades\Auth::id())
                                                    ->where('quest_id', $activeQuest->id)
                                                    ->first();
        }

        return view('student.dashboard', compact('activeQuest', 'activeAttempt'));
    }

    public function registration()
    {
        $registrationRecord = RegistrationCode::query()
            ->where('user_id', Auth::id())
            ->with(['teacher:id,name,profile_pic,email', 'grade', 'section'])
            ->first();

        return view('student.sections.registration', compact('registrationRecord'));
    }

    public function gamification() {
        return view('student.sections.gamification');
    }

    public function aiSupport() {
        return view('student.ai-support.index');
    }

    public function performance()
    {
        $user = Auth::user();
        $userId = Auth::id();

        $quizAttempts = QuizAttempt::query()
            ->where('student_id', $userId)
            ->with('quiz')
            ->orderByDesc('created_at')
            ->get();

        $quizCount = $quizAttempts->count();
        $avgQuizScore = $quizCount > 0 ? round((float) $quizAttempts->avg('score'), 1) : null;
        $totalQuizXp = (int) $quizAttempts->sum('xp_earned');
        $bestQuizScore = $quizCount > 0 ? (int) $quizAttempts->max('score') : null;

        $questAttempts = QuestAttempt::query()
            ->where('user_id', $userId)
            ->with('quest')
            ->orderByDesc('updated_at')
            ->get();

        $questsCompleted = $questAttempts->where('status', 'completed')->count();
        $questsInProgress = $questAttempts->where('status', '!=', 'completed')->count();

        $recentQuizzes = QuizAttempt::query()
            ->where('student_id', $userId)
            ->with('quiz')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $performanceBand = null;
        if ($avgQuizScore !== null) {
            if ($avgQuizScore >= 90) {
                $performanceBand = ['label' => 'Excellent', 'class' => 'excellent'];
            } elseif ($avgQuizScore >= 75) {
                $performanceBand = ['label' => 'Good', 'class' => 'good'];
            } elseif ($avgQuizScore >= 60) {
                $performanceBand = ['label' => 'Developing', 'class' => 'average'];
            } else {
                $performanceBand = ['label' => 'Keep practicing', 'class' => 'needs'];
            }
        }

        return view('student.sections.performance', compact(
            'user',
            'quizAttempts',
            'quizCount',
            'avgQuizScore',
            'bestQuizScore',
            'totalQuizXp',
            'questAttempts',
            'questsCompleted',
            'questsInProgress',
            'recentQuizzes',
            'performanceBand'
        ));
    }

    public function feedback()
    {
        $studentId = (int) Auth::id();

        StudentFeedback::query()
            ->where('student_id', $studentId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $feedbacks = StudentFeedback::query()
            ->where('student_id', $studentId)
            ->with('teacher:id,name,profile_pic')
            ->orderByDesc('created_at')
            ->get();

        return view('student.sections.feedback', compact('feedbacks'));
    }

    public function motivation() {
        return view('student.sections.motivation');
    }
        public function quest()
    {
        // Add your logic for the quest page here.
        return view('student.quest');  // Make sure the view exists.
    }
}
