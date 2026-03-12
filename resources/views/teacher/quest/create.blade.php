@extends('teacher.dashboard')

@section('content')

<!-- Page Header -->
<div class="quest-create-header">
    <div>
        <div class="quest-pill">
            <i class="fas fa-scroll"></i>
            Quest Forge
        </div>
        <h2>
            <i class="fas fa-hammer"></i>
            Create a New Quest
        </h2>
        <p>Design your quest details, add challenges, and choose the party that will embark on this adventure.</p>
    </div>

    <!-- Hidden back button (kept for layout consistency if you want to show later) -->
    <a href="{{ route('teacher.quest') }}" class="btn-quest-primary" style="visibility: hidden;">
        ← Back to Manage Quests
    </a>
</div>

<!-- STEP INDICATOR -->
<div class="quest-steps">
    <div class="quest-step quest-step-active" data-step="1">
        <span class="step-number">1</span>
        <span class="step-label">Quest Details</span>
    </div>
    <div class="quest-step" data-step="2">
        <span class="step-number">2</span>
        <span class="step-label">Challenges</span>
    </div>
    <div class="quest-step" data-step="3">
        <span class="step-number">3</span>
        <span class="step-label">Target Party</span>
    </div>
</div>

<div class="quiz-container">

    <!-- STEP 1: Quest Details -->
    <div class="form-step" id="step-1">
        <div class="form-container form-card">
            <div class="form-header-flex">
                <div class="form-header-content">
                    <div class="form-header-icon">
                        <i class="fas fa-scroll"></i>
                    </div>
                    <div>
                        <h2>Quest Details</h2>
                        <p class="form-subtitle">Name your quest, describe the adventure, set difficulty and rewards.</p>
                    </div>
                </div>
                <button type="button" class="btn-ai-reforge" onclick="openAIModal()">
                    <i class="fas fa-magic"></i>
                    Reforge with AI
                </button>
            </div>
            <hr>

            <form class="quest-form" id="quest-form">
                <div class="form-group">
                    <label for="quest-title">
                        Quest Title
                        <span class="badge-soft">Required</span>
                    </label>
                    <input type="text" id="quest-title" placeholder="e.g., Trials of Fractions – Dungeon of Division" required>
                </div>

                <div class="form-group">
                    <label for="quest-description">Quest Description</label>
                    <textarea id="quest-description" placeholder="Describe the story, objective, and what students must accomplish..." required></textarea>
                    <small class="helper-text">
                        Tip: Turn it into a narrative – heroes, monsters, puzzles, and rewards.
                    </small>
                </div>

                <div class="difficulty-and-rewards">
                <div class="difficulty-and-level">
                    <div class="form-group flex-1">
                        <label for="difficulty">
                            <i class="fas fa-swords"></i> Difficulty
                        </label>
                        <div class="premium-input-wrap">
                            <select id="difficulty">
                                <option value="easy">Easy (Warm-up)</option>
                                <option value="medium" selected>Medium (Standard Quest)</option>
                                <option value="hard">Hard (Boss Battle)</option>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group flex-1">
                        <label for="quest-level">
                            <i class="fas fa-stairs"></i> Total Quest Levels
                        </label>
                        <div class="premium-input-wrap">
                            <input type="number" id="quest-level" value="3" min="1" max="10" onchange="updateLevelProgressTracker()" required>
                            <div class="input-unit">STAGES</div>
                        </div>
                        <small class="helper-text">Defines how many nodes appear on the map.</small>
                    </div>
                </div>

                <div class="rewards-forge-container">
                    <label class="section-label">Quest Rewards</label>
                    <div class="rewards-forge">
                        <div class="reward-card xp">
                            <div class="reward-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="reward-details">
                                <label for="xp-reward">XP Reward</label>
                                <div class="reward-input-wrap">
                                    <input type="number" id="xp-reward" value="100">
                                    <span class="reward-badge">XP</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="reward-card ab">
                            <div class="reward-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="reward-details">
                                <label for="ab-reward">AB Reward</label>
                                <div class="reward-input-wrap">
                                    <input type="number" id="ab-reward" value="50">
                                    <span class="reward-badge">AB</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="reward-card gp">
                            <div class="reward-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="reward-details">
                                <label for="gp-reward">GP Reward</label>
                                <div class="reward-input-wrap">
                                    <input type="number" id="gp-reward" value="25">
                                    <span class="reward-badge">GP</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Date & Time -->
                <div class="dates">
                    <div class="form-group">
                        <label for="assign-datetime">
                            Assign Date &amp; Time
                            <span class="badge-soft">Required</span>
                        </label>
                        <input type="datetime-local" id="assign-datetime" required>
                    </div>
                    <div class="form-group">
                        <label for="due-datetime">
                            Due Date &amp; Time
                            <span class="badge-soft danger">Required</span>
                        </label>
                        <input type="datetime-local" id="due-datetime" required>
                    </div>
                </div>
            </form>
        </div>

        <div class="step-footer">
            <button class="cancel-button" onclick="cancelQuest()">Cancel</button>
            <button class="next-button" onclick="nextStep(2)">
                Next: Add Challenges
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- STEP 2: Questions -->
    <div class="form-step" id="step-2" style="display: none;">
        <div class="form-container form-card">
            <div class="form-header-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <h3>Quest Challenges</h3>
            <p class="form-subtitle">Create the puzzles, questions, and checks that students must conquer.</p>
            
            <!-- LEVEL PROGRESS TRACKER -->
            <div class="level-progression-tracker" id="level-tracker">
                {{-- Dynamic levels will be inserted here --}}
            </div>
            <hr>

            <!-- AI QUESTION FORGE Section -->
            <div class="ai-question-forge-container">
                <div class="ai-forge-inner">
                    <div class="ai-forge-header">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        <span>AI Question Forge</span>
                    </div>
                    <div class="ai-forge-body">
                        <input type="text" id="ai-question-topic" placeholder="Question topic (e.g. Photosynthesis, Algebra)...">
                        <button type="button" class="btn-ai-generate-single" onclick="generateSingleQuestionWithAI()">
                            <i class="fas fa-sparkles"></i> Forge
                        </button>
                    </div>
                </div>
                <div id="ai-single-loading" class="ai-mini-loader" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Weaving magic...
                </div>
            </div>

            <div class="question-form">
                <div class="form-group">
                    <label for="question-text">Question</label>
                    <textarea id="question-text" placeholder="Enter the challenge question, riddle, or prompt..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="question-type">Question Type</label>
                    <div class="select-with-icon">
                        <i class="fas fa-magic"></i>
                        <select id="question-type" onchange="toggleQuestionFields()" required>
                            <option value="" disabled selected hidden>Select Question Type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="identification">Identification</option>
                        </select>
                    </div>
                </div>

                <div id="multiple-choice-options" class="question-options">
                    <label>Options</label>
                    <div id="options-container"></div>
                    <button type="button" class="add-option-button" onclick="addOption()">
                        <i class="fas fa-plus-circle"></i>
                        Add Option
                    </button>
                    <small class="helper-text">
                        Mark the correct answer using the radio button on the right.
                    </small>
                </div>

                <div id="identification-answer" class="question-options">
                    <label>Correct Answer</label>
                    <input type="text" class="correct-answer" placeholder="Enter the correct answer..." required/>
                </div>

                <div class="form-group points-group">
                    <label for="question-level">
                        Select Level
                        <span class="badge-soft">Progression</span>
                    </label>
                    <div class="points-input-wrap">
                        <i class="fas fa-stairs"></i>
                        <select id="question-level" required>
                            {{-- Populated by JS based on Total Levels --}}
                        </select>
                    </div>
                </div>

                <div class="form-group points-group">
                    <label for="points">
                        Points
                        <span class="badge-soft">Scoring</span>
                    </label>
                    <div class="points-input-wrap">
                        <i class="fas fa-trophy"></i>
                        <input type="number" id="points" value="10">
                    </div>
                </div>

                <button type="button" class="add-question-button" onclick="addQuestion()">
                    <i class="fas fa-plus"></i>
                    Add Question to Quest
                </button>
            </div>
        </div>

        <div class="form-container form-card added-questions-card" id="added-questions-section">
            <h3>Added Questions</h3>
            <p class="form-subtitle">Review, edit, or remove questions before finalizing the quest.</p>
            <hr>
            <div class="added-questions-list">
                <ul id="questions-list"></ul>
            </div>
        </div>

        <div class="step-footer between">
            <button class="back-button" onclick="previousStep(1)">
                <i class="fas fa-arrow-left"></i>
                Back to Details
            </button>
            <button class="next-button" onclick="nextStep(3)">
                Next: Target Party
                <i class="fas fa-users"></i>
            </button>
        </div>
    </div>

    <!-- STEP 3: Target Audience -->
    <div class="form-step" id="step-3" style="display: none;">
        <div class="form-container form-card">
            <div class="form-header-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Target Party</h3>
            <p class="form-subtitle">Choose which grade and section will receive this quest.</p>
            <hr>

            <div class="target-audience">
                <div class="audience-selects">
                    {{-- Grade Select --}}
                    <div class="form-group">
                        <label for="grade">Grade</label>
                        <div class="select-with-icon">
                            <i class="fas fa-layer-group"></i>
                            <select id="grade" required onchange="loadSections(this.value)">
                                <option value="" disabled selected hidden>Select Grade</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}">
                                        {{ $grade->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Section Select --}}
                    <div class="form-group">
                        <label for="section">Section</label>
                        <div class="select-with-icon">
                            <i class="fas fa-users-class"></i>
                            <select id="section" required>
                                <option value="" disabled selected hidden>Select Section</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="target-tip">
                    <i class="fas fa-lightbulb"></i>
                    You can reuse this quest later by assigning it to different sections or grades.
                </div>
            </div>
        </div>

        <div class="step-footer between">
            <button class="back-button" onclick="previousStep(2)">
                <i class="fas fa-arrow-left"></i>
                Back to Challenges
            </button>
            <div class="right-buttons">
                <button class="cancel-button" onclick="cancelQuest()">Cancel</button>
                <button class="create-quest-button" onclick="createQuest()">
                    <i class="fas fa-check-circle"></i>
                    Create Quest
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modals (kept, just restyled) --}}
<div id="customConfirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h3 id="confirmTitle">Confirm Action</h3>
        <p id="confirmMessage">Are you sure?</p>
        <div class="modal-buttons">
            <button id="confirmCancel" class="btn-cancel">Cancel</button>
            <button id="confirmOk" class="btn-ok">OK</button>
        </div>
    </div>
