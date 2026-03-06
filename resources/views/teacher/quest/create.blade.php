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
            <div class="form-header-icon">
                <i class="fas fa-scroll"></i>
            </div>
            <h2>Quest Details</h2>
            <p class="form-subtitle">Name your quest, describe the adventure, set difficulty and rewards.</p>
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
                    <div class="form-group">
                        <label for="difficulty">Difficulty</label>
                        <div class="difficulty-select-wrap">
                            <i class="fas fa-swords"></i>
                            <select id="difficulty">
                                <option value="easy">Easy (Warm-up)</option>
                                <option value="medium">Medium (Standard Quest)</option>
                                <option value="hard">Hard (Boss Battle)</option>
                            </select>
                        </div>
                    </div>

                    <div class="rewards">
                        <div class="reward">
                            <label for="xp-reward">
                                XP Reward
                                <span class="reward-tag xp">XP</span>
                            </label>
                            <input type="number" id="xp-reward" value="100">
                        </div>
                        <div class="reward">
                            <label for="ab-reward">
                                AB Reward
                                <span class="reward-tag ab">AB</span>
                            </label>
                            <input type="number" id="ab-reward" value="50">
                        </div>
                        <div class="reward">
                            <label for="gp-reward">
                                GP Reward
                                <span class="reward-tag gp">GP</span>
                            </label>
                            <input type="number" id="gp-reward" value="25">
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
            <hr>

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
                            <option value="multiple-choice">Multiple Choice</option>
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

<script>
let questions = [];
let optionCount = 0;
let editingIndex = null;

function toggleQuestionFields() {
    const questionType = document.getElementById('question-type').value;
    document.getElementById('multiple-choice-options').style.display = 'none';
    document.getElementById('identification-answer').style.display = 'none';
    if (questionType === 'multiple-choice') {
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

    if (!questionTextEl.checkValidity()) {
        questionTextEl.reportValidity();
        return;
    }
    if (!questionTypeEl.checkValidity()) {
        questionTypeEl.reportValidity();
        return;
    }
    if (!pointsEl.checkValidity()) {
        pointsEl.reportValidity();
        return;
    }

    const questionText = questionTextEl.value.trim();
    const questionType = questionTypeEl.value;
    const points = pointsEl.value.trim();

    let correctAnswer = "";
    let options = [];
    let correctIndex = null;

    if (questionType === 'multiple-choice') {
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
        q.points = points;
        q.answer = correctAnswer;

        if (questionType === 'multiple-choice') {
            q.options = options;
            q.correctIndex = correctIndex;
        } else {
            q.options = [];
            q.correctIndex = null;
        }

        displayAddedQuestions();
        resetQuestionForm();
        editingIndex = null;
        document.querySelector('.add-question-button').textContent = '+ Add Question to Quest';
        return;
    }

    const newQuestion = {
        id: `Q${questions.length + 1}`,
        text: questionText,
        type: questionType,
        points: points,
        answer: correctAnswer,
        options: [],
        correctIndex: null,
    };

    if (questionType === 'multiple-choice') {
        newQuestion.options = options;
        newQuestion.correctIndex = correctIndex;
    }

    questions.push(newQuestion);
    displayAddedQuestions();
    resetQuestionForm();
}

function editQuestion(index) {
    const question = questions[index];

    document.getElementById('question-text').value = question.text;
    document.getElementById('question-type').value = question.type;
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
        }
    });
}

function displayAddedQuestions() {
    const questionList = document.getElementById('questions-list');
    questionList.innerHTML = '';

    questions.forEach((question, index) => {
        const li = document.createElement('li');
        li.classList.add('question-item');
        li.innerHTML = `
            <div class="question-content">
                <strong>${question.id}:</strong> ${question.text}
                <br>
                <small>${question.type} - ${question.points} pts</small>
            </div>
            <div class="question-actions">
                <button class="edit-btn" onclick="editQuestion(${index})">✏️ Edit</button>
                <button class="delete-btn" onclick="deleteQuestion(${index})">🗑️ Delete</button>
            </div>
        `;
        questionList.appendChild(li);
    });
}

