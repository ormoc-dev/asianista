<?php

namespace App\Http\Livewire\Teacher\Quests;

use Livewire\Component;
use App\Models\Quest;
use App\Models\QuestQuestion;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $currentStep = 1;

    // Step 1: Quest Details
    public $title;
    public $description;
    public $difficulty = 'medium';
    public $level = 3;
    public $xp_reward = 100;
    public $ab_reward = 50;
    public $gp_reward = 25;
    public $assign_date;
    public $due_date;

    // Step 2: Challenges
    public $questions = [];
    public $questionText;
    public $questionType;
    public $options = [];
    public $correctAnswer;
    public $questionPoints = 10;
    public $questionLevel = 1;
    public $editingIndex = null;

    // Step 3: Target Party
    public $grade_id;
    public $section_id;

    // AI Forge
    public $aiTopic;
    public $aiQuestionTopic;
    public $isAiLoading = false;
    public $isSingleAiLoading = false;

    public function generateWithAI()
    {
        if (empty($this->aiTopic)) {
            $this->addError('aiTopic', 'Please enter a topic for the forge.');
            $this->emit('forgeFailed');
            return;
        }

        $this->isAiLoading = true;

        try {
            $controller = new \App\Http\Controllers\AIAssistantController();
            
            $request = new \Illuminate\Http\Request([
                'topic' => $this->aiTopic,
                'difficulty' => $this->difficulty,
                'total_levels' => $this->level
            ]);

            $response = $controller->generateQuest($request);
            $result = $response->getData(true);

            if ($result['status'] === 'success') {
                $data = $result['data'];
                $this->title = $data['title'];
                $this->description = $data['description'];
                $this->xp_reward = $data['xp_reward'];
                $this->ab_reward = $data['ab_reward'];
                $this->gp_reward = $data['gp_reward'];
                
                $this->aiQuestionTopic = $this->aiTopic;

                $this->questions = collect($data['challenges'])->map(function($c, $i) {
                    return [
                        'text' => $c['text'],
                        'type' => $c['type'],
                        'level' => $c['level'] ?? ($i + 1),
                        'points' => $c['points'],
                        'answer' => $c['answer'],
                        'options' => $c['options'] ?? null,
                    ];
                })->toArray();

                // Emit success event for Alpine.js
                $this->emit('forgeComplete');
                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'success',
                    'title' => 'Quest Reforged!',
                    'text' => 'The Neural Link has woven a new adventure for you.',
                ]);
            } else {
                $this->emit('forgeFailed');
            }
        } catch (\Exception $e) {
            $this->addError('aiTopic', 'The Neural Link was interrupted: ' . $e->getMessage());
            $this->emit('forgeFailed');
        } finally {
            $this->isAiLoading = false;
        }
    }

    public function generateSingleQuestionWithAI()
    {
        $topic = $this->aiQuestionTopic ?: $this->title;
        
        if (empty($topic)) {
            $this->addError('aiQuestionTopic', 'Please enter a topic or quest title.');
            return;
        }

        if (empty($this->questionType)) {
            $this->addError('questionType', 'Please select a question type first.');
            return;
        }

        $this->isSingleAiLoading = true;

        try {
            $controller = new \App\Http\Controllers\AIAssistantController();
            $request = new \Illuminate\Http\Request([
                'topic' => $topic,
                'type' => $this->questionType,
                'difficulty' => $this->difficulty
            ]);

            $response = $controller->generateQuestion($request);
            $result = $response->getData(true);

            if ($result['status'] === 'success') {
                $data = $result['data'];
                $this->questionText = $data['text'];
                $this->questionPoints = $data['points'];
                $this->questionLevel = $data['level'] ?? 1;

                if ($this->questionType === 'multiple_choice') {
                    $this->options = $data['options'];
                    $this->correctAnswer = array_search($data['answer'], $this->options);
                } else {
                    $this->correctAnswer = $data['answer'];
                }

                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'success',
                    'title' => 'Challenge Forged!',
                    'text' => 'A new puzzle has been created.',
                ]);
            }
        } catch (\Exception $e) {
            $this->addError('aiQuestionTopic', 'The Neural Realm is unresponsive.');
        } finally {
            $this->isSingleAiLoading = false;
        }
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'difficulty' => 'nullable|string',
        'level' => 'required|integer|min:1',
        'xp_reward' => 'nullable|integer',
        'ab_reward' => 'nullable|integer',
        'gp_reward' => 'nullable|integer',
        'assign_date' => 'required|date',
        'due_date' => 'required|date|after:assign_date',
    ];

    public function mount()
    {
        $this->assign_date = now()->format('Y-m-d\TH:i');
        $this->due_date = now()->addDays(7)->format('Y-m-d\TH:i');
    }

    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->validate();
        } elseif ($this->currentStep === 2) {
            if (empty($this->questions)) {
                $this->addError('questions', 'Please add at least one question to the quest.');
                return;
            }
        }
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function addOption()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function addQuestion()
    {
        $this->validate([
            'questionText' => 'required|string',
            'questionType' => 'required|string|in:multiple_choice,identification',
            'questionPoints' => 'required|integer|min:1',
            'questionLevel' => 'required|integer|min:1',
        ]);

        if ($this->questionType === 'multiple_choice') {
            if (count($this->options) < 2) {
                $this->addError('options', 'Multiple choice questions must have at least 2 options.');
                return;
            }
            if ($this->correctAnswer === null || !isset($this->options[$this->correctAnswer])) {
                $this->addError('correctAnswer', 'Please select the correct answer.');
                return;
            }
        } else {
            if (empty($this->correctAnswer)) {
                $this->addError('correctAnswer', 'Please provide the correct answer.');
                return;
            }
        }

        $questionData = [
            'text' => $this->questionText,
            'type' => $this->questionType,
            'points' => $this->questionPoints,
            'level' => $this->questionLevel,
            'answer' => $this->questionType === 'multiple_choice' ? $this->options[$this->correctAnswer] : $this->correctAnswer,
            'options' => $this->questionType === 'multiple_choice' ? $this->options : null,
        ];

        if ($this->editingIndex !== null) {
            $this->questions[$this->editingIndex] = $questionData;
            $this->editingIndex = null;
        } else {
            $this->questions[] = $questionData;
        }

        $this->resetQuestionFields();
    }

    public function editQuestion($index)
    {
        $q = $this->questions[$index];
        $this->questionText = $q['text'];
        $this->questionType = $q['type'];
        $this->questionPoints = $q['points'];
        $this->questionLevel = $q['level'];
        $this->options = $q['options'] ?? [];
        
        if ($q['type'] === 'multiple_choice') {
            $this->correctAnswer = array_search($q['answer'], $this->options);
        } else {
            $this->correctAnswer = $q['answer'];
        }

        $this->editingIndex = $index;
    }

    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function resetQuestionFields()
    {
        $this->questionText = '';
        $this->questionType = '';
        $this->options = [];
        $this->correctAnswer = null;
        $this->questionPoints = 10;
        $this->questionLevel = 1;
        $this->editingIndex = null;
    }

    public function saveQuest()
    {
        $this->validate([
            'grade_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $quest = Quest::create([
                'title' => $this->title,
                'description' => $this->description,
                'difficulty' => $this->difficulty,
                'level' => $this->level,
                'xp_reward' => $this->xp_reward,
                'ab_reward' => $this->ab_reward,
                'gp_reward' => $this->gp_reward,
                'assign_date' => $this->assign_date,
                'due_date' => $this->due_date,
                'grade_id' => $this->grade_id,
                'section_id' => $this->section_id,
                // 'teacher_id' => auth()->id(), // Uncomment when auth is ready
            ]);

            foreach ($this->questions as $q) {
                QuestQuestion::create([
                    'quest_id' => $quest->id,
                    'question' => $q['text'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'level' => $q['level'],
                    'options' => $q['options'],
                    'answer' => $q['answer'],
                ]);
            }

            DB::commit();

            session()->flash('success', '✨ Quest forged successfully! Adventure awaits!');
            return redirect()->route('teacher.quest');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'Failed to forge quest: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.teacher.quests.create', [
            'grades' => Grade::with('sections')->get(),
            'sections' => $this->grade_id ? Section::where('grade_id', $this->grade_id)->get() : [],
        ])->layout('livewire.teacher.app-layout');
    }
}