</div>

<div id="customSuccessModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h3 id="successTitle">Success</h3>
        <p id="successMessage">Quest created successfully!</p>
        <div class="modal-buttons">
            <button id="successOk" class="btn-ok">OK</button>
        </div>
    </div>
</div>

<div id="customAlertModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h3 id="alertTitle">Notice</h3>
        <p id="alertMessage">Message goes here.</p>
        <div class="modal-buttons">
            <button id="alertOk" class="btn-ok">OK</button>
        </div>
    </div>
</div>

{{-- AI REFORGE MODAL --}}
<div id="aiReforgeModal" class="modal-overlay" style="display: none;">
    <div class="modal-box ai-modal-premium">
        <div class="ai-modal-glow"></div>
        
        <div class="ai-modal-header">
            <div class="ai-neural-icon">
                <i class="fas fa-brain"></i>
            </div>
            <div>
                <h3>Neural Quest Forge</h3>
                <p>Consult the elders to weave a new adventure.</p>
            </div>
        </div>
        
        <div class="ai-modal-content">
            <div class="form-group" style="text-align:left;">
                <label class="ai-input-label">Quest Topic / Source Material</label>
                <div class="ai-input-wrapper">
                    <textarea id="ai-topic" placeholder="e.g., The secret history of the Phoenix, or an introduction to Algebra spells..." class="ai-hero-textarea"></textarea>
                    <div class="ai-input-shadow"></div>
                </div>
            </div>

            <div class="modal-buttons" style="margin-top:25px; gap:15px;">
                <button onclick="closeAIModal()" class="btn-ai-cancel">
                    <i class="fas fa-times"></i> Dismiss
                </button>
                <button onclick="generateWithAI()" id="btn-ai-generate" class="btn-ai-forge-premium">
                    <i class="fas fa-sparkles"></i> Forge Content
                </button>
            </div>
        </div>

        <div id="ai-loading" style="display:none; margin-top:20px; text-align:center;">
            <div class="ai-loader-container">
                <div class="ai-ring"></div>
                <div class="ai-spark"></div>
            </div>
            <p class="ai-loading-text">Synchronizing with the Neural Realm...</p>
        </div>
    </div>
