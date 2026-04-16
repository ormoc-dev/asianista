@extends('teacher.layouts.app')

@section('title', isset($quest) ? 'Edit Quest' : 'Create Quest')
@section('page-title', isset($quest) ? 'Edit Quest' : 'Create Quest')

@push('styles')
<style>
    .step-indicator {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }
    .step {
        flex: 1;
        padding: 12px 16px;
        background: var(--bg-main);
        border-radius: var(--radius-sm);
        text-align: center;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-secondary);
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .step.active {
        background: #eef2ff;
        color: var(--primary);
        border-color: var(--primary);
    }
    .step-number {
        display: inline-flex;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--border);
        color: var(--text-secondary);
        font-size: 0.8rem;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
    }
    .step.active .step-number {
        background: var(--primary);
        color: #fff;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .reward-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .reward-card {
        background: var(--bg-main);
        padding: 20px;
        border-radius: var(--radius-sm);
        text-align: center;
    }
    .reward-card i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    .question-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .question-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: var(--bg-main);
        border-radius: var(--radius-sm);
        border-left: 4px solid var(--primary);
    }
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
    }
    .map-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
        margin-top: 8px;
    }
    .map-option {
        position: relative;
        aspect-ratio: 16/10;
        border-radius: var(--radius-sm);
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.2s;
        background: var(--bg-main);
    }
    .map-option:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-lg);
    }
    .map-option.selected {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .map-option img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .map-option .map-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 8px 10px;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
    }
    .map-option .check-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: var(--primary);
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
    }
    .map-option.selected .check-icon {
        display: flex;
    }
    .map-upload-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--bg-main);
        border: 2px dashed var(--border);
        color: var(--text-secondary);
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .map-upload-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .map-upload-btn i {
        font-size: 1.5rem;
    }
    #mapUploadInput {
        display: none;
    }
</style>
@endpush

