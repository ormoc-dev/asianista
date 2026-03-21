@extends('teacher.layouts.app')

@section('title', 'Edit Quiz')
@section('page-title', 'Edit Quiz')

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
        <h2 class="card-title">Edit Quiz</h2>
        <a href="{{ route('teacher.quizzes') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.quizzes.update', $quiz->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Quiz Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $quiz->title) }}" required>
            </div>

            <h3 style="font-size: 1.1rem; margin: 24px 0 16px;"><i class="fas fa-question-circle" style="color: var(--primary);"></i> Questions</h3>
            <div id="questions-wrapper">
                @foreach($quiz->questions as $qIndex => $question)
                <div class="question-card" data-index="{{ $qIndex }}">
                    <button type="button" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; right: 10px;" onclick="this.closest('.question-card').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                    <div class="form-group">
                        <label class="form-label">Question <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="questions[{{ $qIndex }}][question]" class="form-control" value="{{ old("questions.$qIndex.question", $question->question) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="questions[{{ $qIndex }}][type]" class="form-control question-type" data-index="{{ $qIndex }}">
                            <option value="multiple_choice" {{ $question->type=='multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="identification" {{ $question->type=='identification' ? 'selected' : '' }}>Identification</option>
                        </select>
                    </div>
                    <div class="options-container" style="{{ $question->type=='multiple_choice' ? '' : 'display:none;' }}">
                        <label class="form-label">Options</label>
                        @foreach(json_decode($question->options) ?? [] as $optIndex => $option)
                        <div class="option-item">
                            <input type="text" name="questions[{{ $qIndex }}][options][]" class="form-control" value="{{ old("questions.$qIndex.options.$optIndex", $option) }}" placeholder="Option text" required>
                            <button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>
                        </div>
                        @endforeach
                        <button type="button" class="btn btn-sm btn-secondary add-option-btn" style="margin-top: 8px;">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                        <div class="form-group" style="margin-top: 12px;">
                            <label class="form-label">Correct Answer</label>
                            <input type="text" name="questions[{{ $qIndex }}][answer]" class="form-control" value="{{ old("questions.$qIndex.answer", $question->answer) }}">
                        </div>
                    </div>
                    <div class="form-group identification-answer" style="{{ $question->type=='identification' ? '' : 'display:none;' }}">
                        <label class="form-label">Answer</label>
                        <input type="text" name="questions[{{ $qIndex }}][answer]" class="form-control" value="{{ old("questions.$qIndex.answer", $question->answer) }}">
                    </div>
                </div>
                @endforeach
            </div>

            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" id="add-question-btn">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.quizzes') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Quiz
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let questionIndex = {{ $quiz->questions->count() }};

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
