<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function registration() {
        return view('student.sections.registration');
    }

    public function lessons() {
        return view('student.sections.lessons');
    }

    public function gamification() {
        return view('student.sections.gamification');
    }

    public function aiSupport() {
        return view('student.ai-support.index');
    }

    public function performance() {
        return view('student.sections.performance');
    }

    public function feedback() {
        return view('student.sections.feedback');
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
