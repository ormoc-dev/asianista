<?php

namespace App\Http\Livewire\Teacher\Lessons;

use Livewire\Component;
use App\Models\Lesson;
use App\View\Concerns\LivewireViewMacros;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class Edit extends Component
{
    use WithFileUploads;

    public $lessonId;
    public $title;
    public $section;
    public $newFile;

    protected $rules = [
        'title' => 'required|string|max:255',
        'section' => 'nullable|string|max:100',
        'newFile' => 'nullable|file|max:20480',
    ];

    public function mount($id)
    {
        $lesson = Lesson::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);
        $this->lessonId = $lesson->id;
        $this->title = $lesson->title;
        $this->section = $lesson->section;
    }

    public function update()
    {
        $this->validate();

        $lesson = Lesson::query()->ownedByTeacher((int) Auth::id())->findOrFail($this->lessonId);

        $filePath = $lesson->file_path;

        if ($this->newFile) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $this->newFile->store('lessons', 'public');
        }

        $lesson->update([
            'title' => $this->title,
            'section' => $this->section,
            'file_path' => $filePath,
            'status' => 'approved',
        ]);

        session()->flash('success', 'Lesson updated successfully.');

        return redirect()->route('teacher.lessons.index');
    }

    public function render(): View
    {
        /** @var View&LivewireViewMacros $page */
        $page = view('livewire.teacher.lessons.edit');

        return $page->layout('livewire.teacher.app-layout');
    }
}
