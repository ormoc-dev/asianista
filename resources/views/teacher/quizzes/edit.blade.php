@extends('teacher.dashboard')

@section('content')
<div class="edit-quiz-container">

    <div class="header">
        <h2>✏️ Edit Quiz</h2>
        <a href="{{ route('teacher.quizzes') }}" class="btn-secondary">← Back to Quizzes</a>
    </div>

    <div class="form-card">
        <form action="{{ route('teacher.quizzes.update', $quiz->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Quiz Title -->
            <div class="form-group">
                <label for="title">Quiz Title <span>*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" required>
            </div>

            <!-- Questions Container -->
            <h3>Questions</h3>
            <div id="questions-wrapper">
                @foreach($quiz->questions as $qIndex => $question)
                <div class="question-card" data-index="{{ $qIndex }}">
                    <div class="form-group">
                        <label>Question <span>*</span></label>
                        <input type="text" name="questions[{{ $qIndex }}][question]" value="{{ old("questions.$qIndex.question", $question->question) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select name="questions[{{ $qIndex }}][type]" class="question-type" data-index="{{ $qIndex }}">
                            <option value="multiple_choice" {{ $question->type=='multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="identification" {{ $question->type=='identification' ? 'selected' : '' }}>Identification</option>
                        </select>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div class="options-container" style="{{ $question->type=='multiple_choice' ? '' : 'display:none;' }}">
                        @foreach(json_decode($question->options) ?? [] as $optIndex => $option)
                        <div class="form-group option-item">
                            <input type="text" name="questions[{{ $qIndex }}][options][]" value="{{ old("questions.$qIndex.options.$optIndex", $option) }}" placeholder="Option text" required>
                            <button type="button" class="remove-option-btn">❌</button>
                        </div>
                        @endforeach
                        <button type="button" class="btn-secondary add-option-btn">➕ Add Option</button>
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <input type="text" name="questions[{{ $qIndex }}][answer]" value="{{ old("questions.$qIndex.answer", $question->answer) }}">
                        </div>
                    </div>

                    <!-- Identification Answer -->
                    <div class="form-group identification-answer" style="{{ $question->type=='identification' ? '' : 'display:none;' }}">
                        <label>Answer</label>
                        <input type="text" name="questions[{{ $qIndex }}][answer]" value="{{ old("questions.$qIndex.answer", $question->answer) }}">
                    </div>

                    <button type="button" class="btn-secondary remove-question-btn">🗑 Remove Question</button>
                    <hr>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn-secondary" id="add-question-btn">➕ Add Question</button>
            <button type="submit" class="btn-primary">💾 Update Quiz</button>
        </form>
    </div>
</div>

<style>
.edit-quiz-container { max-width: 900px; margin: 0 auto; padding: 30px; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.btn-secondary { background:#e0e7ff; color:#1e3a8a; padding:6px 12px; border-radius:6px; text-decoration:none; cursor:pointer; margin-bottom:10px; }
.form-card { background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.08); }
.question-card { background:#f1f5f9; padding:15px; margin-bottom:15px; border-radius:8px; position:relative; }
input, select { width:100%; padding:8px 10px; margin-bottom:10px; border-radius:6px; border:1px solid #cbd5e1; }
.btn-primary { width:100%; background:#2563eb; color:#fff; padding:12px; border:none; border-radius:8px; cursor:pointer; margin-top:10px; }
.option-item { display:flex; align-items:center; gap:10px; }
.remove-option-btn { background:#f87171; color:white; border:none; border-radius:4px; cursor:pointer; padding:2px 6px; }
.remove-question-btn { position:absolute; top:10px; right:10px; background:#f87171; color:white; border:none; border-radius:6px; cursor:pointer; padding:4px 8px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let questionIndex = {{ $quiz->questions->count() }};

    // Add Question
    document.getElementById('add-question-btn').addEventListener('click', () => {
        const wrapper = document.getElementById('questions-wrapper');
        const qCard = document.createElement('div');
        qCard.classList.add('question-card');
        qCard.dataset.index = questionIndex;
        qCard.innerHTML = `
            <div class="form-group">
                <label>Question <span>*</span></label>
                <input type="text" name="questions[${questionIndex}][question]" required>
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
                    <input type="text" name="questions[${questionIndex}][answer]">
                </div>
            </div>
            <div class="form-group identification-answer" style="display:none;">
                <label>Answer</label>
                <input type="text" name="questions[${questionIndex}][answer]">
            </div>
            <button type="button" class="btn-secondary remove-question-btn">🗑 Remove Question</button>
            <hr>
        `;
        wrapper.appendChild(qCard);
        questionIndex++;
    });

    // Delegate remove question
    document.getElementById('questions-wrapper').addEventListener('click', e => {
        if(e.target.classList.contains('remove-question-btn')) {
            e.target.closest('.question-card').remove();
        }
        if(e.target.classList.contains('remove-option-btn')) {
            e.target.closest('.option-item').remove();
        }
        if(e.target.classList.contains('add-option-btn')) {
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

    // Toggle type
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
@endsection
