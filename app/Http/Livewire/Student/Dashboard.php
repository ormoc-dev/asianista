<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;
use App\Models\Quest;
use App\Models\QuestAttempt;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $activeQuest = Quest::latest()->first();
        $activeAttempt = null;
        
        if ($activeQuest) {
            $activeAttempt = QuestAttempt::where('user_id', Auth::id())
                                        ->where('quest_id', $activeQuest->id)
                                        ->first();
        }

        return view('livewire.student.dashboard', compact('activeQuest', 'activeAttempt'))
            ->layout('livewire.student.app-layout');
    }
}
