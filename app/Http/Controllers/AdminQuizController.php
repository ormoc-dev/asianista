<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class AdminQuizController extends Controller
{
    /**
     * Display all quizzes uploaded by teachers.
     */
    public function index()
    {
        $quizzes = Quiz::with(['teacher', 'grade', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Approve a quiz.
     */
    public function approve($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['status' => 'active']);

        return back()->with('success', '✅ Quiz approved successfully!');
    }

    /**
     * Reject a quiz.
     */
    public function reject($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['status' => 'inactive']);

        return back()->with('error', '❌ Quiz rejected successfully.');
    }
}