@section('content')
<form id="questForm" method="POST" action="{{ isset($quest) ? route('teacher.quest.update', $quest) : route('teacher.quest.store') }}">
    @csrf
    @if(isset($quest))
        @method('PUT')
    @endif
    
    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" data-step="1">
            <span class="step-number">1</span>Details
        </div>
        <div class="step" data-step="2">
            <span class="step-number">2</span>Challenges
        </div>
        <div class="step" data-step="3">
            <span class="step-number">3</span>Target
        </div>
    </div>

    <!-- Step 1: Quest Details -->
    <div class="step-content active" id="step1">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quest Information</h2>
                <button type="button" class="btn btn-secondary" onclick="openAiModal()">
                    <i class="fas fa-magic"></i> Generate with AI
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Quest Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., The Algebra Adventure" value="{{ old('title', $quest->title ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the quest adventure..." required>{{ old('description', $quest->description ?? '') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-control">
                            @php $diff = old('difficulty', $quest->difficulty ?? 'medium'); @endphp
                            <option value="easy" @selected($diff === 'easy')>Easy</option>
                            <option value="medium" @selected($diff === 'medium')>Medium</option>
                            <option value="hard" @selected($diff === 'hard')>Hard</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Levels</label>
                        <input type="number" name="level" class="form-control" value="{{ old('level', $quest->level ?? 3) }}" min="1" max="10">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-map"></i> Quest Map</label>
                    @php
                        $mapHidden = old('map_image');
                        if ($mapHidden === null) {
                            $mapHidden = (isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png')
                                ? 'existing:' . $quest->map_image
                                : 'default';
                        }
                    @endphp
                    <input type="hidden" name="map_image" id="mapImage" value="{{ $mapHidden }}">
                    <div class="map-selector" id="mapSelector">
                        <!-- Default Maps -->
                        <div class="map-option {{ (isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png') ? '' : 'selected' }}" data-map="default" onclick="selectMap(this, 'default')">
                            <img src="{{ asset('images/quest_map_bg.png') }}" alt="Default Map">
                            <span class="map-label">Default Map</span>
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                        </div>
                        @if(isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png')
                        <div class="map-option selected" data-map="existing" data-existing-path="{{ $quest->map_image }}" onclick="selectMapExisting(this)">
                            <img src="{{ asset('storage/' . $quest->map_image) }}" alt="Current Map">
                            <span class="map-label">Current Map</span>
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                        </div>
                        @endif
                        
                        <!-- Upload Button -->
                        <div class="map-option map-upload-btn" onclick="document.getElementById('mapUploadInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Upload Map</span>
                        </div>
                    </div>
                    <input type="file" id="mapUploadInput" accept="image/*" onchange="handleMapUpload(this)">
                    <small class="text-muted" style="display: block; margin-top: 8px; color: var(--text-muted);">
                        <i class="fas fa-info-circle"></i> Select a default map or upload your own (JPG, PNG, max 2MB)
                    </small>
                </div>

                <label class="form-label">Rewards</label>
                <div class="reward-grid">
                    <div class="reward-card">
                        <i class="fas fa-star" style="color: var(--primary);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">XP Reward</label>
                        <input type="number" name="xp_reward" class="form-control" value="{{ old('xp_reward', $quest->xp_reward ?? 100) }}">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">AP Reward</label>
                        <input type="number" name="ab_reward" class="form-control" value="{{ old('ab_reward', $quest->ab_reward ?? 50) }}">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-coins" style="color: var(--accent);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">GP Reward</label>
                        <input type="number" name="gp_reward" class="form-control" value="{{ old('gp_reward', $quest->gp_reward ?? 25) }}">
                    </div>
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Assign Date</label>
                        <input type="datetime-local" name="assign_date" class="form-control" value="{{ old('assign_date', isset($quest) ? \Carbon\Carbon::parse($quest->assign_date)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date', isset($quest) ? \Carbon\Carbon::parse($quest->due_date)->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <label class="form-label" style="margin-top: 20px;">Game Settings</label>
                <div class="reward-grid">
                    <div class="reward-card">
                        <i class="fas fa-clock" style="color: #3b82f6;"></i>
                        <label class="form-label" style="font-size: 0.8rem;">Time Limit (minutes)</label>
                        <input type="number" name="time_limit_minutes" class="form-control" value="{{ old('time_limit_minutes', $quest->time_limit_minutes ?? 10) }}" min="1" placeholder="Minutes per level">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-heart-broken" style="color: #ef4444;"></i>
                        <label class="form-label" style="font-size: 0.8rem;">HP Penalty</label>
                        <input type="number" name="hp_penalty" class="form-control" value="{{ old('hp_penalty', $quest->hp_penalty ?? 10) }}" min="0" placeholder="HP lost per wrong answer">
                    </div>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                Next: Add Challenges <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Challenges -->
    <div class="step-content" id="step2">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quest Challenges</h2>
                <button type="button" class="btn btn-secondary" onclick="openAiQuestionModal()">
                    <i class="fas fa-magic"></i> AI Forge
                </button>
            </div>
            <div class="card-body">
                <div id="questionsContainer" class="question-list">
                    <!-- Questions will be added here -->
                </div>

                <div style="background: var(--bg-main); padding: 20px; border-radius: var(--radius-sm); margin-top: 20px;">
                    <h3 style="font-size: 1rem; margin-bottom: 16px;"><i class="fas fa-plus"></i> Add Question</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Question Text</label>
                        <textarea id="questionText" class="form-control" rows="2" placeholder="Enter your question..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select id="questionType" class="form-control" onchange="toggleOptions()">
                                <option value="">Select Type</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="identification">Identification</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Level</label>
                            <select id="questionLevel" class="form-control">
                                <!-- Options will be populated dynamically based on total levels -->
                            </select>
                        </div>
                    </div>

                    <div id="optionsContainer" style="display: none;">
                        <label class="form-label">Options</label>
                        <div id="optionsList"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addOption()" style="margin-top: 8px;">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correct Answer</label>
                        <input type="text" id="correctAnswer" class="form-control" placeholder="Enter the correct answer">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Points</label>
                        <input type="number" id="questionPoints" class="form-control" value="10" style="width: 150px;">
                    </div>

                    <button type="button" class="btn btn-primary" onclick="addQuestion()">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                Next: Target Party <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Target -->
    <div class="step-content" id="step3">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Target Party</h2>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-graduation-cap"></i> Grade</label>
                        <select name="grade_id" id="gradeSelect" class="form-control" onchange="loadSections()" required>
                            <option value="">Select Grade</option>
                            @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', isset($quest) ? $quest->grade_id : null) == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-users"></i> Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>

                <div style="background: #fef3c7; padding: 16px; border-radius: var(--radius-sm); margin-top: 20px; color: #92400e;">
                    <i class="fas fa-lightbulb"></i>
                    You can reuse this quest later by assigning it to different sections or grades.
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check"></i> {{ isset($quest) ? 'Update Quest' : 'Create Quest' }}
            </button>
        </div>
    </div>
</form>

<!-- AI Modal -->
<div id="aiModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-magic"></i> AI Quest Generator</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Topic</label>
                <textarea id="aiTopic" class="form-control" rows="3" placeholder="e.g., Introduction to Algebra, Photosynthesis..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="closeAiModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateWithAI()">
                    <i class="fas fa-sparkles"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Question Forge Modal -->
<div id="aiQuestionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-magic"></i> AI Question Forge</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Topic/Subject</label>
                <input type="text" id="aiQuestionTopic" class="form-control" placeholder="e.g., Algebra, History, Science...">
            </div>
            <div class="form-group">
                <label class="form-label">Question Type</label>
                <select id="aiQuestionType" class="form-control">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="identification">Identification</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Target Level</label>
                <select id="aiQuestionLevel" class="form-control">
                    <!-- Populated dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Difficulty</label>
                <select id="aiQuestionDifficulty" class="form-control">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="closeAiQuestionModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateQuestionWithAI()">
                    <i class="fas fa-sparkles"></i> Generate Question
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    $questInitialQuestions = isset($quest)
        ? $quest->questions->map(fn ($q) => [
            'id' => $q->id,
            'text' => $q->question,
            'type' => $q->type,
            'level' => (int) $q->level,
            'answer' => $q->answer,
            'points' => (int) $q->points,
            'options' => $q->options,
        ])->values()->all()
        : [];
    $questIsEdit = isset($quest);
    $questInitialSectionId = $questIsEdit ? old('section_id', $quest->section_id) : null;
    $questScriptBootstrap = [
        'questions' => $questInitialQuestions,
        'isEdit' => $questIsEdit,
        'initialSectionId' => $questInitialSectionId,
    ];
@endphp
<script type="application/json" id="quest-bootstrap-data">
{!! json_encode($questScriptBootstrap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
const __questBoot = JSON.parse(document.getElementById('quest-bootstrap-data').textContent);
let questions = __questBoot.questions;
const questIsEdit = __questBoot.isEdit;
const questInitialSectionId = __questBoot.initialSectionId;
let options = [];
let currentStep = 1;
let uploadedMaps = [];

function selectMapExisting(element) {
    const path = element.getAttribute('data-existing-path');
    if (path) {
        selectMap(element, 'existing:' + path);
    }
}

function selectMap(element, mapValue) {
    document.querySelectorAll('.map-option').forEach(el => {
        if (!el.classList.contains('map-upload-btn')) {
            el.classList.remove('selected');
        }
    });
    element.classList.add('selected');
    document.getElementById('mapImage').value = mapValue;
}

function handleMapUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB');
        return;
    }
    
    // Create preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const mapId = 'custom_' + Date.now();
        uploadedMaps.push({ id: mapId, data: e.target.result });
        
        // Add new map option before upload button
        const selector = document.getElementById('mapSelector');
        const uploadBtn = selector.querySelector('.map-upload-btn');
        
        const newMap = document.createElement('div');
        newMap.className = 'map-option';
        newMap.setAttribute('data-map', mapId);
        newMap.onclick = function() { selectMap(this, mapId); };
        newMap.innerHTML = `
            <img src="${e.target.result}" alt="Custom Map">
            <span class="map-label">Custom Map ${uploadedMaps.length}</span>
            <span class="check-icon"><i class="fas fa-check"></i></span>
        `;
        
        selector.insertBefore(newMap, uploadBtn);
        
        // Select the newly uploaded map
        selectMap(newMap, mapId);
        
        // Store base64 in hidden input for submission
        document.getElementById('mapImage').value = e.target.result;
    };
    reader.readAsDataURL(file);
    
    // Reset input
    input.value = '';
}

function nextStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
    document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
    currentStep = step;
    
    // Update level dropdowns when entering step 2
    if (step === 2) {
        updateLevelDropdowns();
    }
}

function updateLevelDropdowns() {
    const totalLevels = parseInt(document.querySelector('input[name="level"]').value) || 3;
    const levelSelects = ['questionLevel', 'aiQuestionLevel'];
    
    levelSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            const currentValue = select.value;
            select.innerHTML = '';
            for (let i = 1; i <= totalLevels; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Level ${i}`;
                select.appendChild(option);
            }
            // Restore previous selection if valid
            if (currentValue && currentValue <= totalLevels) {
                select.value = currentValue;
            }
        }
    });
}

function openAiQuestionModal() {
    updateLevelDropdowns();
    document.getElementById('aiQuestionModal').style.display = 'flex';
}

function closeAiQuestionModal() {
    document.getElementById('aiQuestionModal').style.display = 'none';
}

async function generateQuestionWithAI() {
    const topic = document.getElementById('aiQuestionTopic').value;
    const type = document.getElementById('aiQuestionType').value;
    const level = document.getElementById('aiQuestionLevel').value;
    const difficulty = document.getElementById('aiQuestionDifficulty').value;
    
    if (!topic) {
        alert('Please enter a topic');
        return;
    }
    
    const btn = document.querySelector('#aiQuestionModal .btn-primary');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Forging...';
    
    try {
        const response = await fetch('{{ route("teacher.ai.generate-question") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ topic, type, difficulty })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Add the generated question
            const q = result.data;
            questions.push({
                text: q.text,
                type: q.type,
                level: parseInt(level),
                answer: q.answer,
                points: q.points || 10,
                options: q.type === 'multiple_choice' ? q.options : null
            });
            
            renderQuestions();
            closeAiQuestionModal();
            
            // Clear modal inputs
            document.getElementById('aiQuestionTopic').value = '';
            
            alert('Question forged successfully!');
        } else {
            alert('Failed to generate question: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('AI Question Error:', error);
        alert('Failed to connect to the Neural Forge');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function prevStep(step) {
    nextStep(step);
}

function toggleOptions() {
    const type = document.getElementById('questionType').value;
    const container = document.getElementById('optionsContainer');
    container.style.display = type === 'multiple_choice' ? 'block' : 'none';
}

function addOption() {
    const index = options.length;
    options.push('');
    renderOptions();
}

function renderOptions() {
    const container = document.getElementById('optionsList');
    container.innerHTML = options.map((opt, i) => `
        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <input type="text" class="form-control" value="${opt}" onchange="options[${i}] = this.value" placeholder="Option ${i + 1}">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(${i})"><i class="fas fa-trash"></i></button>
        </div>
    `).join('');
}

function removeOption(index) {
    options.splice(index, 1);
    renderOptions();
}

function addQuestion() {
    const text = document.getElementById('questionText').value;
    const type = document.getElementById('questionType').value;
    const level = document.getElementById('questionLevel').value;
    const answer = document.getElementById('correctAnswer').value;
    const points = document.getElementById('questionPoints').value;

    if (!text || !type || !answer) {
        alert('Please fill in all required fields');
        return;
    }

    questions.push({
        text,
        type,
        level,
        answer,
        points,
        options: type === 'multiple_choice' ? [...options] : null
    });

    renderQuestions();
    
    // Clear form
    document.getElementById('questionText').value = '';
    document.getElementById('correctAnswer').value = '';
    options = [];
    renderOptions();
}

function renderQuestions() {
    const container = document.getElementById('questionsContainer');
    container.innerHTML = questions.map((q, i) => {
        const preview = (q.text && q.text.length > 50) ? q.text.substring(0, 50) + '...' : (q.text || '');
        return `
        <div class="question-item">
            <div>
                <strong>Level ${q.level}:</strong> ${preview}
                <div style="font-size: 0.8rem; color: var(--text-muted);">${q.type} • ${q.points} pts</div>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(${i})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    }).join('');

    // Update hidden inputs for form submission
    updateHiddenInputs();
}

function removeQuestion(index) {
    questions.splice(index, 1);
    renderQuestions();
}

function updateHiddenInputs() {
    // Remove existing hidden inputs
    document.querySelectorAll('input[name^="questions"]').forEach(el => el.remove());
    
    // Add new hidden inputs
    const form = document.getElementById('questForm');
    questions.forEach((q, i) => {
        Object.keys(q).forEach(key => {
            if (q[key] === null || q[key] === undefined) {
                return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `questions[${i}][${key}]`;
            input.value = typeof q[key] === 'object' ? JSON.stringify(q[key]) : q[key];
            form.appendChild(input);
        });
    });
}

function loadSections() {
    const gradeId = document.getElementById('gradeSelect').value;
    const sectionSelect = document.getElementById('sectionSelect');
    
    console.log('Loading sections for grade:', gradeId);
    
    if (!gradeId) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        return Promise.resolve();
    }

    // Show loading state
    sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
    sectionSelect.disabled = true;

    return fetch(`{{ url('/api/grades') }}/${gradeId}/sections`)
        .then(res => {
            console.log('Response status:', res.status);
            if (!res.ok) {
                throw new Error('Network response was not ok: ' + res.status);
            }
            return res.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.error) {
                throw new Error(data.error);
            }
            if (data.length === 0) {
                sectionSelect.innerHTML = '<option value="">No sections available</option>';
            } else {
                sectionSelect.innerHTML = '<option value="">Select Section</option>' +
                    data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            }
            sectionSelect.disabled = false;
        })
        .catch((error) => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error: ' + error.message + '</option>';
            sectionSelect.disabled = false;
        });
}