function resetQuestionForm() {
    document.getElementById('question-text').value = '';
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
    updateStepIndicator(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function previousStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
    document.getElementById(`step-${step}`).style.display = 'block';
    updateStepIndicator(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function createQuest() {
    const step3 = document.getElementById('step-3');
    const selects = step3.querySelectorAll('select');

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

    const title = document.getElementById('quest-title').value;
    const grade = document.getElementById('grade').value;
    const section = document.getElementById('section').value;
    showCustomSuccess(`Quest "${title}" created for Grade ${grade} - Section ${section}!`);
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
        max-width: 920px;
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

    .difficulty-and-rewards {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    @media (min-width: 768px) {
        .difficulty-and-rewards {
            flex-direction: row;
            gap: 18px;
        }

        .difficulty-and-rewards .form-group {
            flex: 1;
        }

        .rewards {
            flex: 1.4;
        }
    }

    .difficulty-select-wrap,
    .select-with-icon,
    .points-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 3px 6px 3px 8px;
        border-radius: 999px;
        background: rgba(255,255,255,0.7);
        border: 1px solid #d1d5db;
    }

    .difficulty-select-wrap i,
    .select-with-icon i,
    .points-input-wrap i {
        color: var(--primary);
        font-size: 0.9rem;
    }

    .difficulty-select-wrap select,
    .select-with-icon select,
    .points-input-wrap input {
        border: none;
        background: transparent;
        padding: 8px 8px 8px 2px;
        outline: none;
        font-size: 0.9rem;
        width: 100%;
    }

    .rewards {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .reward {
        flex: 1;
        min-width: 120px;
    }

    .reward-tag {
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .reward-tag.xp { background: rgba(59,130,246,0.18); color: #1d4ed8; }
    .reward-tag.ab { background: rgba(16,185,129,0.18); color: #047857; }
    .reward-tag.gp { background: rgba(234,179,8,0.18); color: #92400e; }

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
        background: rgba(15,23,42,0.9);
        color: #e5edff;
        border: 1px solid rgba(148,163,184,0.6);
    }

    .added-questions-card hr {
        border-top-color: rgba(51,65,85,0.9);
    }

    .added-questions-card .form-subtitle {
        color: #cbd5f5;
    }

    .added-questions-list {
        background: rgba(15,23,42,0.65);
        border-radius: 12px;
        padding: 12px;
        max-height: 250px;
        overflow-y: auto;
    }

    .added-questions-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .question-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(15,23,42,0.85);
        border: 1px solid rgba(148,163,184,0.7);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.35);
        font-size: 0.9rem;
    }

    .question-content small {
        color: #cbd5f5;
    }

    .question-actions {
        display: flex;
        gap: 6px;
    }

    .edit-btn,
    .delete-btn {
        border: none;
        padding: 6px 10px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .edit-btn {
        background-color: #3b82f6;
        color: white;
    }

    .edit-btn:hover {
        background-color: #2563eb;
    }

    .delete-btn {
        background-color: #ef4444;
        color: white;
    }

    .delete-btn:hover {
        background-color: #dc2626;
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
        margin-top: 14px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(0,35,102,0.08);
        color: var(--primary);
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .target-tip i {
        color: var(--accent);
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
        background: rgba(15,23,42,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .modal-box {
        background: #0b1020;
        border-radius: 18px;
        padding: 24px 26px;
        width: 340px;
        box-shadow: 0 14px 30px rgba(0,0,0,0.65);
        text-align: center;
        color: #e5edff;
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
        .quest-create-header {
            flex-direction: column;
        }

        .quest-create-header p {
            max-width: 100%;
        }

        .quiz-container {
            padding: 0;
        }

        .quest-steps {
            flex-direction: column;
        }
    }
</style>
@endsection
