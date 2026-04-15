<?php

namespace App\Http\Livewire\Teacher\Lessons;

use Livewire\Component;
use App\Models\Lesson;
use App\View\Concerns\LivewireViewMacros;
use Livewire\WithFileUploads;
use Illuminate\View\View;

class Create extends Component
{
    use WithFileUploads;

    public $title;
    public $section;
    public $content;
    public $file;

    protected $rules = [
        'title' => 'required|string|max:255',
        'section' => 'required|string|max:100',
        'content' => 'nullable|string',
        'file' => 'nullable|file|max:20480', // 20MB
    ];

    public function save()
    {
        $this->validate();

        $filePath = null;
        if ($this->file) {
            $filePath = $this->file->store('lessons', 'public');
        }

        // Faking teacher ID as in the original controller
        $teacherId = 1;

        Lesson::create([
            'title' => $this->title,
            'section' => $this->section,
            'content' => $this->content,
            'file_path' => $filePath,
            'teacher_id' => $teacherId,
            'status' => 'approved',
        ]);

        session()->flash('success', 'Lesson published successfully.');

        return redirect()->route('teacher.lessons.index');
    }

    public function render(): View
    {
        /** @var View&LivewireViewMacros $page */
        $page = view('livewire.teacher.lessons.create');

        return $page->layout('livewire.teacher.app-layout');
    }
}
