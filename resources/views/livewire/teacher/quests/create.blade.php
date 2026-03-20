<div class="quest-forge-container" x-data="{ showAiModal: false, aiTopicText: '', isForging: false }" 
     @close-ai-modal.window="showAiModal = false; isForging = false;"
     x-init="
        Livewire.on('forgeComplete', () => { isForging = false; showAiModal = false; });
        Livewire.on('forgeFailed', () => { isForging = false; });
     ">
    <style>
        /* Hide elements with x-cloak until Alpine loads */
        [x-cloak] { display: none !important; }
        
        /* Re-using and adapting the premium styles from the legacy view */
        .quest-steps {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .quest-step {
            flex: 1;
            padding: 12px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        .quest-step-active {
            background: #eef2ff;
            color: #4f46e5;
            border-color: #4f46e5;
        }
        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #cbd5e1;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .quest-step-active .step-number {
            background: #4f46e5;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #4f46e5;
            outline: none;
        }
        
        .reward-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .reward-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .reward-icon { font-size: 1.5rem; margin-bottom: 8px; }
        .xp-icon { color: #4f46e5; }
        .ab-icon { color: #10b981; }
        .gp-icon { color: #f59e0b; }
        
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }
        .footer-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .level-tracker {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .level-dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 700;
            border: 2px solid #e2e8f0;
        }
        .level-dot.active {
            background: #eef2ff;
            color: #4f46e5;
            border-color: #4f46e5;
        }

        .question-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #4f46e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>

    <div class="header" style="margin-bottom: 30px;">
        <h1 style="color: var(--primary); font-size: 2rem; font-weight: 800;">🛠️ Quest Forge</h1>
        <p style="color: #64748b;">Design your quest details, challenges, and target audience.</p>
    </div>

    <!-- Step Indicator -->
    <div class="quest-steps">
        <div class="quest-step {{ $currentStep == 1 ? 'quest-step-active' : '' }}">
            <span class="step-number">1</span> Details
        </div>
        <div class="quest-step {{ $currentStep == 2 ? 'quest-step-active' : '' }}">
            <span class="step-number">2</span> Challenges
        </div>
        <div class="quest-step {{ $currentStep == 3 ? 'quest-step-active' : '' }}">
            <span class="step-number">3</span> Target
        </div>
    </div>

    @if($currentStep == 1)
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #1e3a8a;">📜 Quest Information</h2>
                <button type="button" @click="showAiModal = true" class="btn-primary" style="background: linear-gradient(135deg, #7c3aed, #4812e8);">
                    <i class="fas fa-magic"></i> Reforge with AI
                </button>
            </div>
            
            <div class="form-group">
                <label>Quest Title</label>
                <input type="text" wire:model.defer="title" class="form-control" placeholder="e.g., Dungeon of Division">
                @error('title') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea wire:model.defer="description" class="form-control" rows="4" placeholder="Describe the adventure..."></textarea>
                @error('description') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Difficulty</label>
                    <select wire:model.defer="difficulty" class="form-control">
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Total Levels (Stages)</label>
                    <input type="number" wire:model.defer="level" class="form-control" min="1" max="10">
                </div>
            </div>

            <div class="reward-grid">
                <div class="reward-card">
                    <div class="reward-icon xp-icon"><i class="fas fa-bolt"></i></div>
                    <label>XP Reward</label>
                    <input type="number" wire:model.defer="xp_reward" class="form-control" style="text-align: center;">
                </div>
                <div class="reward-card">
                    <div class="reward-icon ab-icon"><i class="fas fa-shield-alt"></i></div>
                    <label>AB Reward</label>
                    <input type="number" wire:model.defer="ab_reward" class="form-control" style="text-align: center;">
                </div>
                <div class="reward-card">
                    <div class="reward-icon gp-icon"><i class="fas fa-coins"></i></div>
                    <label>GP Reward</label>
                    <input type="number" wire:model.defer="gp_reward" class="form-control" style="text-align: center;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="form-group">
                    <label>Assign Date</label>
                    <input type="datetime-local" wire:model.defer="assign_date" class="form-control">
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="datetime-local" wire:model.defer="due_date" class="form-control">
                    @error('due_date') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="footer-buttons" style="justify-content: flex-end;">
                <button wire:click="nextStep" class="btn-primary">Next: Add Challenges <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
    @endif

    @if($currentStep == 2)
        <div class="form-card" style="margin-bottom: 30px;">
            <h2 style="margin-bottom: 20px; color: #1e3a8a;">🧩 Quest Challenges</h2>
            
            <!-- Progression Tracker -->
            <div class="level-tracker">
                @for($i = 1; $i <= $level; $i++)
                    <div class="level-dot {{ collect($questions)->contains('level', $i) ? 'active' : '' }}">
                        {{ $i }}
                    </div>
                @endfor
            </div>

            <!-- AI QUESTION FORGE Section -->
            <div style="background: linear-gradient(135deg, #f5f3ff, #ede9fe); padding: 20px; border-radius: 16px; border: 1px solid #c4b5fd; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; color: #5b21b6; font-weight: 800;">
                    <i class="fas fa-wand-magic-sparkles"></i> AI Question Forge
                </div>
                <div style="display: flex; gap: 10px;">
                    <input type="text" wire:model.defer="aiQuestionTopic" placeholder="Question topic (e.g. Photosynthesis, Algebra)..." class="form-control" style="flex: 1;">
                    <button type="button" wire:click="generateSingleQuestionWithAI" wire:loading.attr="disabled" class="btn-primary" style="background: #7c3aed;">
                        <span wire:loading.remove wire:target="generateSingleQuestionWithAI"><i class="fas fa-sparkles"></i> Forge</span>
                        <span wire:loading wire:target="generateSingleQuestionWithAI"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
                @error('aiQuestionTopic') <span style="color:red; font-size:0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
            </div>

            <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add Question</h3>
                
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea wire:model.defer="questionText" class="form-control" rows="2"></textarea>
                    @error('questionText') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Question Type</label>
                        <select wire:model="questionType" class="form-control">
                            <option value="">Select Type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="identification">Identification</option>
                        </select>
                        @error('questionType') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Target Level</label>
                        <select wire:model.defer="questionLevel" class="form-control">
                            @for($i = 1; $i <= $level; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                @if($questionType == 'multiple_choice')
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 700; display: block; margin-bottom: 10px;">Options</label>
                        @foreach($options as $index => $option)
                            <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                <input type="radio" wire:model.defer="correctAnswer" value="{{ $index }}" name="correct">
                                <input type="text" wire:model.defer="options.{{ $index }}" class="form-control" placeholder="Option {{ $index+1 }}">
                                <button wire:click="removeOption({{ $index }})" class="btn-secondary" style="padding: 10px;"><i class="fas fa-trash"></i></button>
                            </div>
                        @endforeach
                        <button wire:click="addOption" class="btn-secondary" style="width: 100%; border: 1px dashed #cbd5e1;"><i class="fas fa-plus"></i> Add Option</button>
                        @error('options') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                        @error('correctAnswer') <span style="color:red; font-size:0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                    </div>
                @elseif($questionType == 'identification')
                    <div class="form-group">
                        <label>Correct Answer</label>
                        <input type="text" wire:model.defer="correctAnswer" class="form-control">
                        @error('correctAnswer') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label>Points</label>
                    <input type="number" wire:model.defer="questionPoints" class="form-control">
                </div>

                <button wire:click="addQuestion" class="btn-primary" style="width: 100%;">
                    {{ $editingIndex !== null ? 'Update Question' : '+ Add Question to Quest' }}
                </button>
            </div>
        </div>

        @if(!empty($questions))
            <div class="form-card">
                <h3 style="margin-bottom: 20px;">Added Questions ({{ count($questions) }})</h3>
                <div class="added-questions-list">
                    @foreach($questions as $index => $q)
                        <div class="question-item">
                            <div>
                                <span style="font-weight: 800; color: #4f46e5;">Level {{ $q['level'] }}:</span> 
                                <span style="color: #1e293b; font-weight: 600;">{{ Str::limit($q['text'], 50) }}</span>
                                <div style="font-size: 0.8rem; color: #64748b;">{{ ucfirst($q['type']) }} • {{ $q['points'] }} Points</div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button wire:click="editQuestion({{ $index }})" style="color: #4f46e5; background: none; border: none; cursor: pointer;"><i class="fas fa-edit"></i></button>
                                <button wire:click="removeQuestion({{ $index }})" style="color: #ef4444; background: none; border: none; cursor: pointer;"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('questions') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="footer-buttons">
            <button wire:click="previousStep" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to Details</button>
            <button wire:click="nextStep" class="btn-primary">Next: Target Party <i class="fas fa-users"></i></button>
        </div>
    @endif

    @if($currentStep == 3)
        <div class="form-card">
            <h2 style="margin-bottom: 20px; color: #1e3a8a;">👥 Target Party</h2>
            <p style="color: #64748b; margin-bottom: 30px;">Choose which grade and section will receive this quest.</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label><i class="fas fa-layer-group"></i> Grade</label>
                    <select wire:model="grade_id" class="form-control">
                        <option value="">Select Grade</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label><i class="fas fa-users-class"></i> Section</label>
                    <select wire:model="section_id" class="form-control" {{ empty($sections) ? 'disabled' : '' }}>
                        <option value="">Select Section</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                    @error('section_id') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="background: #fff9db; border-radius: 12px; padding: 15px; margin-top: 20px; display: flex; gap: 10px; align-items: center; color: #856404; font-size: 0.9rem;">
                <i class="fas fa-lightbulb"></i>
                You can reuse this quest later by assigning it to different sections or grades.
            </div>

            @error('save') <div style="color:red; margin-top:20px; font-weight:700;">{{ $message }}</div> @enderror

            <div class="footer-buttons">
                <button wire:click="previousStep" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to Challenges</button>
                <button wire:click="saveQuest" class="btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-check-circle"></i> Create Quest
                </button>
            </div>
        </div>
    @endif

    <!-- AI Reforge Modal (Alpine.js for UI, Livewire for AI generation) -->
    <div x-show="showAiModal"
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-blur" 
         style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 20px;"
         @click.self="showAiModal = false">
        <div class="modal-box" style="background: radial-gradient(circle at top right, #0f172a, #0b1121); width: 100%; max-width: 550px; border-radius: 24px; border: 1px solid rgba(96, 165, 250, 0.2); padding: 40px; color: white;">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; box-shadow: 0 0 25px rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.6rem; font-weight: 800; margin: 0; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Neural Quest Forge</h3>
                        <p style="font-size: 0.9rem; color: #64748b; margin: 5px 0 0;">Consult the elders to weave a new adventure.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label style="color: #60a5fa; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Quest Topic / Source Material</label>
                    <textarea x-model="aiTopicText" class="form-control" style="background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.1); height: 120px;" placeholder="e.g., The secret history of the Phoenix, or an introduction to Algebra spells..."></textarea>
                    @error('aiTopic') <span style="color:#f87171; font-size:0.8rem; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="button" @click="showAiModal = false" class="btn-secondary" style="flex: 1; background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">Dismiss</button>
                    <button type="button" 
                            :disabled="isForging"
                            @click="
                                if (!aiTopicText.trim()) {
                                    alert('Please enter a topic for the forge.');
                                    return;
                                }
                                isForging = true;
                                $wire.set('aiTopic', aiTopicText).then(() => {
                                    $wire.call('generateWithAI');
                                });
                            "
                            class="btn-primary" 
                            style="flex: 2; background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <span x-show="!isForging"><i class="fas fa-sparkles"></i> Forge Content</span>
                        <span x-show="isForging"><i class="fas fa-spinner fa-spin"></i> Synchronizing...</span>
                    </button>
                </div>
            </div>
    </div>
</div>
