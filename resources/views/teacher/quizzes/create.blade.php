@extends('teacher.layouts.app')

@section('title', 'Create Quiz')
@section('page-title', 'Create Quiz')

@push('styles')
<style>
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
        <form action="{{ route('teacher.quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Quiz Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter quiz title..." required>
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
                <textarea name="description" class="form-control" rows="4" placeholder="Describe what this quiz is about...">{{ old('description') }}</textarea>
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

@push('scripts')
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
            <button type="button" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; right: 10px;" onclick="this.closest('.question-card').remove()">
                <i class="fas fa-trash"></i>
            </button>
            <div class="form-group">
                <label class="form-label">Question <span style="color: var(--danger);">*</span></label>
                <input type="text" name="questions[${questionIndex}][question]" class="form-control" placeholder="Enter question..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="questions[${questionIndex}][type]" class="form-control question-type" data-index="${questionIndex}">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="identification">Identification</option>
                </select>
            </div>
            <div class="options-container">
                <label class="form-label">Options</label>
                <div class="option-item">
                    <input type="text" name="questions[${questionIndex}][options][]" class="form-control" placeholder="Option text" required>
                    <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>
                </div>
                <button type="button" class="btn btn-sm btn-secondary add-option-btn" style="margin-top: 8px;">
                    <i class="fas fa-plus"></i> Add Option
                </button>
                <div class="form-group" style="margin-top: 12px;">
                    <label class="form-label">Correct Answer</label>
                    <input type="text" name="questions[${questionIndex}][answer]" class="form-control" placeholder="Enter correct answer">
                </div>
            </div>
            <div class="form-group identification-answer" style="display:none;">
                <label class="form-label">Answer</label>
                <input type="text" name="questions[${questionIndex}][answer]" class="form-control" placeholder="Enter answer">
            </div>
        `;
        wrapper.appendChild(qCard);
        questionIndex++;
    });

    // Delegate events
    document.getElementById('questions-wrapper').addEventListener('click', e => {
        if(e.target.closest('.remove-option-btn')) {
            e.target.closest('.option-item').remove();
        }
        if(e.target.closest('.add-option-btn')) {
            const card = e.target.closest('.question-card');
            const idx = card.dataset.index;
            const container = card.querySelector('.options-container');
            const newOpt = document.createElement('div');
            newOpt.classList.add('option-item');
            newOpt.innerHTML = `<input type="text" name="questions[${idx}][options][]" class="form-control" placeholder="Option text" required>
                                <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>`;
            container.insertBefore(newOpt, e.target.closest('.add-option-btn'));
        }
    });

    // Toggle question type
    document.getElementById('questions-wrapper').addEventListener('change', e => {
        if(e.target.classList.contains('question-type')) {
            const card = e.target.closest('.question-card');
            if(e.target.value === 'multiple_choice') {
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
@endpush
@endsection