</div>

<script>
let questions = [];
let optionCount = 0;
let editingIndex = null;

function updateLevelProgressTracker() {
    const totalLevels = parseInt(document.getElementById('quest-level').value) || 1;
    const tracker = document.getElementById('level-tracker');
    const levelSelect = document.getElementById('question-level');
    
    // Update Tracker UI
    tracker.innerHTML = '';
    for (let i = 1; i <= totalLevels; i++) {
        const hasQuestions = questions.some(q => q.level == i);
        const dot = document.createElement('div');
        dot.className = `level-dot ${hasQuestions ? 'active' : ''}`;
        dot.innerHTML = `<span>${i}</span>`;
        dot.title = `Level ${i}: ${hasQuestions ? 'Ready' : 'Empty'}`;
        tracker.appendChild(dot);
    }

    // Update Question Level Dropdown
    const currentVal = levelSelect.value;
    levelSelect.innerHTML = '';
    for (let i = 1; i <= totalLevels; i++) {
        const opt = document.createElement('option');
        opt.value = i;
        opt.textContent = `Level ${i}`;
        levelSelect.appendChild(opt);
    }
    if (currentVal && currentVal <= totalLevels) {
        levelSelect.value = currentVal;
    }
}

function openAIModal() {
    document.getElementById('aiReforgeModal').style.display = 'flex';
}

function closeAIModal() {
    document.getElementById('aiReforgeModal').style.display = 'none';
    document.getElementById('ai-loading').style.display = 'none';
}

function generateWithAI() {
    const topic = document.getElementById('ai-topic').value;
    if (!topic) {
        showCustomAlert("Please enter a topic for the forge.");
        return;
    }

    const btn = document.getElementById('btn-ai-generate');
    const loading = document.getElementById('ai-loading');
    
    btn.disabled = true;
    loading.style.display = 'block';

    fetch("{{ route('teacher.ai.generate-quest') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            topic: topic,
            difficulty: document.getElementById('difficulty').value,
            total_levels: parseInt(document.getElementById('quest-level').value) || 3
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            const data = result.data;
            
            // Populate Step 1
            document.getElementById('quest-title').value = data.title;
            document.getElementById('quest-description').value = data.description;
            document.getElementById('xp-reward').value = data.xp_reward;
            document.getElementById('ab-reward').value = data.ab_reward;
            document.getElementById('gp-reward').value = data.gp_reward;
            
            // Sync topic to individual question generator
            document.getElementById('ai-question-topic').value = topic;

            // Populate Step 2
            questions = data.challenges.map((c, i) => ({
                id: `Q${i + 1}`,
                text: c.text,
                type: c.type,
                level: c.level || (i + 1),
                points: c.points,
                answer: c.answer,
                options: c.options || [],
                correctIndex: c.type === 'multiple_choice' ? c.options.indexOf(c.answer) : null
            }));

            displayAddedQuestions();
            updateLevelProgressTracker();
            closeAIModal();
            showCustomSuccess("AI has successfully reforged the quest content!", () => {
                // Stay on current page but now it's populated
            });
        } else {
            showCustomAlert("The Neural Link was interrupted. Please try again.");
        }
    })
    .catch(error => {
        console.error("AI Error:", error);
        showCustomAlert("A mystical error occurred during the forge.");
    })
    .finally(() => {
        btn.disabled = false;
        loading.style.display = 'none';
    });
}

function generateSingleQuestionWithAI() {
    let topic = document.getElementById('ai-question-topic').value;
    const type = document.getElementById('question-type').value;
    const questTitle = document.getElementById('quest-title').value;
    
    // Fallback to quest title if topic is missing
    if (!topic && questTitle) {
        topic = questTitle;
        document.getElementById('ai-question-topic').value = topic;
    }

    if (!topic) {
        showCustomAlert("Please enter a topic for the question.");
        return;
    }
    if (!type) {
        showCustomAlert("Please select a question type first.");
        return;
    }

    const btn = document.querySelector('.btn-ai-generate-single');
    const loading = document.getElementById('ai-single-loading');
    
    btn.disabled = true;
    loading.style.display = 'block';

    fetch("{{ route('teacher.ai.generate-question') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            topic: topic,
            type: type,
            difficulty: document.getElementById('difficulty').value
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            const data = result.data;
            
            // Populate Fields
            document.getElementById('question-text').value = data.text;
            document.getElementById('question-level').value = data.level || 1;
            document.getElementById('points').value = data.points;

            if (data.type === 'multiple_choice') {
                document.getElementById('options-container').innerHTML = '';
                optionCount = 0;
                data.options.forEach((opt, idx) => {
                    addOption();
                    const inputs = document.querySelectorAll('#options-container input[type="text"]');
                    const lastInput = inputs[inputs.length - 1];
                    lastInput.value = opt;
                    
                    if (opt === data.answer) {
                        const radios = document.querySelectorAll('#options-container input[type="radio"]');
                        radios[radios.length - 1].checked = true;
                    }
                });
                toggleQuestionFields();
            } else {
                document.querySelector('#identification-answer .correct-answer').value = data.answer;
                toggleQuestionFields();
            }
            
            showCustomSuccess("Question generated successfully! Review and click 'Add Question to Quest'.", () => {
                // Stay on current page
            });
        } else {
            showCustomAlert("The Neural Link was interrupted. Please try again.");
        }
    })
    .catch(error => {
        console.error("AI Error:", error);
        showCustomAlert("A mystical error occurred during the forge.");
    })
    .finally(() => {
        btn.disabled = false;
        loading.style.display = 'none';
    });
}

