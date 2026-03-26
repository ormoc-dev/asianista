@extends('teacher.layouts.app')

@section('title', 'Create Quiz')
@section('page-title', 'Create Quiz')

@push('styles')
<style>
    .ai-panel {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .ai-panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .ai-panel-header i {
        color: #0ea5e9;
        font-size: 1.5rem;
    }
    .ai-panel-header h3 {
        margin: 0;
        font-size: 1rem;
        color: #0369a1;
    }
    .ai-panel-header p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }
    .ai-form-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .ai-form-row .form-group {
        flex: 1;
        min-width: 150px;
        margin-bottom: 12px;
    }
    .btn-ai {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-ai:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }
    .btn-ai:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ai-loading {
        display: none;
        align-items: center;
        gap: 8px;
        color: #0369a1;
        font-size: 0.9rem;
    }
    .ai-loading.show {
        display: flex;
    }
    .question-card {
        background: var(--bg-main);
        padding: 20px;
        margin-bottom: 16px;
        border-radius: var(--radius-sm);
        position: relative;
        border-left: 4px solid var(--primary);
    }
    .option-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .checkbox-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create New Quiz</h2>
        <a href="{{ route('teacher.quizzes') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <!-- AI Generation Panel -->
        <div class="ai-panel">
            <div class="ai-panel-header">
                <i class="fas fa-robot"></i>
                <div>
                    <h3>AI Question Generator</h3>
                    <p>Let AI create quiz questions for you (optional)</p>
                </div>
            </div>
            <div class="ai-form-row">
                <div class="form-group">
                    <label class="form-label">Topic / Subject</label>
                    <input type="text" id="aiTopic" class="form-control" placeholder="e.g., Biology, History">
                </div>
                <div class="form-group">
                    <label class="form-label">Number of Questions</label>
                    <select id="aiNumQuestions" class="form-control">
                        <option value="3">3 Questions</option>
                        <option value="5" selected>5 Questions</option>
                        <option value="10">10 Questions</option>
                        <option value="15">15 Questions</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Difficulty</label>
                    <select id="aiDifficulty" class="form-control">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label">Question Types</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" id="typeMultiple" checked> Multiple Choice
                    </label>
                    <label>
                        <input type="checkbox" id="typeId"> Identification
                    </label>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 16px; margin-top: 8px;">
                <button type="button" class="btn-ai" id="generateAiBtn" onclick="generateQuizQuestions()">
                    <i class="fas fa-magic"></i> Generate Questions
                </button>
                <div class="ai-loading" id="aiLoading">
                    <i class="fas fa-spinner fa-spin"></i> Generating questions...
                </div>
            </div>
        </div>

        <form action="{{ route('teacher.quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Quiz Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" id="quizTitle" class="form-control" value="{{ old('title') }}" placeholder="Enter quiz title..." required>
                @error('title') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Quiz Type <span style="color: var(--danger);">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="" disabled selected>Select quiz type</option>
                    <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Regular Quiz</option>
                    <option value="pre-test" {{ old('type') == 'pre-test' ? 'selected' : '' }}>Pre-Test</option>
                    <option value="post-test" {{ old('type') == 'post-test' ? 'selected' : '' }}>Post-Test</option>
                </select>
                @error('type') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Upload File (Optional)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.docx,.pptx,.txt">
                @error('file') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Assign Date <span style="color: var(--danger);">*</span></label>
                    <input type="datetime-local" name="assign_date" class="form-control" value="{{ old('assign_date') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date <span style="color: var(--danger);">*</span></label>
                    <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Quiz Description</label>
                <textarea name="description" id="quizDescription" class="form-control" rows="3" placeholder="Describe what this quiz is about...">{{ old('description') }}</textarea>
            </div>

            <h3 style="font-size: 1.1rem; margin: 24px 0 16px;"><i class="fas fa-question-circle" style="color: var(--primary);"></i> Questions</h3>
            <div id="questions-wrapper"></div>

            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" id="add-question-btn">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.quizzes') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Quiz
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let questionIndex = 0;

// Add Question
document.getElementById('add-question-btn').addEventListener('click', () => {
    addQuestionCard();
});

