<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\QuestQuestion;
use App\Models\QuestMapLayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TeacherQuestController extends Controller
{
    public function index()
    {
        $quests = Quest::query()
            ->ownedByTeacher((int) Auth::id())
            ->with(['grade', 'section'])
            ->latest()
            ->get();

        return view('teacher.quest.index', compact('quests'));
    }

    /**
     * Quests from other teachers (or legacy with no owner) available to copy into your account.
     */
    public function cloneLibrary()
    {
        $teacherId = (int) Auth::id();

        $quests = Quest::query()
            ->with(['grade', 'section', 'teacher'])
            ->where(function ($q) use ($teacherId) {
                $q->whereNull('teacher_id')
                    ->orWhere('teacher_id', '!=', $teacherId);
            })
            ->latest()
            ->get();

        return view('teacher.quest.clone-library', compact('quests'));
    }

    /**
     * Duplicate a quest (any source) into the current teacher's library.
     */
    public function cloneQuest(Quest $quest)
    {
        $teacherId = (int) Auth::id();

        try {
            $newQuest = DB::transaction(function () use ($quest, $teacherId) {
                $quest->load('questions');

                $mapPath = $this->replicateQuestMapImage($quest->map_image);

                $copy = Quest::create([
                    'title' => $this->uniqueCloneTitle($quest->title, $teacherId),
                    'description' => $quest->description,
                    'difficulty' => $quest->difficulty,
                    'map_image' => $mapPath,
                    'map_pins' => is_array($quest->map_pins) && count($quest->map_pins) > 0 ? $quest->map_pins : null,
                    'level' => $quest->level,
                    'xp_reward' => $quest->xp_reward,
                    'ab_reward' => $quest->ab_reward,
                    'gp_reward' => $quest->gp_reward,
                    'time_limit_minutes' => $quest->time_limit_minutes,
                    'hp_penalty' => $quest->hp_penalty ?? 10,
                'require_fullscreen' => (bool) $quest->require_fullscreen,
                    'assign_date' => $quest->assign_date,
                    'due_date' => $quest->due_date,
                    'grade_id' => $quest->grade_id,
                    'section_id' => $quest->section_id,
                    'teacher_id' => $teacherId,
                ]);

                foreach ($quest->questions as $q) {
                    QuestQuestion::create([
                        'quest_id' => $copy->id,
                        'question' => $q->question,
                        'type' => $q->type,
                        'points' => $q->points,
                        'level' => $q->level,
                        'options' => $q->options,
                        'answer' => $q->answer,
                    ]);
                }

                return $copy;
            });

            return redirect()
                ->route('teacher.quest.edit', $newQuest)
                ->with('success', 'Quest copied to your library. Review dates and details, then save.');
        } catch (\Throwable $e) {
            Log::error('Quest clone failed: '.$e->getMessage(), ['quest_id' => $quest->id]);

            return redirect()
                ->route('teacher.quest.clone-library')
                ->with('error', 'Could not clone this quest. Please try again.');
        }
    }

    public function show(Quest $quest)
    {
        $teacherId = (int) Auth::id();

        abort_unless((int) $quest->teacher_id === $teacherId, 403);

        $quest->load(['questions', 'grade', 'section']);

        $attempts = QuestAttempt::with(['user', 'currentQuestion'])
            ->where('quest_id', $quest->id)
            ->whereHas('user', fn ($q) => $q->where('registered_by_teacher_id', $teacherId))
            ->get();

        $isExpired = $quest->due_date ? now()->gt($quest->due_date) : false;

        // Same ordering as gameplay: level ASC, id ASC (see StudentQuestController).
        $orderedQuestions = $quest->questions->sortBy(fn ($q) => [$q->level, $q->id])->values();
        $indexByQuestionId = $orderedQuestions->pluck('id')->flip()->all();

        $studentsByQuestion = [];
        foreach ($orderedQuestions as $q) {
            $studentsByQuestion[$q->id] = [
                'passed' => [],
                'in_progress' => [],
                'failed' => [],
                'not_started' => [],
            ];
        }

        foreach ($attempts as $attempt) {
            if (! $attempt->user) {
                continue;
            }

            $studentName = trim((string) ($attempt->user->full_name ?? $attempt->user->name));
            $studentName = $studentName !== '' ? $studentName : 'Unknown Student';

            if ($attempt->status === 'completed') {
                foreach ($orderedQuestions as $q) {
                    $studentsByQuestion[$q->id]['passed'][] = $studentName;
                }

                continue;
            }

            if ($attempt->status !== 'started') {
                continue;
            }

            $curId = $attempt->current_question_id;
            $curIdx = ($curId !== null && isset($indexByQuestionId[$curId]))
                ? (int) $indexByQuestionId[$curId]
                : null;

            foreach ($orderedQuestions as $idx => $q) {
                $qid = $q->id;
                if ($curIdx === null) {
                    $studentsByQuestion[$qid][$isExpired ? 'failed' : 'not_started'][] = $studentName;

                    continue;
                }
                if ($curIdx > $idx) {
                    $studentsByQuestion[$qid]['passed'][] = $studentName;
                } elseif ($curIdx === $idx) {
                    $studentsByQuestion[$qid]['in_progress'][] = $studentName;
                } elseif ($isExpired) {
                    $studentsByQuestion[$qid]['failed'][] = $studentName;
                } else {
                    $studentsByQuestion[$qid]['not_started'][] = $studentName;
                }
            }
        }

        foreach ($studentsByQuestion as $qid => $buckets) {
            foreach ($buckets as $key => $names) {
                $studentsByQuestion[$qid][$key] = array_values(array_unique($names));
            }
        }

        return view('teacher.quest.show', compact('quest', 'studentsByQuestion'));
    }

    public function create()
    {
        $grades = Grade::with('sections')->get();
        return view('teacher.quest.create', compact('grades'));
    }

    public function edit(Quest $quest)
    {
        abort_unless((int) $quest->teacher_id === (int) Auth::id(), 403);

        $quest->load(['questions', 'grade', 'section']);
        $grades = Grade::with('sections')->get();

        return view('teacher.quest.create', compact('grades', 'quest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'nullable|string',
            'level' => 'required|integer|min:1|max:30',
            'map_pins_json' => 'nullable|string|max:65535',
            'xp_reward' => 'nullable|integer',
            'ab_reward' => 'nullable|integer',
            'gp_reward' => 'nullable|integer',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'hp_penalty' => 'nullable|integer|min:0',
            'require_fullscreen' => 'nullable|boolean',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after:assign_date',
            'grade_id' => 'required|integer',
            'section_id' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,identification',
            'questions.*.points' => 'required|integer',
            'questions.*.level' => 'required|integer|min:1',
            'questions.*.answer' => 'required|string',
            'map_image' => 'nullable|string',
        ]);

        try {
            $mapImagePath = null;
            
            // Handle map image upload (base64)
            if ($request->filled('map_image') && str_starts_with($request->map_image, 'data:image')) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->map_image));
                $fileName = 'quest_maps/' . uniqid() . '.png';
                Storage::disk('public')->put($fileName, $imageData);
                $mapImagePath = $fileName;
            } elseif ($request->filled('map_image') && $request->map_image === 'default') {
                $mapImagePath = 'quest_map_bg.png';
            }

            $quest = Quest::create([
                'title' => $request->title,
                'description' => $request->description,
                'difficulty' => $request->difficulty,
                'map_image' => $mapImagePath,
                'map_pins' => $this->normalizedMapPinsFromRequest($request),
                'level' => $request->level,
                'xp_reward' => $request->xp_reward,
                'ab_reward' => $request->ab_reward,
                'gp_reward' => $request->gp_reward,
                'time_limit_minutes' => $request->time_limit_minutes,
                'hp_penalty' => $request->hp_penalty ?? 10,
                'require_fullscreen' => $request->boolean('require_fullscreen'),
                'assign_date' => $request->assign_date,
                'due_date' => $request->due_date,
                'grade_id' => $request->grade_id,
                'section_id' => $request->section_id,
                'teacher_id' => Auth::id(),
            ]);

            foreach ($request->questions as $q) {
                QuestQuestion::create([
                    'quest_id' => $quest->id,
                    'question' => $q['text'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'level' => $q['level'],
                    'options' => isset($q['options']) ? $q['options'] : null,
                    'answer' => $q['answer'],
                ]);
            }

            return redirect()->route('teacher.quest')
                ->with('success', 'Quest created successfully!');

        } catch (\Exception $e) {
            Log::error('Quest Creation Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to forge the quest. Please try again.');
        }
    }

    public function update(Request $request, Quest $quest)
    {
        abort_unless((int) $quest->teacher_id === (int) Auth::id(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'nullable|string',
            'level' => 'required|integer|min:1|max:30',
            'map_pins_json' => 'nullable|string|max:65535',
            'xp_reward' => 'nullable|integer',
            'ab_reward' => 'nullable|integer',
            'gp_reward' => 'nullable|integer',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'hp_penalty' => 'nullable|integer|min:0',
            'require_fullscreen' => 'nullable|boolean',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after:assign_date',
            'grade_id' => 'required|integer',
            'section_id' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,identification',
            'questions.*.points' => 'required|integer',
            'questions.*.level' => 'required|integer|min:1',
            'questions.*.answer' => 'required|string',
            'map_image' => 'nullable|string',
        ]);

        try {
            $mapImagePath = $quest->map_image;

            if ($request->filled('map_image')) {
                if (str_starts_with($request->map_image, 'data:image')) {
                    if ($quest->map_image && str_starts_with((string) $quest->map_image, 'quest_maps/')) {
                        Storage::disk('public')->delete($quest->map_image);
                    }
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->map_image));
                    $fileName = 'quest_maps/' . uniqid() . '.png';
                    Storage::disk('public')->put($fileName, $imageData);
                    $mapImagePath = $fileName;
                } elseif ($request->map_image === 'default') {
                    if ($quest->map_image && str_starts_with((string) $quest->map_image, 'quest_maps/')) {
                        Storage::disk('public')->delete($quest->map_image);
                    }
                    $mapImagePath = 'quest_map_bg.png';
                } elseif (str_starts_with($request->map_image, 'existing:')) {
                    $mapImagePath = substr($request->map_image, strlen('existing:'));
                }
            }

            $quest->update([
                'title' => $request->title,
                'description' => $request->description,
                'difficulty' => $request->difficulty,
                'map_image' => $mapImagePath,
                'map_pins' => $this->normalizedMapPinsFromRequest($request),
                'level' => $request->level,
                'xp_reward' => $request->xp_reward,
                'ab_reward' => $request->ab_reward,
                'gp_reward' => $request->gp_reward,
                'time_limit_minutes' => $request->time_limit_minutes,
                'hp_penalty' => $request->hp_penalty ?? 10,
                'require_fullscreen' => $request->boolean('require_fullscreen'),
                'assign_date' => $request->assign_date,
                'due_date' => $request->due_date,
                'grade_id' => $request->grade_id,
                'section_id' => $request->section_id,
            ]);

            $keptQuestionIds = [];

            foreach ($request->questions as $q) {
                $options = $q['options'] ?? null;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : null;
                }

                if (! empty($q['id'])) {
                    $qq = QuestQuestion::where('quest_id', $quest->id)->where('id', $q['id'])->first();
                    if ($qq) {
                        $qq->update([
                            'question' => $q['text'],
                            'type' => $q['type'],
                            'points' => $q['points'],
                            'level' => $q['level'],
                            'options' => $options,
                            'answer' => $q['answer'],
                        ]);
                        $keptQuestionIds[] = (int) $qq->id;

                        continue;
                    }
                }

                $newQ = QuestQuestion::create([
                    'quest_id' => $quest->id,
                    'question' => $q['text'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'level' => $q['level'],
                    'options' => $options,
                    'answer' => $q['answer'],
                ]);
                $keptQuestionIds[] = (int) $newQ->id;
            }

            $removableIds = QuestQuestion::where('quest_id', $quest->id)
                ->whereNotIn('id', $keptQuestionIds)
                ->pluck('id');

            foreach ($removableIds as $rid) {
                if (! QuestAttempt::where('current_question_id', $rid)->exists()) {
                    QuestQuestion::where('id', $rid)->delete();
                }
            }

            return redirect()->route('teacher.quest')
                ->with('success', 'Quest updated successfully!');
        } catch (\Exception $e) {
            Log::error('Quest Update Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update the quest. Please try again.');
        }
    }

    protected function normalizedMapPinsFromRequest(Request $request): ?array
    {
        if (! $request->boolean('use_custom_map_pins')) {
            return null;
        }

        $raw = $request->input('map_pins_json');
        if (! is_string($raw)) {
            return null;
        }

        $raw = trim($raw);
        if ($raw === '' || $raw === '[]') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }

        $normalized = QuestMapLayout::normalizePinArray($decoded);

        return count($normalized) > 0 ? $normalized : null;
    }

    protected function replicateQuestMapImage(?string $mapImage): ?string
    {
        if (! $mapImage || $mapImage === 'quest_map_bg.png' || ! str_starts_with($mapImage, 'quest_maps/')) {
            return $mapImage;
        }

        if (! Storage::disk('public')->exists($mapImage)) {
            return $mapImage;
        }

        $newName = 'quest_maps/'.uniqid('copy_', true).'_'.basename($mapImage);
        Storage::disk('public')->copy($mapImage, $newName);

        return $newName;
    }

    protected function uniqueCloneTitle(string $baseTitle, int $teacherId): string
    {
        $title = $baseTitle.' (Copy)';
        $n = 2;
        while (Quest::query()->ownedByTeacher($teacherId)->where('title', $title)->exists()) {
            $title = $baseTitle.' (Copy '.$n.')';
            $n++;
        }

        return $title;
    }
}
