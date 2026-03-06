<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class StudentLessonController extends Controller
{
    // Display only approved lessons
    public function index()
    {
        $lessons = Lesson::where('status', 'approved')->latest()->get();
        return view('student.lessons.index', compact('lessons'));
    }

    // Download lesson file
    public function download($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->status !== 'approved') {
            abort(403, 'This lesson is not yet approved by admin.');
        }

        return Storage::disk('public')->download($lesson->file_path);
    }
}
