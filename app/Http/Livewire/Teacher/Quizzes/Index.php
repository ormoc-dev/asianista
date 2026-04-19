<?php

namespace App\Http\Livewire\Teacher\Quizzes;

use Livewire\Component;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    public function delete($id)
    {
        $quiz = Quiz::where('teacher_id', Auth::id())->findOrFail($id);

        if ($quiz->file_path && Storage::disk('public')->exists($quiz->file_path)) {
            Storage::disk('public')->delete($quiz->file_path);
        }

        $quiz->delete();
        session()->flash('success', '🗑️ Quiz deleted successfully!');
    }

    public function render()
    {
        $quizzes = Quiz::where('teacher_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.teacher.quizzes.index', [
            'quizzes' => $quizzes
        ])->layout('livewire.teacher.app-layout');
    }
}