function addQuestionCard(data = null) {
    const wrapper = document.getElementById('questions-wrapper');
    const qCard = document.createElement('div');
    qCard.classList.add('question-card');
    qCard.dataset.index = questionIndex;
    
    const questionText = data ? data.question : '';
    const questionType = data ? data.type : 'multiple_choice';
    const points = data ? (data.points || 10) : 10;
    
    const isIdentification = questionType === 'identification';
    const requiredAttr = isIdentification ? '' : 'required';
    
    let optionsHtml = '';
    if (data && data.options && data.options.length > 0) {
        data.options.forEach((opt, i) => {
            optionsHtml += `
                <div class="option-item">
                    <input type="text" name="questions[${questionIndex}][options][]" class="form-control" value="${opt}" placeholder="Option text" ${requiredAttr}>
                    <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>
                </div>
            `;
        });
    } else {
        optionsHtml = `
            <div class="option-item">
                <input type="text" name="questions[${questionIndex}][options][]" class="form-control" placeholder="Option text" ${requiredAttr}>
                <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>
            </div>
        `;
    }
    
    qCard.innerHTML = `
        <button type="button" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; right: 10px;" onclick="this.closest('.question-card').remove()">
            <i class="fas fa-trash"></i>
        </button>
        <div class="form-group">
            <label class="form-label">Question <span style="color: var(--danger);">*</span></label>
            <input type="text" name="questions[${questionIndex}][question]" class="form-control" value="${questionText.replace(/"/g, '&quot;')}" placeholder="Enter question..." required>
        </div>
        <div class="form-group">
            <label class="form-label">Type</label>
            <select name="questions[${questionIndex}][type]" class="form-control question-type" data-index="${questionIndex}">
                <option value="multiple_choice" ${questionType === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                <option value="identification" ${questionType === 'identification' ? 'selected' : ''}>Identification</option>
            </select>
        </div>
        <div class="options-container" style="${questionType === 'identification' ? 'display:none;' : ''}">
            <label class="form-label">Options</label>
            ${optionsHtml}
            <button type="button" class="btn btn-sm btn-secondary add-option-btn" style="margin-top: 8px;">
                <i class="fas fa-plus"></i> Add Option
            </button>
            <div class="form-group" style="margin-top: 12px;">
                <label class="form-label">Correct Answer</label>
                <input type="text" name="questions[${questionIndex}][answer]" class="form-control" value="${data ? (data.answer || '') : ''}" placeholder="Enter correct answer">
            </div>
        </div>
        <div class="form-group identification-answer" style="${questionType === 'identification' ? '' : 'display:none;'}">
            <label class="form-label">Answer</label>
            <input type="text" name="questions[${questionIndex}][answer]" class="form-control" value="${data ? (data.answer || '') : ''}" placeholder="Enter answer">
        </div>
        <div class="form-group">
            <label class="form-label">Points</label>
            <input type="number" name="questions[${questionIndex}][points]" class="form-control" value="${points}" min="1" max="100" style="width: 100px;">
        </div>
    `;
    wrapper.appendChild(qCard);
    questionIndex++;
}

// Delegate events
document.getElementById('questions-wrapper').addEventListener('click', e => {
    if(e.target.closest('.remove-option-btn')) {
        e.target.closest('.option-item').remove();
    }
    if(e.target.closest('.add-option-btn')) {
        const card = e.target.closest('.question-card');
        const idx = card.dataset.index;
        const container = card.querySelector('.options-container');
        const typeSelect = card.querySelector('.question-type');
        const isIdentification = typeSelect && typeSelect.value === 'identification';
        const requiredAttr = isIdentification ? '' : 'required';
        
        const newOpt = document.createElement('div');
        newOpt.classList.add('option-item');
        newOpt.innerHTML = `<input type="text" name="questions[${idx}][options][]" class="form-control" placeholder="Option text" ${requiredAttr}>
                            <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>`;
        container.insertBefore(newOpt, e.target.closest('.add-option-btn'));
    }
});

// Toggle question type
document.getElementById('questions-wrapper').addEventListener('change', e => {
    if(e.target.classList.contains('question-type')) {
        const card = e.target.closest('.question-card');
        const optionsContainer = card.querySelector('.options-container');
        const optionInputs = optionsContainer.querySelectorAll('input[type="text"][name*="[options]"]');
        
        if(e.target.value === 'multiple_choice') {
            optionsContainer.style.display = '';
            card.querySelector('.identification-answer').style.display = 'none';
            // Add required back to option inputs
            optionInputs.forEach(input => input.setAttribute('required', 'required'));
        } else {
            optionsContainer.style.display = 'none';
            card.querySelector('.identification-answer').style.display = '';
            // Remove required from option inputs to prevent validation errors
            optionInputs.forEach(input => input.removeAttribute('required'));
        }
    }
});

// AI Generation
async function generateQuizQuestions() {
    const topic = document.getElementById('aiTopic').value.trim();
    const numQuestions = document.getElementById('aiNumQuestions').value;
    const difficulty = document.getElementById('aiDifficulty').value;
    const includeMultiple = document.getElementById('typeMultiple').checked;
    const includeId = document.getElementById('typeId').checked;
    
    if (!topic) {
        alert('Please enter a topic for the quiz.');
        return;
    }
    
    const questionTypes = [];
    if (includeMultiple) questionTypes.push('multiple_choice');
    if (includeId) questionTypes.push('identification');
    
    if (questionTypes.length === 0) {
        alert('Please select at least one question type.');
        return;
    }
    
    const btn = document.getElementById('generateAiBtn');
    const loading = document.getElementById('aiLoading');
    
    btn.disabled = true;
    loading.classList.add('show');
    
    try {
        const response = await fetch("{{ route('teacher.ai.generate-quiz') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                num_questions: parseInt(numQuestions),
                question_types: questionTypes,
                difficulty: difficulty
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            const data = result.data;
            
            // Set title and description
            document.getElementById('quizTitle').value = data.title || topic;
            document.getElementById('quizDescription').value = data.description || '';
            
            // Clear existing questions
            document.getElementById('questions-wrapper').innerHTML = '';
            questionIndex = 0;
            
            // Add generated questions
            if (data.questions && data.questions.length > 0) {
                data.questions.forEach(q => {
                    addQuestionCard(q);
                });
            }
            
            alert(`Generated ${data.questions ? data.questions.length : 0} questions! Review and edit as needed.`);
        } else {
            alert('Failed to generate questions: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating questions.');
    } finally {
        btn.disabled = false;
        loading.classList.remove('show');
    }
}
</script>
@endpush