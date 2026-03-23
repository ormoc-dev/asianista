<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Quest;
use App\Models\QuestQuestion;
use App\Models\QuestAttempt;
use App\Models\QuestAttemptPower;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentQuestController extends Controller
{
    public function index()
    {
        // For now, fetch all quests until student-grade linking is implemented
        $quests = Quest::with(['grade', 'section', 'attempts' => function($query) {
            $query->where('user_id', Auth::id());
        }])->latest()->get();
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

        $attempt = QuestAttempt::where('user_id', Auth::id())
                                ->where('quest_id', $quest->id)
                                ->first();

        // Check if quest is expired and not already completed
        if ($quest->due_date && \Carbon\Carbon::parse($quest->due_date)->isPast()) {
            if (!$attempt || $attempt->status !== 'completed') {
                return back()->with('error', 'This quest has already expired, Hero. Better luck next time!');
            }
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
                                ->with('usedPowers')
                                ->first();

        if (!$attempt) {
            return redirect()->route('student.quest.show', $quest->id);
        }

        // Check if expired during play (only if not already completed)
        if ($quest->due_date && \Carbon\Carbon::parse($quest->due_date)->isPast() && $attempt->status !== 'completed') {
            return redirect()->route('student.quest.show', $quest->id)->with('error', 'The deadline has passed! You can no longer continue this mission.');
        }

        // If no question provided, load the current one from attempt
        if (!$question) {
            $question = QuestQuestion::find($attempt->current_question_id);
        }

        // Ensure user can only play their current or previous questions (no skipping)
        // Simplified for now: just load the question
        return view('student.quest.play', compact('quest', 'question', 'attempt'));
    }

    public function usePower(Request $request, Quest $quest, QuestAttempt $attempt)
    {
        // Verify the attempt belongs to the current user
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $powerName = $request->input('power_name');
        $level = $request->input('level', 1);

        // Check if power already used for this level
        if ($attempt->hasUsedPower($powerName, $level)) {
            return response()->json(['error' => 'Power already used for this level'], 400);
        }

        // Record power usage
        $attempt->usePower($powerName, $level);

        return response()->json(['success' => true, 'message' => 'Power activated!']);
    }

    public function submitStep(Request $request, Quest $quest, QuestQuestion $question)
    {
        // Final security check: Is the quest expired?
        if ($quest->due_date && \Carbon\Carbon::parse($quest->due_date)->isPast()) {
            $attempt = QuestAttempt::where('user_id', Auth::id())
                                    ->where('quest_id', $quest->id)
                                    ->first();

            if (!$attempt || $attempt->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'The Sands of Time have run out! This quest is expired.'
                ], 403);
            }
        }

        $isCorrect = false;
        if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $isCorrect = ($request->answer === $question->answer);
        } else {
            // Text based answer (basic comparison)
            $isCorrect = (strtolower(trim($request->answer)) === strtolower(trim($question->answer)));
        }

        $user = Auth::user();
        $attempt = QuestAttempt::where('user_id', Auth::id())
                                ->where('quest_id', $quest->id)
                                ->first();
        
        $activePower = $request->input('active_power');
        $hpPenalty = $quest->hp_penalty ?? 10;

        if ($isCorrect) {
            // Restore HP on correct answer (regenerate based on character class)
            $characterData = $user->getCharacterData();
            $maxHP = 100; // Default max HP
            $restoreAmount = ceil($maxHP * 0.2); // Restore 20% of max HP
            $newHP = min($maxHP, $user->hp + $restoreAmount);
            $user->update(['hp' => $newHP]);
            
            // Find next question based on the standardized order (level, then id)
            $nextQuestion = $quest->questions()
                                 ->where(function($q) use ($question) {
                                     $q->where('level', '>', $question->level)
                                       ->orWhere(function($sq) use ($question) {
                                           $sq->where('level', $question->level)
                                              ->where('id', '>', $question->id);
                                       });
                                 })
                                 ->orderBy('level')
                                 ->orderBy('id')
                                 ->first();

            if ($nextQuestion) {
                $attempt->update(['current_question_id' => $nextQuestion->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Victory! You restored ' . $restoreAmount . ' HP! Moving to the next challenge.',
                    'next_url' => route('student.quest.play', [$quest->id, $nextQuestion->id]),
                    'new_hp' => $newHP
                ]);
            } else {
                $attempt->update(['status' => 'completed']);
                return response()->json([
                    'success' => true,
                    'message' => 'Quest Complete! You have conquered the Neural Realm.',
                    'next_url' => route('student.quest.show', $quest->id),
                    'new_hp' => $newHP
                ]);
            }
        }

        // Wrong answer - deduct HP unless protected by power
        $newHP = $user->hp;
        $hpDeducted = false;
        
        // Check if protected by Shield Guard, Revive, or Focus Aura
        $isProtected = in_array($activePower, ['shield', 'revive', 'focus']);
        
        if (!$isProtected) {
            $newHP = max(0, $user->hp - $hpPenalty);
            $user->update(['hp' => $newHP]);
            $hpDeducted = true;
        }

        return response()->json([
            'success' => false,
            'message' => $hpDeducted ? "Not quite right. You lost {$hpPenalty} HP!" : 'Not quite right. Try again, Hero!',
            'new_hp' => $newHP
        ]);
    }

    public function timeOut(Request $request, Quest $quest, QuestQuestion $question)
    {
        $user = Auth::user();
        $attempt = QuestAttempt::where('user_id', Auth::id())
                                ->where('quest_id', $quest->id)
                                ->first();

        if (!$attempt) {
            return response()->json(['error' => 'No active attempt'], 404);
        }

        // Deduct HP penalty for time-out (unless protected)
        $hpPenalty = $quest->hp_penalty ?? 10;
        $newHP = max(0, $user->hp - $hpPenalty);
        $user->update(['hp' => $newHP]);

        // Find next question in same level first
        $nextQuestion = $quest->questions()
                             ->where('level', $question->level)
                             ->where('id', '>', $question->id)
                             ->orderBy('id')
                             ->first();

        // If no more questions in same level, go to next level
        if (!$nextQuestion) {
            $nextQuestion = $quest->questions()
                                 ->where('level', '>', $question->level)
                                 ->orderBy('level')
                                 ->orderBy('id')
                                 ->first();
        }

        if ($nextQuestion) {
            $attempt->update(['current_question_id' => $nextQuestion->id]);
            return response()->json([
                'success' => true,
                'message' => "Time's up! You lost {$hpPenalty} HP. Moving to next challenge.",
                'next_url' => route('student.quest.play', [$quest->id, $nextQuestion->id]),
                'new_hp' => $newHP
            ]);
        } else {
            // No more questions - quest complete
            $attempt->update(['status' => 'completed']);
            return response()->json([
                'success' => true,
                'message' => "Time's up! Quest complete.",
                'next_url' => route('student.quest.show', $quest->id),
                'new_hp' => $newHP
            ]);
        }
    }
}
