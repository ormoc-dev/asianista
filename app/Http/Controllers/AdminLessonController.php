<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
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

        // Notify teacher only when notifications table exists.
        if ($lesson->teacher && Schema::hasTable('notifications')) {
            Notification::send($lesson->teacher, new LessonStatusNotification('approved', $lesson->title));
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

        if ($lesson->teacher && Schema::hasTable('notifications')) {
            Notification::send($lesson->teacher, new LessonStatusNotification('rejected', $lesson->title));
        }

        return back()->with('success', '❌ Lesson rejected successfully.');
    }
}