function toggleQuestionFields() {
    const questionType = document.getElementById('question-type').value;
    document.getElementById('multiple-choice-options').style.display = 'none';
    document.getElementById('identification-answer').style.display = 'none';
    if (questionType === 'multiple_choice') {
        document.getElementById('multiple-choice-options').style.display = 'block';
    } else if (questionType === 'identification') {
        document.getElementById('identification-answer').style.display = 'block';
    }
}
window.onload = toggleQuestionFields;

function addOption() {
    optionCount++;
    const container = document.getElementById('options-container');
    const optionGroup = document.createElement('div');
    optionGroup.classList.add('option-group');

    const formGroup = document.createElement('div');
    formGroup.classList.add('form-group');

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = `Option ${optionCount}`;

    const radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = 'correct-option';
    radio.classList.add('correct-radio');

    formGroup.appendChild(input);
    optionGroup.appendChild(formGroup);
    optionGroup.appendChild(radio);
    container.appendChild(optionGroup);
}

function addQuestion() {
    const questionTextEl = document.getElementById('question-text');
    const questionTypeEl = document.getElementById('question-type');
    const pointsEl = document.getElementById('points');
    const levelEl = document.getElementById('question-level');

    if (!questionTextEl.checkValidity()) {
        questionTextEl.reportValidity();
        return;
    }
    if (!questionTypeEl.checkValidity()) {
        questionTypeEl.reportValidity();
        return;
    }
    if (!levelEl.checkValidity()) {
        levelEl.reportValidity();
        return;
    }
    if (!pointsEl.checkValidity()) {
        pointsEl.reportValidity();
        return;
    }

    const questionText = questionTextEl.value.trim();
    const questionType = questionTypeEl.value;
    const points = pointsEl.value.trim();
    const level = levelEl.value.trim();

    let correctAnswer = "";
    let options = [];
    let correctIndex = null;

    if (questionType === 'multiple_choice') {
        const optionTextInputs = Array.from(
            document.querySelectorAll('#options-container input[type="text"]')
        );
        const radios = Array.from(
            document.querySelectorAll('#options-container input[type="radio"]')
        );

        if (optionTextInputs.length < 2) {
            showCustomAlert("Please add at least two options for a multiple-choice question.");
            return;
        }

        options = optionTextInputs.map(input => input.value.trim());

        if (options.some(v => v === '')) {
            showCustomAlert("Please fill in all option fields or remove any empty ones.");
            return;
        }

        correctIndex = radios.findIndex(r => r.checked);
        if (correctIndex === -1) {
            showCustomAlert("Please select the correct answer using the radio button.");
            return;
        }

        correctAnswer = options[correctIndex];
    } else if (questionType === 'identification') {
        const idAnswerInput = document.querySelector('#identification-answer .correct-answer');
        if (!idAnswerInput.value.trim()) {
            idAnswerInput.reportValidity();
            return;
        }
        correctAnswer = idAnswerInput.value.trim();
    }

    if (editingIndex !== null) {
        const q = questions[editingIndex];
        q.text = questionText;
        q.type = questionType;
        q.level = level;
        q.points = points;
        q.answer = correctAnswer;

        if (questionType === 'multiple_choice') {
            q.options = options;
            q.correctIndex = correctIndex;
        } else {
            q.options = [];
            q.correctIndex = null;
        }

        displayAddedQuestions();
        updateLevelProgressTracker();
        resetQuestionForm();
        editingIndex = null;
        document.querySelector('.add-question-button').textContent = '+ Add Question to Quest';
        return;
    }

    const newQuestion = {
        id: `Q${questions.length + 1}`,
        text: questionText,
        type: questionType,
        level: level,
        points: points,
        answer: correctAnswer,
        options: [],
        correctIndex: null,
    };

    if (questionType === 'multiple_choice') {
        newQuestion.options = options;
        newQuestion.correctIndex = correctIndex;
    }

    questions.push(newQuestion);
    displayAddedQuestions();
    updateLevelProgressTracker();
    resetQuestionForm();
}

function editQuestion(index) {
    const question = questions[index];

    document.getElementById('question-text').value = question.text;
    document.getElementById('question-type').value = question.type;
    document.getElementById('question-level').value = question.level || 1;
    document.getElementById('points').value = question.points;

    toggleQuestionFields();

    document.getElementById('options-container').innerHTML = '';
    optionCount = 0;

    if (question.type === 'multiple-choice' && question.options) {
        const container = document.getElementById('options-container');
        question.options.forEach((optText, i) => {
            optionCount++;
            const optionGroup = document.createElement('div');
            optionGroup.classList.add('option-group');

            const formGroup = document.createElement('div');
            formGroup.classList.add('form-group');

            const input = document.createElement('input');
            input.type = 'text';
            input.value = optText;
            input.placeholder = `Option ${i + 1}`;

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'correct-option';
            radio.classList.add('correct-radio');
            if (question.correctIndex === i) {
                radio.checked = true;
            }

            formGroup.appendChild(input);
            optionGroup.appendChild(formGroup);
            optionGroup.appendChild(radio);
            container.appendChild(optionGroup);
        });

        document.getElementById('multiple-choice-options').style.display = 'block';
    }

    if (question.type === 'identification') {
        const idAnswerInput = document.querySelector('#identification-answer .correct-answer');
        if (idAnswerInput) {
            idAnswerInput.value = question.answer || '';
        }
    }

    editingIndex = index;

    const addButton = document.querySelector('.add-question-button');
    addButton.textContent = 'Update Question';
}

