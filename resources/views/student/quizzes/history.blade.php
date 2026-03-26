@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> My Quiz History</h1>
        <a href="{{ route('student.quizzes') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Quizzes
        </a>
    </div>

    @if($attempts->count() > 0)
        <div class="attempts-list">
            @foreach($attempts as $attempt)
                <div class="attempt-card">
                    <div class="attempt-info">
                        <h3>{{ $attempt->quiz->title }}</h3>
                        <span class="quiz-type">{{ ucfirst($attempt->quiz->type ?? 'Quiz') }}</span>
                        <span class="attempt-date">
                            <i class="fas fa-calendar"></i> {{ $attempt->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="attempt-stats">
                        <div class="stat score {{ $attempt->score >= 75 ? 'pass' : 'fail' }}">
                            <span class="value">{{ $attempt->score }}%</span>
                            <span class="label">Score</span>
                        </div>
                        <div class="stat">
                            <span class="value">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</span>
                            <span class="label">Correct</span>
                        </div>
                        <div class="stat xp">
                            <span class="value">+{{ $attempt->xp_earned }}</span>
                            <span class="label">XP</span>
                        </div>
                    </div>
                    <a href="{{ route('student.quizzes.result', $attempt->quiz_id) }}" class="btn-view">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $attempts->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-check"></i>
            <h2>No Quiz Attempts Yet</h2>
            <p>You haven't taken any quizzes yet. Start taking quizzes to track your progress!</p>
            <a href="{{ route('student.quizzes') }}" class="btn btn-primary">
                <i class="fas fa-play"></i> Take a Quiz
            </a>
        </div>
    @endif
</div>

<style>
    .content-wrapper {
        padding: 20px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--text-dark);
    }

    .page-header h1 i {
        color: var(--accent);
        margin-right: 10px;
    }

    .attempts-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .attempt-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        flex-wrap: wrap;
    }

    .attempt-info {
        flex: 1;
        min-width: 200px;
    }

    .attempt-info h3 {
        margin: 0 0 5px 0;
        color: var(--text-dark);
        font-size: 1.1rem;
    }

    .quiz-type {
        background: var(--primary);
        color: white;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        margin-right: 10px;
    }

    .attempt-date {
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .attempt-stats {
        display: flex;
        gap: 20px;
    }

    .stat {
        text-align: center;
        padding: 10px 20px;
        background: #f8fafc;
        border-radius: 10px;
    }

    .stat .value {
        display: block;
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stat .label {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .stat.score.pass .value {
        color: #22c55e;
    }

    .stat.score.fail .value {
        color: #ef4444;
    }

    .stat.xp .value {
        color: var(--accent);
    }

    .btn-view {
        background: var(--primary);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.2s;
    }

    .btn-view:hover {
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--card-bg);
        border-radius: 16px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .empty-state h2 {
        margin: 0 0 10px 0;
        color: var(--text-dark);
    }

    .empty-state p {
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: var(--text-dark);
    }

    @media (max-width: 600px) {
        .attempt-card {
            flex-direction: column;
            text-align: center;
        }

        .attempt-stats {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
