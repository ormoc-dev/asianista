<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Quest;
use App\Models\StudentFeedback;
use App\Models\MinigameSetting;
use App\Models\MinigameAssignment;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    public function dashboard() {
        $teacherId = (int) Auth::id();

        $stats = [
            'pending_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->where('status', 'pending')->count(),
            'approved_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->where('status', 'approved')->count(),
            'quests_created' => Quest::ownedByTeacher($teacherId)->count(),
            'lessons_created' => Lesson::ownedByTeacher($teacherId)->count(),
            'quizzes_created' => Quiz::where('teacher_id', $teacherId)->count(),
            'pending_quizzes' => Quiz::where('teacher_id', $teacherId)->where('status', 'pending')->count(),
            'active_quizzes' => Quiz::where('teacher_id', $teacherId)->where('status', 'active')->count(),
        ];

        return view('teacher.dashboard', compact('stats'));
    }

    public function registration()
{
    $students = User::where('role', 'student')->registeredByTeacher(Auth::id())->get();
    return view('teacher.index', compact('students')); // Using index.blade.php
}

    public function lessons() {
        return view('teacher.lessons');
    }

    public function quizzes() {
        return view('teacher.quizzes');
    }

    public function miniGames() {
        $games = $this->getAvailableMiniGames();
        $grades = Grade::with('sections')->orderBy('name')->get();
        $assignments = MinigameAssignment::query()
            ->where('teacher_id', (int) Auth::id())
            ->with(['game:id,name,slug,image', 'grade:id,name', 'section:id,name'])
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.mini-games.index', compact('games', 'grades', 'assignments'));
    }

    public function playMiniGame(string $slug) {
        return redirect()->route('teacher.mini-games')
            ->with('info', 'Teachers cannot play mini games. You can assign and monitor them for students.');
    }

    public function generateMiniGameParagraph(Request $request, string $slug)
    {
        $game = MinigameSetting::query()->where('slug', $slug)->where('is_enabled', true)->firstOrFail();

        $validated = $request->validate([
            'topic' => 'nullable|string|max:255',
            'sentences' => 'nullable|integer|min:3|max:15',
        ]);

        $topic = trim((string) ($validated['topic'] ?? 'ICT and digital literacy'));
        $sentences = (int) ($validated['sentences'] ?? 8);
        $apiKey = (string) env('GROQ_API_KEY');

        if ($apiKey === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'AI is not configured. Please set GROQ_API_KEY first.',
            ], 503);
        }

        $prompt = "Create one classroom typing paragraph for the mini game '{$game->name}'. ".
            "Theme/topic: {$topic}. ".
            "Write approximately {$sentences} sentences, clear for students, and focused on ICT development and cyber safety. ".
            "Target around " . ($sentences * 18) . "-" . ($sentences * 24) . " words. ".
            "Return only the paragraph text with no quotes.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You generate educational typing paragraphs.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.8,
                'max_tokens' => 420,
                'stream' => false,
            ]);

            if ($response->failed()) {
                Log::error('Mini game paragraph AI failed', ['body' => $response->body()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'AI generation failed. Please try again.',
                ], 500);
            }

            $paragraph = trim((string) ($response->json('choices.0.message.content') ?? ''));
            $paragraph = trim($paragraph, "\"' \n\r\t");

            return response()->json([
                'status' => 'success',
                'paragraph' => $paragraph,
            ]);
        } catch (\Throwable $e) {
            Log::error('Mini game paragraph AI exception', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'AI generation failed. Please try again.',
            ], 500);
        }
    }

    public function assignMiniGame(Request $request, string $slug)
    {
        $game = MinigameSetting::query()->where('slug', $slug)->where('is_enabled', true)->firstOrFail();

        $validated = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($q) => $q->where('grade_id', $request->input('grade_id'))),
            ],
            'paragraph' => 'required|string|min:20|max:2500',
        ]);

        MinigameAssignment::create([
            'minigame_setting_id' => $game->id,
            'teacher_id' => (int) Auth::id(),
            'grade_id' => (int) $validated['grade_id'],
            'section_id' => (int) $validated['section_id'],
            'paragraph' => $validated['paragraph'],
            'is_active' => true,
            'starts_at' => now(),
        ]);

        return redirect()->route('teacher.mini-games')
            ->with('success', "{$game->name} has been assigned to the selected grade and section.");
    }

    public function gamification() {
        return view('teacher.gamification');
    }

    public function aiTrack() {
        return view('teacher.ai-track');
    }

    public function performance() {
        $teacherId = Auth::id();

        $students = User::where('role', 'student')->registeredByTeacher($teacherId)->get();

        $quizStats = QuizAttempt::select(
            'quiz_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as total_attempts'),
            DB::raw('MAX(score) as highest_score'),
            DB::raw('MIN(score) as lowest_score')
        )
            ->whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))
            ->groupBy('quiz_id')
            ->with('quiz')
            ->get();

        $studentRankings = QuizAttempt::select(
            'student_id',
            DB::raw('AVG(score) as average_score'),
            DB::raw('COUNT(*) as quizzes_taken'),
            DB::raw('SUM(xp_earned) as total_xp')
        )
            ->whereHas('student', fn ($q) => $q->where('registered_by_teacher_id', $teacherId))
            ->groupBy('student_id')
            ->with('student')
            ->orderBy('average_score', 'desc')
            ->get();

        $recentAttempts = QuizAttempt::with(['student', 'quiz'])
            ->whereHas('student', fn ($q) => $q->where('registered_by_teacher_id', $teacherId))
            ->latest()
            ->take(10)
            ->get();

        $overallStats = [
            'total_students' => User::where('role', 'student')->registeredByTeacher($teacherId)->count(),
            'total_quizzes' => Quiz::where('teacher_id', $teacherId)->count(),
            'total_lessons' => Lesson::where('status', 'approved')->where('teacher_id', $teacherId)->count(),
            'total_attempts' => QuizAttempt::whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))->count(),
            'class_average' => QuizAttempt::whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))->avg('score') ?? 0,
        ];

        return view('teacher.performance', compact('students', 'quizStats', 'studentRankings', 'recentAttempts', 'overallStats'));
    }

    public function feedback() {
        $teacherId = (int) Auth::id();

        $students = User::where('role', 'student')
            ->registeredByTeacher($teacherId)
            ->with(['quizAttempts' => function ($query) use ($teacherId) {
                $query->whereHas('quiz', fn ($q) => $q->where('teacher_id', $teacherId))
                    ->with('quiz')
                    ->latest();
            }])
            ->get()
            ->map(function($student) {
                $attempts = $student->quizAttempts;
                $student->average_score = $attempts->avg('score') ?? 0;
                $student->quizzes_taken = $attempts->count();
                $student->total_xp = $attempts->sum('xp_earned');
                $student->last_attempt = $attempts->first();
                return $student;
            })
            ->sortByDesc('average_score');
            
        return view('teacher.feedback', compact('students'));
    }

    public function sendFeedback(Request $request) {
        $request->validate([
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'student')->where('registered_by_teacher_id', Auth::id());
                }),
            ],
            'type' => 'required|in:praise,improvement,concern',
            'message' => 'required|string|min:10|max:1000',
        ]);

        StudentFeedback::create([
            'teacher_id' => (int) Auth::id(),
            'student_id' => (int) $request->input('student_id'),
            'type' => $request->input('type'),
            'message' => $request->input('message'),
        ]);

        return redirect()->route('teacher.feedback')
            ->with('success', 'Feedback sent successfully to the student. They can read it under Feedback in their account.');
    }

    public function reports() {
        return view('teacher.reports');
    }

    public function aiSupport() {
        return view('teacher.ai-support.index');
    }

    public function contentReview() {
        return view('teacher.content-review');
    }

    public function quest()
    {
        // Add your logic for the quest page here.
        return view('teacher.quest');  // Make sure the view exists.
    }

    private function getAvailableMiniGames(): array
    {
        return MinigameSetting::query()
            ->where('is_enabled', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (MinigameSetting $game) {
                return [
                    $game->slug => [
                        'slug' => $game->slug,
                        'name' => $game->name,
                        'image' => $game->image,
                        'type' => $game->type,
                        'mechanics' => $game->mechanics,
                        'gamification' => $game->gamification,
                        'best_for' => $game->best_for,
                        'enabled' => $game->is_enabled,
                    ],
                ];
            })
            ->toArray();
    }
}
