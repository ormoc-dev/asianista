<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;

class AppLayout extends Component
{
    public function render()
    {
        try {
            $activeQuest = \App\Models\Quest::latest()->first();
            $activeAttempt = null;
            
            if ($activeQuest) {
                $activeAttempt = \App\Models\QuestAttempt::where('user_id', \Illuminate\Support\Facades\Auth::id())
                                                        ->where('quest_id', $activeQuest->id)
                                                        ->first();
            }

            return view('livewire.student.app-layout', compact('activeQuest', 'activeAttempt'));
        } catch (\Exception $e) {
            // If there's an error, render without quest data
            return view('livewire.student.app-layout', ['activeQuest' => null, 'activeAttempt' => null]);
        }
    }
}
