@extends('teacher.dashboard')

@section('content')
<div class="create-quiz-container">

    <!-- Header -->
    <div class="header">
        <h2>🧩 Create New Quiz</h2>
        <a href="{{ route('teacher.quizzes') }}" class="btn-secondary">← Back to Quizzes</a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('teacher.quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Quiz Title -->
            <div class="form-group">
                <label for="title">Quiz Title <span>*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Enter quiz title..." required>
                @error('title') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <!-- Quiz Type -->
            <div class="form-group">
                <label for="type">Quiz Type <span>*</span></label>
                <select id="type" name="type" required>
                    <option value="" disabled selected>Select quiz type</option>
                    <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Regular Quiz</option>
                    <option value="pre-test" {{ old('type') == 'pre-test' ? 'selected' : '' }}>Pre-Test</option>
                    <option value="post-test" {{ old('type') == 'post-test' ? 'selected' : '' }}>Post-Test</option>
                </select>
                @error('type') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label for="file">Upload File (Optional)</label>
                <input type="file" id="file" name="file" accept=".pdf,.docx,.pptx,.txt">
                @error('file') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <!-- Assign & Due Dates -->
            <div class="form-group-dates">
                <div class="form-group">
                    <label for="assign_date">Assign Date <span>*</span></label>
                    <input type="datetime-local" id="assign_date" name="assign_date" value="{{ old('assign_date') }}" required>
                    @error('assign_date') <small class="error-text">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date <span>*</span></label>
                    <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                    @error('due_date') <small class="error-text">{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Quiz Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe what this quiz is about...">{{ old('description') }}</textarea>
                @error('description') <small class="error-text">{{ $message }}</small> @enderror
            </div>

            <!-- Questions -->
            <h3>Questions</h3>
            <div id="questions-wrapper"></div>

            <button type="button" class="btn-secondary" id="add-question-btn">➕ Add Question</button>
            <button type="submit" class="btn-primary">💾 Save Quiz</button>
        </form>
    </div>
</div>

<style>
.create-quiz-container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #f9fafb;
    padding: 30px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.header h2 {
    color: #1e3a8a;
    font-size: 1.8rem;
    font-weight: 700;
}

.btn-secondary {
    background-color: #e0e7ff;
    color: #1e3a8a;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-secondary:hover {
    background-color: #c7d2fe;
    transform: translateX(-2px);
}

.form-card {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 25px 30px;
    transition: all 0.3s ease;
}

.form-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group-dates {
    display: flex;
    gap: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #1e3a8a;
}

.form-group label span {
    color: #dc2626;
}

input[type="text"],
input[type="file"],
input[type="datetime-local"],
textarea,
select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-color: #f8fafc;
}

input:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    background-color: #fff;
}

textarea {
    resize: none;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 15px;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    transform: translateY(-2px);
}

.error-text {
    color: #dc2626;
    font-size: 0.85rem;
}

/* Questions styling */
.question-card {
    background:#f9fafb;
    padding:20px;
    margin-bottom:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    position:relative;
}

.option-item {
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:8px;
}

.remove-option-btn {
    background:#f87171;
    color:white;
    border:none;
    border-radius:4px;
    cursor:pointer;
    padding:3px 8px;
}

.remove-question-btn {
    position:absolute;
    top:10px;
    right:10px;
    background:#f87171;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    padding:4px 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let questionIndex = 0;

    // Add Question
    document.getElementById('add-question-btn').addEventListener('click', () => {
        const wrapper = document.getElementById('questions-wrapper');
        const qCard = document.createElement('div');
        qCard.classList.add('question-card');
        qCard.dataset.index = questionIndex;
        qCard.innerHTML = `
            <div class="form-group">
                <label>Question <span>*</span></label>
                <input type="text" name="questions[${questionIndex}][question]" placeholder="Enter question..." required>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="questions[${questionIndex}][type]" class="question-type" data-index="${questionIndex}">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="identification">Identification</option>
                </select>
            </div>
            <div class="options-container">
                <div class="form-group option-item">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Option text" required>
                    <button type="button" class="remove-option-btn">❌</button>
                </div>
                <button type="button" class="btn-secondary add-option-btn">➕ Add Option</button>
                <div class="form-group">
                    <label>Correct Answer</label>
                    <input type="text" name="questions[${questionIndex}][answer]" placeholder="Enter correct answer">
                </div>
            </div>
            <div class="form-group identification-answer" style="display:none;">
                <label>Answer</label>
                <input type="text" name="questions[${questionIndex}][answer]" placeholder="Enter answer">
            </div>
            <button type="button" class="btn-secondary remove-question-btn">🗑 Remove Question</button>
        `;
        wrapper.appendChild(qCard);
        questionIndex++;
    });

    // Delegate remove question/option/add option
    document.getElementById('questions-wrapper').addEventListener('click', e => {
        if(e.target.classList.contains('remove-question-btn')){
            e.target.closest('.question-card').remove();
        }
        if(e.target.classList.contains('remove-option-btn')){
            e.target.closest('.option-item').remove();
        }
        if(e.target.classList.contains('add-option-btn')){
            const card = e.target.closest('.question-card');
            const idx = card.dataset.index;
            const container = card.querySelector('.options-container');
            const newOpt = document.createElement('div');
            newOpt.classList.add('form-group','option-item');
            newOpt.innerHTML = `<input type="text" name="questions[${idx}][options][]" placeholder="Option text" required>
                                <button type="button" class="remove-option-btn">❌</button>`;
            container.insertBefore(newOpt, e.target);
        }
    });

    // Toggle question type
    document.getElementById('questions-wrapper').addEventListener('change', e => {
        if(e.target.classList.contains('question-type')){
            const card = e.target.closest('.question-card');
            if(e.target.value === 'multiple_choice'){
                card.querySelector('.options-container').style.display = '';
                card.querySelector('.identification-answer').style.display = 'none';
            } else {
                card.querySelector('.options-container').style.display = 'none';
                card.querySelector('.identification-answer').style.display = '';
            }
        }
    });
});
</script>
@endsection
