@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Simple Header -->
    <div class="page-header">
        <h1><i class="fas fa-clipboard-check"></i> Quizzes</h1>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('student.quizzes.history') }}" class="btn-history">
                <i class="fas fa-history"></i> My History
            </a>
            <span class="count-badge">{{ $quizzes->count() }} Available</span>
        </div>
    </div>

    @if($quizzes->count() > 0)
        <div class="quizzes-list">
            @foreach($quizzes as $quiz)
                @php
                    $isOverdue = $quiz->due_date && $quiz->due_date < now();
                @endphp
                <div class="quiz-item {{ $isOverdue ? 'overdue' : '' }}">
                    <div class="quiz-info">
                        <h3>{{ $quiz->title }}</h3>
                        <p class="quiz-meta">
                            <span class="type-badge {{ $quiz->type ?? 'quiz' }}">
                                {{ ucfirst($quiz->type ?? 'Quiz') }}
                            </span>
                            <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions</span>
                            @if($quiz->due_date)
                                <span class="due-date {{ $isOverdue ? 'overdue' : '' }}">
                                    <i class="fas fa-clock"></i> 
                                    {{ $isOverdue ? 'Overdue: ' : 'Due: ' }}{{ $quiz->due_date->format('M d, Y') }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn-start">
                        <i class="fas fa-play"></i> Start
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No quizzes available yet.</p>
        </div>
    @endif
</div>

<style>
    .content-wrapper {
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--accent);
    }

    .page-header h1 {
        color: var(--text-dark);
        font-size: 1.5rem;
        margin: 0;
    }

    .page-header h1 i {
        color: var(--accent);
        margin-right: 10px;
    }

    .count-badge {
        background: var(--card-bg);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        color: var(--text-dark);
        border: 1px solid rgba(0,0,0,0.1);
    }

    .quizzes-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .quiz-item {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        border-left: 4px solid var(--accent);
    }

    .quiz-item:hover {
        transform: translateX(5px);
    }

    .quiz-item.overdue {
        border-left-color: #ef4444;
        background: #fef2f2;
    }

    .quiz-info h3 {
        margin: 0 0 8px 0;
        color: var(--text-dark);
        font-size: 1.1rem;
    }

    .quiz-meta {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .quiz-meta span {
        margin-right: 15px;
    }

    .type-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .type-badge.pre-test {
        background: #dbeafe;
        color: #2563eb;
    }

    .type-badge.post-test {
        background: #d1fae5;
        color: #059669;
    }

    .type-badge.quiz {
        background: #ede9fe;
        color: #7c3aed;
    }

    .due-date {
        color: var(--text-muted);
    }

    .due-date.overdue {
        color: #ef4444;
        font-weight: 600;
    }

    .btn-start {
        background: var(--primary);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: opacity 0.2s;
    }

    .btn-start:hover {
        opacity: 0.9;
    }

    .btn-history {
        background: var(--accent);
        color: var(--text-dark);
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: opacity 0.2s;
    }

    .btn-history:hover {
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }
</style>
@endsection
