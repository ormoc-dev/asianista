<?php

namespace App\Http\Controllers;

use App\Models\Lesson;

class AdminLessonController extends Controller
{
    /**
     * Display all lessons uploaded by teachers.
     */
    public function index()
    {
        $lessons = Lesson::with('teacher')->orderByDesc('created_at')->get();

        return view('admin.lessons.index', compact('lessons'));
    }

    /**
     * Display a single lesson (any status) for review.
     */
    public function show(Lesson $lesson)
    {
        $lesson->load('teacher');

        return view('admin.lessons.show', compact('lesson'));
    }
}
