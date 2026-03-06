<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    // Display available quizzes for student
    public function index()
    {
        $quizzes = [
            ['id' => 1, 'title' => 'Pre-Test: Math Basics', 'status' => 'Available'],
            ['id' => 2, 'title' => 'Post-Test: Algebra', 'status' => 'Available'],
        ];

        return view('student.quizzes.index', compact('quizzes'));
    }

    // Take quiz
    public function take($id)
    {
        // Example: load the quiz questions
        return view('student.quizzes.take', compact('id'));
    }

    // Submit quiz and show AI feedback
    public function submit(Request $request, $id)
    {
        // AI-generated feedback (placeholder)
        $feedback = "Great effort! You performed well in algebra concepts. Review fractions for improvement.";
        return view('student.quizzes.result', compact('feedback'));
    }
}