function openAiModal() {
    document.getElementById('aiModal').style.display = 'flex';
}

function closeAiModal() {
    document.getElementById('aiModal').style.display = 'none';
}

function generateWithAI() {
    const topic = document.getElementById('aiTopic').value;
    if (!topic) {
        alert('Please enter a topic');
        return;
    }

    const btn = document.querySelector('#aiModal .btn-primary');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    fetch('{{ route("teacher.ai.generate-quest") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ topic })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Populate form with generated data
            document.querySelector('input[name="title"]').value = data.data.title;
            document.querySelector('textarea[name="description"]').value = data.data.description;
            document.querySelector('input[name="xp_reward"]').value = data.data.xp_reward;
            document.querySelector('input[name="ab_reward"]').value = data.data.ab_reward;
            document.querySelector('input[name="gp_reward"]').value = data.data.gp_reward;
            
            questions = data.data.challenges || [];
            renderQuestions();
            closeAiModal();
            alert('Quest generated successfully!');
        } else {
            alert('Failed to generate quest: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Failed to generate quest'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Form validation
document.getElementById('questForm').addEventListener('submit', function(e) {
    if (questions.length === 0) {
        e.preventDefault();
        alert('Please add at least one question');
        nextStep(2);
    }
});

// Initialize level dropdowns on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLevelDropdowns();
    if (questIsEdit) {
        if (questions.length) {
            renderQuestions();
        }
        const gradeEl = document.getElementById('gradeSelect');
        if (gradeEl && gradeEl.value) {
            loadSections().then(() => {
                const sectionEl = document.getElementById('sectionSelect');
                if (sectionEl && questInitialSectionId != null) {
                    sectionEl.value = String(questInitialSectionId);
                }
            });
        }
    }
});
</script>
@endpush
@endsection
