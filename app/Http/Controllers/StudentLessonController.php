<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class StudentLessonController extends Controller
{
    protected function authorizeLessonForStudent(Lesson $lesson): void
    {
        if ($lesson->status !== 'approved') {
            abort(403, 'This lesson is not yet approved by admin.');
        }

        if (! $lesson->isVisibleToStudent(auth()->user())) {
            abort(403, 'This lesson is not assigned to your class.');
        }
    }

    public function index()
    {
        $user = auth()->user();
        $lessons = Lesson::query()
            ->where('status', 'approved')
            ->visibleToStudent($user)
            ->latest()
            ->get();

        return view('student.lessons.index', compact('lessons'));
    }

    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        $this->authorizeLessonForStudent($lesson);

        return view('student.lessons.show', compact('lesson'));
    }

    public function download($id)
    {
        $lesson = Lesson::findOrFail($id);
        $this->authorizeLessonForStudent($lesson);

        if (! $lesson->file_path) {
            abort(404, 'No file for this lesson.');
        }

        return Storage::disk('public')->download($lesson->file_path);
    }
}
