<?php

namespace App\Http\Livewire\Teacher\Lessons;

use Livewire\Component;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    public function delete($id)
    {
        $lesson = Lesson::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);

        if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();
        session()->flash('success', 'Lesson deleted successfully.');
    }

    public function render()
    {
        $lessons = Lesson::query()
            ->ownedByTeacher((int) Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('livewire.teacher.lessons.index', [
            'lessons' => $lessons
        ])->layout('livewire.teacher.app-layout');
    }
}
