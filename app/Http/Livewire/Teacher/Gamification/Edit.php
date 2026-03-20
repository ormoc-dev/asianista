<?php

namespace App\Http\Livewire\Teacher\Gamification;

use Livewire\Component;
use App\Models\Challenge;

class Edit extends Component
{
    public $challengeId;
    public $title;
    public $points;
    public $description;

    protected $rules = [
        'title' => 'required|string|max:255',
        'points' => 'required|integer|min:1',
        'description' => 'required|string',
    ];

    public function mount($id)
    {
        $challenge = Challenge::findOrFail($id);
        $this->challengeId = $challenge->id;
        $this->title = $challenge->title;
        $this->points = $challenge->points;
        $this->description = $challenge->description;
    }

    public function update()
    {
        $this->validate();

        $challenge = Challenge::findOrFail($this->challengeId);
        $challenge->update([
            'title' => $this->title,
            'points' => $this->points,
            'description' => $this->description,
        ]);

        session()->flash('success', '✅ Challenge updated successfully!');

        return redirect()->route('teacher.gamification.index');
    }

    public function render()
    {
        return view('livewire.teacher.gamification.edit')
            ->layout('livewire.teacher.app-layout');
    }
}
