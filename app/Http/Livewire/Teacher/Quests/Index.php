<?php

namespace App\Http\Livewire\Teacher\Quests;

use Livewire\Component;
use App\Models\Quest;

class Index extends Component
{
    public $search = '';

    public function render()
    {
        $quests = Quest::with(['grade', 'section'])
            ->where('title', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.teacher.quests.index', [
            'quests' => $quests
        ])->layout('livewire.teacher.app-layout');
    }
}
