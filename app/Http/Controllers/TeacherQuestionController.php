<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class TeacherQuestionController extends Controller
{
    public function index($quiz_id)
    {
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);
        return view('teacher.quizzes.questions.index', compact('quiz'));
    }

    public function create($quiz_id)
    {
        $quiz = Quiz::findOrFail($quiz_id);
        return view('teacher.quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, $quiz_id)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,identification',
            'correct_answer' => 'required|string',
            'direction' => 'nullable|string',
            'choices' => 'nullable|array',
        ]);

        Question::create([
            'quiz_id' => $quiz_id,
            'question' => $request->question,
            'type' => $request->type,
            'choices' => $request->type === 'multiple_choice' ? json_encode($request->choices) : null,
            'correct_answer' => $request->correct_answer,
            'direction' => $request->direction,
        ]);

        return redirect()->route('teacher.quizzes.questions.index', $quiz_id)
            ->with('success', '✅ Question added successfully!');
    }
}
