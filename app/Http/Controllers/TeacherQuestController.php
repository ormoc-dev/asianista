<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Quest;
use App\Models\QuestQuestion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TeacherQuestController extends Controller
{
    public function index()
    {
        $quests = Quest::with(['grade', 'section'])->latest()->get();
        return view('teacher.quest.index', compact('quests'));
    }

    public function show(Quest $quest)
    {
        $quest->load(['questions', 'grade', 'section']);
        return view('teacher.quest.show', compact('quest'));
    }
    public function create()
{
    $grades = Grade::with('sections')->get();
    return view('teacher.quest.create', compact('grades'));
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'xp_reward' => 'nullable|integer',
            'ab_reward' => 'nullable|integer',
            'gp_reward' => 'nullable|integer',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'hp_penalty' => 'nullable|integer|min:0',
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
                'level' => $request->level,
                'xp_reward' => $request->xp_reward,
                'ab_reward' => $request->ab_reward,
                'gp_reward' => $request->gp_reward,
                'time_limit_minutes' => $request->time_limit_minutes,
                'hp_penalty' => $request->hp_penalty ?? 10,
                'assign_date' => $request->assign_date,
                'due_date' => $request->due_date,
                'grade_id' => $request->grade_id,
                'section_id' => $request->section_id,
                // Uncomment when teacher auth is fully ready: 'teacher_id' => auth()->id(),
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

            return redirect()->route('teacher.quest.index')
                ->with('success', 'Quest created successfully!');

        } catch (\Exception $e) {
            Log::error('Quest Creation Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to forge the quest. Please try again.');
        }
    }
}