function deleteQuestion(index) {
    showCustomConfirm("Are you sure you want to delete this question?", function(confirmed) {
        if (confirmed) {
            questions.splice(index, 1);
            questions.forEach((q, i) => {
                q.id = `Q${i + 1}`;
            });
            displayAddedQuestions();
            updateLevelProgressTracker();
        }
    });
}

function displayAddedQuestions() {
    const questionList = document.getElementById('questions-list');
    questionList.innerHTML = '';

    questions.forEach((question, index) => {
        const li = document.createElement('li');
        li.classList.add('question-item');
        
        // Icon based on type
        const typeIcon = question.type === 'multiple-choice' ? 'fa-list-ul' : 'fa-font';
        
        li.innerHTML = `
            <div class="question-content">
                <h4><i class="fas ${typeIcon}" style="margin-right:8px; opacity:0.6;"></i> ${question.text}</h4>
                <div class="question-meta">
                    <span class="meta-tag">#${question.id}</span>
                    <span class="meta-tag">LVL ${question.level || 1}</span>
                    <span class="meta-tag">${question.type.replace('-', ' ')}</span>
                    <span class="meta-tag"><i class="fas fa-star" style="color:var(--accent);"></i> ${question.points} Points</span>
                </div>
            </div>
            <div class="question-actions">
                <button class="edit-btn" onclick="editQuestion(${index})"><i class="fas fa-pen"></i></button>
                <button class="delete-btn" onclick="deleteQuestion(${index})"><i class="fas fa-trash-alt"></i></button>
            </div>
        `;
        questionList.appendChild(li);
    });
}

function resetQuestionForm() {
    document.getElementById('question-text').value = '';
    document.getElementById('question-level').value = 1;
    document.getElementById('points').value = 10;

    const questionTypeSelect = document.getElementById('question-type');
    questionTypeSelect.value = '';

    document.getElementById('options-container').innerHTML = '';
    optionCount = 0;

    document.querySelectorAll('.correct-answer').forEach(input => {
        input.value = '';
    });

    document.querySelectorAll('.question-options').forEach(div => {
        div.style.display = 'none';
    });

    toggleQuestionFields();
}

/* Step navigation + indicator */
function updateStepIndicator(step) {
    const steps = document.querySelectorAll('.quest-step');
    steps.forEach(s => {
        const sStep = s.getAttribute('data-step');
        if (parseInt(sStep) === step) {
            s.classList.add('quest-step-active');
        } else {
            s.classList.remove('quest-step-active');
        }
    });
}

