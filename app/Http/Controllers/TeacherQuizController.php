<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherQuizController extends Controller
{
    /**
     * Display all quizzes created by the logged-in teacher.
     */
    public function index()
    {
        $quizzes = Quiz::where('teacher_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the create quiz form.
     */
    public function create()
    {
        return view('teacher.quizzes.create');
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,pre-test,post-test',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:20480',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('quizzes', 'public');
        }

        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => 'pending', // waiting for admin approval
            'file_path' => $filePath,
            'assign_date' => $request->assign_date,
            'due_date' => $request->due_date,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('teacher.quizzes')
            ->with('success', '✅ Quiz created successfully and sent for admin approval.');
    }

    /**
     * Edit a quiz.
     */
    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        return view('teacher.quizzes.edit', compact('quiz'));
    }

    /**
     * Update a quiz.
     */
    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,pre-test,post-test',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:20480',
        ]);

        if ($request->hasFile('file')) {
            if ($quiz->file_path && Storage::disk('public')->exists($quiz->file_path)) {
                Storage::disk('public')->delete($quiz->file_path);
            }
            $quiz->file_path = $request->file('file')->store('quizzes', 'public');
        }

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'assign_date' => $request->assign_date,
            'due_date' => $request->due_date,
            'file_path' => $quiz->file_path,
            // Status stays unchanged (still pending or active)
        ]);

        return redirect()->route('teacher.quizzes')
            ->with('success', '✅ Quiz updated successfully!');
    }

    /**
     * Delete a quiz.
     */
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);

        if ($quiz->file_path && Storage::disk('public')->exists($quiz->file_path)) {
            Storage::disk('public')->delete($quiz->file_path);
        }

        $quiz->delete();

        return redirect()->route('teacher.quizzes')
            ->with('success', '🗑️ Quiz deleted successfully!');
    }
}
