@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Result Header -->
    <div class="result-header">
        <div class="result-icon">
            @if($score >= 90)
                🏆
            @elseif($score >= 75)
                🌟
            @elseif($score >= 60)
                👍
            @else
                💪
            @endif
        </div>
        <h1>
            @if($score >= 90)
                Excellent!
            @elseif($score >= 75)
                Great Job!
            @elseif($score >= 60)
                Good Effort!
            @else
                Keep Trying!
            @endif
        </h1>
        <p>You completed the quiz</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <span class="stat-num">{{ $score }}%</span>
            <span class="stat-label">Score</span>
        </div>
        <div class="stat-box">
            <span class="stat-num">{{ $correctCount }}/{{ $totalQuestions }}</span>
            <span class="stat-label">Correct</span>
        </div>
        <div class="stat-box highlight">
            <span class="stat-num">+{{ $xpEarned }}</span>
            <span class="stat-label">XP Earned</span>
        </div>
    </div>

    <!-- Feedback -->
    <div class="feedback-box">
        <h3><i class="fas fa-comment"></i> Feedback</h3>
        <p>{{ $feedback }}</p>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="{{ route('student.quizzes') }}" class="btn-secondary">
            <i class="fas fa-list"></i> More Quizzes
        </a>
        <a href="{{ route('student.dashboard') }}" class="btn-primary">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</div>

<style>
    .content-wrapper {
        padding: 20px;
        margin: 0 auto;
    }

    .result-header {
        text-align: center;
        padding: 40px 20px;
        background: var(--card-bg);
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-top: 4px solid var(--accent);
    }

    .result-icon {
        font-size: 4rem;
        margin-bottom: 15px;
    }

    .result-header h1 {
        margin: 0 0 10px 0;
        color: var(--text-dark);
        font-size: 1.8rem;
    }

    .result-header p {
        margin: 0;
        color: var(--text-muted);
    }

    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-box {
        flex: 1;
        background: var(--card-bg);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-box.highlight {
        background: var(--accent);
    }

    .stat-box.highlight .stat-num,
    .stat-box.highlight .stat-label {
        color: var(--text-dark);
    }

    .stat-num {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .feedback-box {
        background: var(--card-bg);
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .feedback-box h3 {
        margin: 0 0 15px 0;
        color: var(--text-dark);
        font-size: 1.1rem;
    }

    .feedback-box h3 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .feedback-box p {
        margin: 0;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .actions {
        display: flex;
        gap: 15px;
    }

    .actions a {
        flex: 1;
        text-align: center;
        padding: 15px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: var(--text-dark);
    }

    .actions a:hover {
        opacity: 0.9;
    }

    @media (max-width: 480px) {
        .stats-row {
            flex-direction: column;
        }

        .actions {
            flex-direction: column;
        }
    }
</style>
@endsection
