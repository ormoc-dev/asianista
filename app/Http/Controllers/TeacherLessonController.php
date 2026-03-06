<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class TeacherLessonController extends Controller
{
    /**
     * Display all lessons uploaded by the current teacher.
     */
    public function index()
    {
        // For now, no authentication — show all lessons
        $lessons = Lesson::orderBy('created_at', 'desc')->get();

        return view('teacher.lessons.index', compact('lessons'));
    }

    /**
     * Show the form to upload a new lesson (text or file).
     */
    public function create()
    {
        return view('teacher.lessons.create');
    }

    /**
     * Store the uploaded lesson (text, file, or both) as pending.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,docx,pptx,zip,txt|max:20480',
            'section' => 'nullable|string|max:100',
        ]);

        $filePath = null;

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        // ✅ Temporarily fake teacher ID (since Auth isn't set up yet)
        $teacherId = 1; // you can change this to match your dummy teacher

        Lesson::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'file_path'  => $filePath,
            'teacher_id' => $teacherId,
            'status'     => 'pending', // admin must approve
            'section'    => $request->section,
        ]);

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson submitted successfully and is pending admin approval.');
    }

    /**
     * Download the uploaded lesson file.
     */
    public function download($filename)
    {
        $path = 'lessons/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            return response()->download(storage_path('app/public/' . $path));
        }

        return back()->with('error', 'File not found.');
    }

    /**
     * Edit a specific lesson.
     */
    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);
        return view('teacher.lessons.edit', compact('lesson'));
    }

    /**
     * Update the lesson content.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->status === 'approved') {
            return back()->with('error', 'Approved lessons cannot be edited.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,docx,pptx,zip,txt|max:20480',
            'section' => 'nullable|string|max:100',
        ]);

        $filePath = $lesson->file_path;

        if ($request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        $lesson->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'file_path' => $filePath,
            'section'   => $request->section,
            'status'    => 'pending',
        ]);

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson updated and sent for admin re-approval.');
    }

    /**
     * Delete a lesson.
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson deleted successfully.');
    }
}
