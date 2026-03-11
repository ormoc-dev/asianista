<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Quest;
use App\Models\QuestQuestion;
use App\Models\QuestAttempt;
use Illuminate\Support\Facades\Auth;

class StudentQuestController extends Controller
{
    public function index()
    {
        // For now, fetch all quests until student-grade linking is implemented
        $quests = Quest::with(['grade', 'section'])->latest()->get();
        return view('student.quest.index', compact('quests'));
    }

    public function show(Quest $quest)
    {
        $quest->load(['questions', 'grade', 'section']);
        $attempt = QuestAttempt::where('user_id', Auth::id())
                                ->where('quest_id', $quest->id)
                                ->first();

        return view('student.quest.show', compact('quest', 'attempt'));
    }

    public function start(Quest $quest)
    {
        $firstQuestion = $quest->questions()->orderBy('id')->first();

        if (!$firstQuestion) {
            return back()->with('error', 'This quest has no challenges yet.');
        }

        $attempt = QuestAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'quest_id' => $quest->id],
            ['current_question_id' => $firstQuestion->id, 'status' => 'started']
        );

        return redirect()->route('student.quest.play', [$quest->id, $attempt->current_question_id]);
    }

    public function play(Quest $quest, QuestQuestion $question = null)
    {
        $attempt = QuestAttempt::where('user_id', Auth::id())
                                ->where('quest_id', $quest->id)
                                ->first();

        if (!$attempt) {
            return redirect()->route('student.quest.show', $quest->id);
        }

        // If no question provided, load the current one from attempt
        if (!$question) {
            $question = QuestQuestion::find($attempt->current_question_id);
        }

        // Ensure user can only play their current or previous questions (no skipping)
        // Simplified for now: just load the question
        return view('student.quest.play', compact('quest', 'question', 'attempt'));
    }

    public function submitStep(Request $request, Quest $quest, QuestQuestion $question)
    {
        $isCorrect = false;
        if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $isCorrect = ($request->answer === $question->answer);
        } else {
            // Text based answer (basic comparison)
            $isCorrect = (strtolower(trim($request->answer)) === strtolower(trim($question->answer)));
        }

        if ($isCorrect) {
            $attempt = QuestAttempt::where('user_id', Auth::id())
                                    ->where('quest_id', $quest->id)
                                    ->first();
            
            // Find next question
            $nextQuestion = $quest->questions()
                                 ->where('id', '>', $question->id)
                                 ->orderBy('id')
                                 ->first();

            if ($nextQuestion) {
                $attempt->update(['current_question_id' => $nextQuestion->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Victory! Moving to the next challenge.',
                    'next_url' => route('student.quest.play', [$quest->id, $nextQuestion->id])
                ]);
            } else {
                $attempt->update(['status' => 'completed']);
                return response()->json([
                    'success' => true,
                    'message' => 'Quest Complete! You have conquered the Neural Realm.',
                    'next_url' => route('student.quest.show', $quest->id)
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Not quite right. Try again, Hero!'
        ]);
    }
}
