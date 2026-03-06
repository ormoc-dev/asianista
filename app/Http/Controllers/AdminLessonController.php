<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LessonStatusNotification;

class AdminLessonController extends Controller
{
    /**
     * Display all lessons uploaded by teachers (pending, approved, rejected)
     */
    public function index()
    {
        // Fetch all lessons with teacher relationship
        $lessons = Lesson::with('teacher')->orderByDesc('created_at')->get();

        return view('admin.lessons.index', compact('lessons'));
    }

    /**
     * Approve a lesson
     */
    public function approve($id)
    {
        $lesson = Lesson::findOrFail($id);

        // Only update if the lesson is still pending
        if ($lesson->status !== 'pending') {
            return back()->with('warning', 'This lesson has already been reviewed.');
        }

        $lesson->update(['status' => 'approved']);

        // Optionally notify the teacher
        if ($lesson->teacher && $lesson->teacher->email) {
            Notification::send($lesson->teacher, new LessonStatusNotification($lesson, 'approved'));
        }

        return back()->with('success', '✅ Lesson approved successfully!');
    }

    /**
     * Reject a lesson
     */
    public function reject($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->status !== 'pending') {
            return back()->with('warning', 'This lesson has already been reviewed.');
        }

        $lesson->update(['status' => 'rejected']);

        if ($lesson->teacher && $lesson->teacher->email) {
            Notification::send($lesson->teacher, new LessonStatusNotification($lesson, 'rejected'));
        }

        return back()->with('success', '❌ Lesson rejected successfully.');
    }
}
