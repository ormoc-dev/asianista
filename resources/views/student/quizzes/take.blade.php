@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Back Button -->
    <a href="{{ route('student.quizzes') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Quizzes
    </a>

    <!-- Quiz Header -->
    <div class="quiz-header-card">
        <h1>{{ $quiz->title }}</h1>
        @if($quiz->description)
            <p>{{ $quiz->description }}</p>
        @endif
        <div class="quiz-meta">
            <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions</span>
            <span><i class="fas fa-tag"></i> {{ ucfirst($quiz->type ?? 'Quiz') }}</span>
        </div>
    </div>

    @if($quiz->questions->count() > 0)
        <form action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST">
            @csrf
            
            @foreach($quiz->questions as $index => $question)
                <div class="question-box">
                    <div class="question-header">
                        <span class="q-number">Question {{ $index + 1 }}</span>
                    </div>
                    
                    <p class="question-text">{{ $question->question }}</p>
                    
                    @if($question->type == 'multiple_choice')
                        @php
                            $choices = $question->choices;
                            if (is_string($choices)) {
                                $choices = json_decode($choices, true) ?? [];
                            }
                            $choices = $choices ?? [];
                        @endphp
                        <div class="options-list">
                            @foreach($choices as $key => $option)
                                <label class="option-item">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required>
                                    <span class="option-label">{{ chr(65 + $loop->index) }}</span>
                                    <span class="option-text">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->type == 'true_false')
                        <div class="true-false">
                            <label class="tf-option">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" required>
                                <span>True</span>
                            </label>
                            <label class="tf-option">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" required>
                                <span>False</span>
                            </label>
                        </div>
                    @elseif($question->type == 'identification')
                        <div class="identification-answer">
                            <input type="text" name="answers[{{ $question->id }}]" class="form-control" placeholder="Type your answer here..." required style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem;">
                        </div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" class="essay-box" placeholder="Type your answer here..." required></textarea>
                    @endif
                </div>
            @endforeach

            <div class="submit-area">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Submit Quiz
                </button>
            </div>
        </form>
    @else
        <div class="empty-box">
            <i class="fas fa-exclamation-circle"></i>
            <p>No questions available for this quiz.</p>
        </div>
    @endif
</div>

<style>
    .content-wrapper {
        padding: 20px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--accent);
    }

    .quiz-header-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 4px solid var(--accent);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .quiz-header-card h1 {
        margin: 0 0 10px 0;
        color: var(--text-dark);
        font-size: 1.4rem;
    }

    .quiz-header-card p {
        margin: 0 0 15px 0;
        color: var(--text-muted);
    }

    .quiz-meta {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .quiz-meta span i {
        color: var(--accent);
        margin-right: 5px;
    }

    .question-box {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .question-header {
        margin-bottom: 15px;
    }

    .q-number {
        background: var(--primary);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .question-text {
        font-size: 1.1rem;
        color: var(--text-dark);
        margin: 0 0 20px 0;
        line-height: 1.5;
    }

    .options-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .option-item:hover {
        border-color: var(--accent);
        background: #fffbeb;
    }

    .option-item input {
        display: none;
    }

    .option-label {
        width: 30px;
        height: 30px;
        background: #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--text-muted);
        transition: all 0.2s;
    }

    .option-item input:checked + .option-label {
        background: var(--accent);
        color: var(--text-dark);
    }

    .option-text {
        flex: 1;
        color: var(--text-dark);
    }

    .true-false {
        display: flex;
        gap: 15px;
    }

    .tf-option {
        flex: 1;
        padding: 15px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tf-option:hover {
        border-color: var(--accent);
    }

    .tf-option input {
        display: none;
    }

    .tf-option input:checked + span {
        color: var(--primary);
        font-weight: 600;
    }

    .essay-box {
        width: 100%;
        min-height: 150px;
        padding: 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 1rem;
        resize: vertical;
        transition: border-color 0.2s;
    }

    .essay-box:focus {
        outline: none;
        border-color: var(--accent);
    }

    .submit-area {
        text-align: center;
        margin-top: 30px;
        padding: 30px;
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-submit {
        background: var(--primary);
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .btn-submit:hover {
        opacity: 0.9;
    }

    .empty-box {
        text-align: center;
        padding: 50px;
        color: var(--text-muted);
    }

    .empty-box i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }
</style>
@endsection
