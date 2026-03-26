<?php

namespace App\Http\Livewire\Teacher\Gamification;

use Livewire\Component;
use App\Models\Challenge;

class Create extends Component
{
    public $title;
    public $points;
    public $description;

    protected $rules = [
        'title' => 'required|string|max:255',
        'points' => 'required|integer|min:1',
        'description' => 'required|string',
    ];

    public function save()
    {
        $this->validate();

        Challenge::create([
            'title' => $this->title,
            'points' => $this->points,
            'description' => $this->description,
        ]);

        session()->flash('success', '🎉 Challenge created successfully!');

        return redirect()->route('teacher.gamification.index');
    }

    public function render()
    {
        return view('livewire.teacher.gamification.create')
            ->layout('livewire.teacher.app-layout');
    }
}