function nextStep(step) {
    const currentStep = document.querySelector(`#step-${step - 1}`);
    const form = currentStep ? currentStep.querySelector('form') : null;

    if (form && !form.checkValidity()) {
        form.reportValidity();
        return;
    }

    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    document.getElementById(`step-${step}`).style.display = 'block';
    
    if (step === 2) {
        updateLevelProgressTracker();
    }

    updateStepIndicator(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function previousStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    document.getElementById(`step-${step}`).style.display = 'block';
    
    if (step === 2) {
        updateLevelProgressTracker();
    }

    updateStepIndicator(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function createQuest() {
    const step3 = document.getElementById('step-3');
    const selects = step3.querySelectorAll('select');
    
    // Validate Step 1
    const formStep1 = document.getElementById('quest-form');
    if (!formStep1.checkValidity()) {
        formStep1.reportValidity();
        showCustomAlert("Please fill in all required fields in Quest Details.");
        return;
    }

    for (let select of selects) {
        if (!select.checkValidity()) {
            select.reportValidity();
            return;
        }
    }

    if (questions.length === 0) {
        showCustomAlert("Please add at least one question before creating the quest.");
        return;
    }

    // New validation: Ensure every level has at least one question
    const totalLevels = parseInt(document.getElementById('quest-level').value);
    const missingLevels = [];
    for (let i = 1; i <= totalLevels; i++) {
        if (!questions.some(q => q.level == i)) {
            missingLevels.push(i);
        }
    }

    if (missingLevels.length > 0) {
        showCustomAlert(`Your quest map is incomplete! Please add at least one question to the following levels: ${missingLevels.join(', ')}`);
        return;
    }

    const questData = {
        title: document.getElementById('quest-title').value,
        description: document.getElementById('quest-description').value,
        difficulty: document.getElementById('difficulty').value,
        level: document.getElementById('quest-level').value,
        xp_reward: document.getElementById('xp-reward').value,
        ab_reward: document.getElementById('ab-reward').value,
        gp_reward: document.getElementById('gp-reward').value,
        assign_date: document.getElementById('assign-datetime').value,
        due_date: document.getElementById('due-datetime').value,
        grade_id: document.getElementById('grade').value,
        section_id: document.getElementById('section').value,
        questions: questions
    };

    const btn = document.querySelector('.create-quest-button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Forging Quest...';
    btn.disabled = true;

    fetch("{{ route('teacher.quest.store') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(questData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showCustomSuccess(result.message);
        } else {
            showCustomAlert(result.message || "An error occurred during creation.");
        }
    })
    .catch(error => {
        console.error("Error creating quest:", error);
        showCustomAlert("A critical magic failure occurred. Please try again.");
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function cancelQuest() {
    showCustomConfirm("Are you sure you want to cancel? All progress will be lost.", function(confirmed) {
        if (confirmed) {
            window.location.href = "{{ route('teacher.quest') }}";
        }
    });
}

/* Modals */
function showCustomConfirm(message, onConfirm) {
    const modal = document.getElementById("customConfirmModal");
    const msg = document.getElementById("confirmMessage");
    const ok = document.getElementById("confirmOk");
    const cancel = document.getElementById("confirmCancel");

    msg.textContent = message;
    modal.style.display = "flex";

    const newOk = ok.cloneNode(true);
    const newCancel = cancel.cloneNode(true);
    ok.parentNode.replaceChild(newOk, ok);
    cancel.parentNode.replaceChild(newCancel, cancel);

    newOk.addEventListener("click", () => {
        modal.style.display = "none";
        onConfirm(true);
    });

    newCancel.addEventListener("click", () => {
        modal.style.display = "none";
        onConfirm(false);
    });
}

function showCustomSuccess(message, onClose) {
    const modal = document.getElementById("customSuccessModal");
    const msg = document.getElementById("successMessage");
    const ok = document.getElementById("successOk");

    msg.textContent = message;
    modal.style.display = "flex";

    const newOk = ok.cloneNode(true);
    ok.parentNode.replaceChild(newOk, ok);

    newOk.addEventListener("click", () => {
        modal.style.display = "none";
        if (onClose) {
            onClose();
        } else {
            window.location.href = "{{ route('teacher.quest') }}";
        }
    });
}

function showCustomAlert(message, onClose) {
    const modal = document.getElementById("customAlertModal");
    const msg = document.getElementById("alertMessage");
    const ok = document.getElementById("alertOk");

    msg.textContent = message;
    modal.style.display = "flex";

    const newOk = ok.cloneNode(true);
    ok.parentNode.replaceChild(newOk, ok);

    newOk.addEventListener("click", () => {
        modal.style.display = "none";
        if (onClose) onClose();
    });
}

const GRADES_DATA = @json($grades);

function loadSections(gradeId) {
    const sectionSelect = document.getElementById("section");
    sectionSelect.innerHTML = `<option value="" disabled selected hidden>Select Section</option>`;

    const grade = GRADES_DATA.find(g => g.id == gradeId);

    if (grade && grade.sections.length > 0) {
        grade.sections.forEach(sec => {
            const opt = document.createElement("option");
            opt.value = sec.id;
            opt.textContent = sec.name;
            sectionSelect.appendChild(opt);
        });
    } else {
        const opt = document.createElement("option");
        opt.disabled = true;
        opt.textContent = "No sections available";
        sectionSelect.appendChild(opt);
    }
}
</script>

<style>
    .quest-create-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 18px;
    }

    .quest-create-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quest-create-header p {
        margin-top: 6px;
        font-size: 0.9rem;
        color: var(--text-muted);
        max-width: 520px;
    }

    .quest-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0,35,102,0.08);
        color: var(--primary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .quest-pill i {
        color: var(--accent-dark);
    }

    .btn-quest-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 10px 20px;
        border-radius: 999px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(0,0,0,0.25);
        border: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }

    .btn-quest-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.32);
    }

    .form-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .form-header-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .btn-ai-reforge {
        background: linear-gradient(135deg, #7c3aed, #4812e8ff);
        color: #fff;
        padding: 10px 20px;
        border-radius: 999px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(124,58,237,0.3);
        transition: all 0.2s;
    }

    .btn-ai-reforge:hover {
        transform: translateY(-2px) rotate(-1deg);
        box-shadow: 0 8px 20px rgba(124,58,237,0.4);
        filter: brightness(1.1);
    }

    /* AI PREMIUM MODAL STYLES */
    .ai-modal-premium {
        position: relative;
        background: radial-gradient(circle at top right, #0f172a, #0b1121);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 550px;
        border: 1px solid rgba(96, 165, 250, 0.2);
        box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        overflow: hidden;
        backdrop-filter: blur(20px);
    }

    .ai-modal-glow {
        position: absolute;
        top: -150px;
        right: -150px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(96, 165, 250, 0.15), transparent 70%);
        pointer-events: none;
    }

    .ai-modal-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .ai-neural-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #fff;
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.4);
        animation: neural-pulse 3s infinite ease-in-out;
    }

    @keyframes neural-pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 25px rgba(59, 130, 246, 0.4); }
        50% { transform: scale(1.05); box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
    }

    .ai-modal-header h3 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 4px;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .ai-modal-header p {
        font-size: 0.9rem;
        color: #64748b;
    }

    .ai-input-label {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #60a5fa;
        margin-bottom: 12px;
    }

    .ai-input-wrapper {
        position: relative;
    }

    .ai-hero-textarea {
        width: 100%;
        height: 120px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px;
        color: #fff;
        font-size: 1rem;
        line-height: 1.6;
        resize: none;
        transition: all 0.3s;
        position: relative;
        z-index: 2;
    }

    .ai-hero-textarea:focus {
        outline: none;
        border-color: #60a5fa;
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
    }

    .ai-input-shadow {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);
        border-radius: 16px;
        z-index: 1;
        opacity: 0;
        transition: 0.3s;
    }

    .ai-hero-textarea:focus + .ai-input-shadow {
        opacity: 1;
    }

    .btn-ai-forge-premium {
        flex: 2;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        transition: all 0.3s;
    }

    .btn-ai-forge-premium:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
    }

    .btn-ai-cancel {
        flex: 1;
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 20px;
        border-radius: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-ai-cancel:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    /* AI LOADING ANIMATION */
    .ai-loader-container {
        position: relative;
        width: 50px;
        height: 50px;
        margin: 0 auto;
    }

    .ai-ring {
        width: 100%;
        height: 100%;
        border: 3px solid rgba(96, 165, 250, 0.1);
        border-top-color: #60a5fa;
        border-radius: 50%;
        animation: ai-spin 1s infinite linear;
    }

    .ai-spark {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 15px;
        height: 15px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 0 20px #60a5fa;
        transform: translate(-50%, -50%);
        animation: ai-glow 2s infinite alternate;
    }

    @keyframes ai-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes ai-glow {
        from { opacity: 0.5; box-shadow: 0 0 10px #60a5fa; }
        to { opacity: 1; box-shadow: 0 0 30px #60a5fa; }
    }

    .ai-loading-text {
        font-size: 0.9rem;
        color: #60a5fa;
        font-weight: 600;
        margin-top: 15px;
        letter-spacing: 0.5px;
    }

    /* Step indicator */
    .quest-steps {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .quest-step {
        flex: 1;
        min-width: 140px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,0.8);
        background: rgba(241,241,224,0.8);
        box-shadow: 0 4px 12px rgba(15,23,42,0.12);
        font-size: 0.85rem;
        color: var(--text-muted);
        transition: all 0.18s ease;
    }

    .quest-step .step-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(15,23,42,0.75);
        color: #e5edff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .quest-step .step-label {
        font-weight: 500;
    }

    .quest-step.quest-step-active {
        border-color: var(--accent);
        background: linear-gradient(135deg, rgba(0,35,102,0.07), rgba(255,212,59,0.35));
        color: var(--primary);
        box-shadow: 0 6px 16px rgba(15,23,42,0.25);
    }

    .quest-step.quest-step-active .step-number {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
    }

    .quiz-container {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-card {
        background: rgba(241,241,224,0.92);
        border-radius: 18px;
        padding: 22px 22px 24px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.22);
        border: 1px solid rgba(255,255,255,0.8);
        position: relative;
        overflow: hidden;
    }
    #ai-topic{
        color:black;
    }

    .form-card::before {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,212,59,0.35), transparent 60%);
        opacity: 0.8;
    }

    .form-card > * {
        position: relative;
        z-index: 1;
    }

    .form-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: rgba(0,35,102,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.4rem;
        margin-bottom: 8px;
    }

    .form-container h2,
    .form-container h3 {
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 4px;
    }

    .form-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    hr {
        border: none;
        border-top: 2px solid rgba(226,232,240,0.9);
        margin: 10px 0 20px;
    }

    .quest-form,
    .question-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        color: #1e293b;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 0.9rem;
        background: rgba(255,255,255,0.85);
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .form-group textarea {
        min-height: 90px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 1px rgba(0,35,102,0.3);
        background: #fff;
    }

    .helper-text {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        background: rgba(148,163,184,0.15);
        color: #475569;
    }

    .badge-soft.danger {
        background: rgba(248,113,113,0.15);
        color: #b91c1c;
    }

    .difficulty-and-level {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 5px;
    }

    .flex-1 { flex: 1; min-width: 200px; }

    .section-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--primary);
        margin-bottom: 10px;
        display: block;
        opacity: 0.8;
    }

    .premium-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.9);
        border: 1.5px solid rgba(0, 35, 102, 0.15);
        border-radius: 14px;
        padding: 2px 12px;
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .premium-input-wrap:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 35, 102, 0.1), 0 4px 12px rgba(0,0,0,0.05);
        transform: translateY(-1px);
        background: #fff;
    }

    .premium-input-wrap select,
    .premium-input-wrap input {
        width: 100%;
        border: none;
        background: transparent;
        padding: 10px 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--primary);
        outline: none;
        appearance: none;
    }

    .select-arrow {
        position: absolute;
        right: 15px;
        pointer-events: none;
        color: var(--primary);
        font-size: 0.8rem;
        opacity: 0.6;
    }

    .input-unit {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--primary);
        background: rgba(0, 35, 102, 0.08);
        padding: 4px 8px;
        border-radius: 6px;
        margin-left: 8px;
        letter-spacing: 0.5px;
    }

    .rewards-forge-container {
        margin-top: 10px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .rewards-forge {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 5px;
    }

    .reward-card {
        flex: 1;
        min-width: 140px;
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 20px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .reward-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
        z-index: 0;
    }

    .reward-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.7);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        border-color: rgba(255, 255, 255, 0.9);
    }

    .reward-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    /* Color Themes */
    .reward-card.xp .reward-icon { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    .reward-card.ab .reward-icon { background: rgba(16, 185, 129, 0.15); color: #059669; }
    .reward-card.gp .reward-icon { background: rgba(245, 158, 11, 0.15); color: #d97706; }

    .reward-card.xp:hover { border-color: rgba(59, 130, 246, 0.4); }
    .reward-card.ab:hover { border-color: rgba(16, 185, 129, 0.4); }
    .reward-card.gp:hover { border-color: rgba(245, 158, 11, 0.4); }

    .reward-details {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .reward-details label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .reward-input-wrap {
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .reward-input-wrap input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        padding: 0;
        margin: 0;
        outline: none;
    }

    .reward-badge {
        font-size: 0.7rem;
        font-weight: 900;
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .reward-card.xp .reward-badge { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
    .reward-card.ab .reward-badge { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .reward-card.gp .reward-badge { background: rgba(245, 158, 11, 0.1); color: #d97706; }

    .dates {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .dates .form-group {
        flex: 1;
        min-width: 180px;
    }

    .question-options {
        display: none;
        padding: 12px;
        border-radius: 12px;
        background: rgba(255,255,255,0.85);
        border: 1px dashed rgba(148,163,184,0.9);
    }

    .option-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .option-group .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .option-group input[type="text"] {
        width: 100%;
        height: 42px;
        padding: 10px 12px;
    }

    .option-group input[type="radio"] {
        transform: scale(1.2);
        margin-left: 4px;
        cursor: pointer;
    }

    .add-option-button {
        margin-top: 8px;
        background-color: var(--primary);
        color: #fff;
        padding: 8px 14px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 5px 14px rgba(15,23,42,0.35);
        transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .add-option-button:hover {
        background-color: #001845;
        transform: translateY(-1px);
        box-shadow: 0 7px 18px rgba(15,23,42,0.4);
    }

    #identification-answer input {
        width: 100%;
        height: 42px;
    }

    .added-questions-card {
        background: rgba(241, 241, 224, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        position: relative;
    }

    .added-questions-list {
        background: rgba(15, 23, 42, 0.03);
        border-radius: 16px;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
        border: 1px inset rgba(0, 0, 0, 0.05);
    }

    .question-item {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    .question-item:hover {
        transform: translateX(5px);
        border-color: var(--accent);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }

    .question-content h4 {
        margin: 0 0 5px 0;
        color: var(--primary);
        font-size: 0.95rem;
    }

    .question-meta {
        display: flex;
        gap: 10px;
        font-size: 0.75rem;
        color: #64748b;
    }

    .meta-tag {
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(0, 35, 102, 0.05);
        font-weight: 600;
        text-transform: uppercase;
    }

    .target-audience {
        margin-top: 14px;
    }

    .audience-selects {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .audience-selects .form-group {
        flex: 1;
        min-width: 180px;
    }

    .target-tip {
        margin-top: 20px;
        padding: 15px;
        border-radius: 16px;
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.1);
        color: #1e40af;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 12px;
        line-height: 1.5;
    }

    .target-tip i {
        font-size: 1.2rem;
        color: #3b82f6;
        filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.3));
    }

    .step-footer {
        margin-top: 6px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .step-footer.between {
        justify-content: space-between;
    }

    .next-button,
    .back-button,
    .create-quest-button,
    .cancel-button {
        border: none;
        padding: 9px 18px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.18s ease;
    }

    .next-button,
    .create-quest-button {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        box-shadow: 0 7px 18px rgba(0,0,0,0.3);
    }

    .next-button:hover,
    .create-quest-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 11px 24px rgba(0,0,0,0.36);
    }

    .back-button {
        background-color: #6b7280;
        color: #f9fafb;
    }

    .back-button:hover {
        background-color: #4b5563;
    }

    .cancel-button {
        background-color: #ef4444;
        color: #fff;
    }

    .cancel-button:hover {
        background-color: #dc2626;
    }

    /* Modals */
    .modal-overlay {
        position: fixed;
        inset: 0;
        
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-box {
        background: #0b1020;
        border-radius: 18px;
        padding: 24px 26px;
        width: 700px;
        box-shadow: 0 14px 30px rgba(0,0,0,0.65);
        text-align: center;
        color: #fbfbfbff;
    }

    .modal-box h3 {
        color: var(--accent);
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .modal-box p {
        color: #cbd5f5;
        margin-bottom: 22px;
        font-size: 0.9rem;
    }

    .modal-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-ok {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 8px 16px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-cancel {
        background-color: rgba(148,163,184,0.2);
        color: #e5edff;
        padding: 8px 16px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-ok:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.5);
    }

    .btn-cancel:hover {
        background-color: rgba(148,163,184,0.35);
    }

    .modal-box {
        animation: popUp 0.25s ease;
    }

    @keyframes popUp {
        from { transform: scale(0.86); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 768px) {
        .quest-steps { gap: 10px; padding: 0 10px; }
        .step-label { display: none; }
        .form-card { padding: 25px; }
        .rewards-forge { grid-template-columns: 1fr; }
    }

    /* AI QUESTION FORGE STYLES */
    .ai-question-forge-container {
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid rgba(56, 189, 248, 0.2);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .ai-forge-inner {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .ai-forge-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--accent);
        font-size: 0.9rem;
    }
    .ai-forge-body {
        display: flex;
        gap: 10px;
    }
    .ai-forge-body input {
        flex: 1;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 12px;
        color: white;
        font-size: 0.9rem;
    }
    .btn-ai-generate-single {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        border: none;
        border-radius: 8px;
        padding: 0 20px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
        white-space: nowrap;
    }
    .btn-ai-generate-single:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.4);
    }
    .btn-ai-generate-single:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ai-mini-loader {
        margin-top: 10px;
        font-size: 0.85rem;
        color: var(--accent);
        text-align: center;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    @media (max-width: 768px) {
        .quest-steps { gap: 10px; padding: 0 10px; }
        .step-label { display: none; }
        .form-card { padding: 25px; }
        .rewards-forge { grid-template-columns: 1fr; }
        .quest-create-header { flex-direction: column; }
        .quest-create-header p { max-width: 100%; }
        .quiz-container { padding: 0; }
        .quest-steps { flex-direction: column; }
    }
    /* LEVEL PROGRESSION TRACKER */
    .level-progression-tracker {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 20px 0;
        padding: 15px;
        background: rgba(15, 23, 42, 0.05);
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .level-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        cursor: help;
        border: 3px solid transparent;
    }

    .level-dot.active {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        transform: scale(1.1);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .level-dot span {
        font-size: 1.1rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .level-dot::after {
        content: "STAGE";
        position: absolute;
        bottom: -22px;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }

    .level-dot.active::after {
        color: #16a34a;
    }

    .level-dot:not(:last-child)::before {
        content: '';
        position: absolute;
        right: -15px;
        width: 15px;
        height: 2px;
        background: #cbd5e1;
        top: 50%;
        transform: translateY(-50%);
        z-index: -1;
    }

    .level-dot.active:not(:last-child)::before {
        background: #22c55e;
    }
</style>
@endsection
