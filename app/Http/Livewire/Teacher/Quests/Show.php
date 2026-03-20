<?php

namespace App\Http\Livewire\Teacher\Quests;

use Livewire\Component;
use App\Models\Quest;

class Show extends Component
{
    public $quest;
    public $showModal = false;
    public $selectedLevel = 1;
    public $levelQuestions = [];

    public function mount($quest)
    {
        $this->quest = Quest::with(['questions', 'grade', 'section'])->findOrFail($quest);
    }

    public function showLevelDetails($level)
    {
        $this->selectedLevel = $level;
        $this->levelQuestions = $this->quest->questions->where('level', $level)->values();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.teacher.quests.show')
            ->layout('livewire.teacher.app-layout');
    }
}
