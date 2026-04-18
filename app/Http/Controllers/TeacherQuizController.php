<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\Section;
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
            ->with(['grade', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the create quiz form.
     */
    public function create()
    {
        $grades = Grade::orderBy('name')->get();

        return view('teacher.quizzes.create', compact('grades'));
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
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:20480',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,identification',
            'questions.*.answer' => 'required|string',
        ]);

        $section = Section::find($request->section_id);
        if (! $section || (int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('quizzes', 'public');
        }

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => 'pending', // waiting for admin approval
            'file_path' => $filePath,
            'assign_date' => $request->assign_date,
            'due_date' => $request->due_date,
            'teacher_id' => Auth::id(),
            'grade_id' => $request->grade_id,
            'section_id' => $request->section_id,
        ]);

        // Save questions
        if ($request->has('questions')) {
            foreach ($request->questions as $qData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $qData['question'],
                    'type' => $qData['type'],
                    'choices' => isset($qData['options']) ? $qData['options'] : null,
                    'correct_answer' => $qData['answer'],
                    'points' => $qData['points'] ?? 10,
                ]);
            }
        }

        return redirect()->route('teacher.quizzes')
            ->with('success', '✅ Quiz created successfully and sent for admin approval.');
    }

    /**
     * Edit a quiz.
     */
    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        $grades = Grade::orderBy('name')->get();

        return view('teacher.quizzes.edit', compact('quiz', 'grades'));
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
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:20480',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,identification',
            'questions.*.answer' => 'required|string',
        ]);

        $section = Section::find($request->section_id);
        if (! $section || (int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

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
            'grade_id' => $request->grade_id,
            'section_id' => $request->section_id,
            // Status stays unchanged (still pending or active)
        ]);

        // Delete existing questions and recreate
        $quiz->questions()->delete();

        // Save updated questions
        if ($request->has('questions')) {
            foreach ($request->questions as $qData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $qData['question'],
                    'type' => $qData['type'],
                    'choices' => isset($qData['options']) ? $qData['options'] : null,
                    'correct_answer' => $qData['answer'],
                    'points' => $qData['points'] ?? 10,
                ]);
            }
        }

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

    /**
     * Show student scores for a specific quiz.
     */
    public function scores($id)
    {
        $quiz = Quiz::with('questions')->where('teacher_id', Auth::id())->findOrFail($id);
        
        $attempts = QuizAttempt::with('student')
            ->where('quiz_id', $id)
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalStudents = $attempts->count();
        $averageScore = $totalStudents > 0 ? round($attempts->avg('score')) : 0;
        $highestScore = $totalStudents > 0 ? $attempts->max('score') : 0;
        $lowestScore = $totalStudents > 0 ? $attempts->min('score') : 0;
        $passRate = $totalStudents > 0 ? round(($attempts->where('score', '>=', 75)->count() / $totalStudents) * 100) : 0;

        return view('teacher.quizzes.scores', compact('quiz', 'attempts', 'totalStudents', 'averageScore', 'highestScore', 'lowestScore', 'passRate'));
    }
}
