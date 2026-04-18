@extends('student.dashboard')

@section('content')
<div class="performance-page">
    <div class="performance-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> My Performance</h1>
            <p class="performance-sub">Quiz results, quests, and progress in one place.</p>
        </div>
        @if($performanceBand)
            <span class="performance-pill {{ $performanceBand['class'] }}">{{ $performanceBand['label'] }}</span>
        @endif
    </div>

    <div class="performance-stats-grid">
        <div class="perf-stat-card">
            <div class="perf-stat-icon"><i class="fas fa-star"></i></div>
            <div class="perf-stat-body">
                <span class="perf-stat-label">Level &amp; XP</span>
                <strong class="perf-stat-value">{{ $user->level ?? 1 }}</strong>
                <span class="perf-stat-meta">{{ number_format($user->xp ?? 0) }} total XP</span>
            </div>
        </div>
        <div class="perf-stat-card">
            <div class="perf-stat-icon quiz"><i class="fas fa-clipboard-check"></i></div>
            <div class="perf-stat-body">
                <span class="perf-stat-label">Quiz average</span>
                <strong class="perf-stat-value">{{ $avgQuizScore !== null ? $avgQuizScore . '%' : '—' }}</strong>
                <span class="perf-stat-meta">{{ $quizCount }} attempt{{ $quizCount === 1 ? '' : 's' }}</span>
            </div>
        </div>
        <div class="perf-stat-card">
            <div class="perf-stat-icon best"><i class="fas fa-trophy"></i></div>
            <div class="perf-stat-body">
                <span class="perf-stat-label">Best quiz score</span>
                <strong class="perf-stat-value">{{ $bestQuizScore !== null ? $bestQuizScore . '%' : '—' }}</strong>
                <span class="perf-stat-meta">XP from quizzes: +{{ number_format($totalQuizXp) }}</span>
            </div>
        </div>
        <div class="perf-stat-card">
            <div class="perf-stat-icon quest"><i class="fas fa-map-signs"></i></div>
            <div class="perf-stat-body">
                <span class="perf-stat-label">Quests</span>
                <strong class="perf-stat-value">{{ $questsCompleted }} done</strong>
                <span class="perf-stat-meta">{{ $questsInProgress }} in progress</span>
            </div>
        </div>
        <div class="perf-stat-card">
            <div class="perf-stat-icon hero"><i class="fas fa-heart"></i></div>
            <div class="perf-stat-body">
                <span class="perf-stat-label">Hero status</span>
                <strong class="perf-stat-value">{{ $user->hp ?? 0 }} HP</strong>
                <span class="perf-stat-meta">{{ $user->ap ?? 0 }} AP · {{ ucfirst($user->character ?? '—') }}</span>
            </div>
        </div>
    </div>

    <div class="performance-columns">
        <div class="performance-panel">
            <h2><i class="fas fa-history"></i> Recent quizzes</h2>
            @if($recentQuizzes->isEmpty())
                <div class="perf-empty">
                    <i class="fas fa-clipboard-list"></i>
                    <p>No quiz attempts yet.</p>
                    <a href="{{ route('student.quizzes') }}" class="perf-btn">Take a quiz</a>
                </div>
            @else
                <ul class="perf-list">
                    @foreach($recentQuizzes as $attempt)
                        <li class="perf-list-item">
                            <div class="perf-list-main">
                                <span class="perf-list-title">{{ $attempt->quiz->title ?? 'Quiz' }}</span>
                                <span class="perf-list-date">{{ $attempt->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="perf-list-stats">
                                <span class="perf-score {{ ($attempt->score ?? 0) >= 75 ? 'pass' : 'warn' }}">{{ $attempt->score }}%</span>
                                <span class="perf-mini">+{{ $attempt->xp_earned ?? 0 }} XP</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('student.quizzes.history') }}" class="perf-link">Full quiz history <i class="fas fa-arrow-right"></i></a>
            @endif
        </div>

        <div class="performance-panel">
            <h2><i class="fas fa-scroll"></i> Quest activity</h2>
            @if($questAttempts->isEmpty())
                <div class="perf-empty">
                    <i class="fas fa-dragon"></i>
                    <p>No quests started yet.</p>
                    <a href="{{ route('student.quest') }}" class="perf-btn">Browse quests</a>
                </div>
            @else
                <ul class="perf-list">
                    @foreach($questAttempts->take(8) as $qa)
                        <li class="perf-list-item">
                            <div class="perf-list-main">
                                <span class="perf-list-title">{{ $qa->quest->title ?? 'Quest' }}</span>
                                <span class="perf-list-date">
                                    <span class="perf-badge status-{{ $qa->status === 'completed' ? 'done' : 'active' }}">{{ ucfirst($qa->status) }}</span>
                                    @if($qa->status === 'completed' && $qa->score)
                                        · +{{ $qa->score }} XP
                                    @endif
                                </span>
                            </div>
                            <div class="perf-list-stats">
                                @if($qa->status === 'completed')
                                    <span class="perf-mini"><i class="fas fa-check-circle"></i></span>
                                @else
                                    <span class="perf-mini"><i class="fas fa-play-circle"></i> Continue</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('student.quest') }}" class="perf-link">All quests <i class="fas fa-arrow-right"></i></a>
            @endif
        </div>
    </div>
</div>

<style>
.performance-page {
    padding: 24px;
    max-width: 1200px;
    margin: 0 auto;
}
.performance-header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
}
.performance-header h1 {
    margin: 0 0 6px;
    font-size: 1.55rem;
    color: var(--text-dark);
}
.performance-header h1 i {
    color: var(--accent);
    margin-right: 10px;
}
.performance-sub {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.95rem;
}
.performance-pill {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 0.85rem;
}
.performance-pill.excellent { background: rgba(16, 185, 129, 0.15); color: #059669; }
.performance-pill.good { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
.performance-pill.average { background: rgba(245, 158, 11, 0.15); color: #d97706; }
.performance-pill.needs { background: rgba(239, 68, 68, 0.12); color: #dc2626; }

.performance-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 28px;
}
.perf-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    gap: 14px;
    align-items: center;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
}
.perf-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.perf-stat-icon.quiz { background: linear-gradient(135deg, #93c5fd, #3b82f6); color: #fff; }
.perf-stat-icon.best { background: linear-gradient(135deg, #fcd34d, #eab308); color: #422006; }
.perf-stat-icon.quest { background: linear-gradient(135deg, #c4b5fd, #7c3aed); color: #fff; }
.perf-stat-icon.hero { background: linear-gradient(135deg, #fca5a5, #ef4444); color: #fff; }
.perf-stat-label {
    display: block;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 4px;
}
.perf-stat-value {
    display: block;
    font-size: 1.25rem;
    color: var(--text-dark);
}
.perf-stat-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.performance-columns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
}
.performance-panel {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
}
.performance-panel h2 {
    margin: 0 0 16px;
    font-size: 1.1rem;
    color: var(--text-dark);
}
.performance-panel h2 i {
    color: var(--accent);
    margin-right: 8px;
}
.perf-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.perf-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.perf-list-item:last-child { border-bottom: none; }
.perf-list-title {
    font-weight: 600;
    color: var(--text-dark);
    display: block;
    font-size: 0.92rem;
}
.perf-list-date {
    font-size: 0.78rem;
    color: var(--text-muted);
}
.perf-list-stats {
    text-align: right;
    flex-shrink: 0;
}
.perf-score {
    font-weight: 800;
    font-size: 1rem;
}
.perf-score.pass { color: #059669; }
.perf-score.warn { color: #d97706; }
.perf-mini {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 2px;
}
.perf-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
}
.perf-badge.status-done { background: rgba(16, 185, 129, 0.15); color: #059669; }
.perf-badge.status-active { background: rgba(59, 130, 246, 0.15); color: #2563eb; }

.perf-empty {
    text-align: center;
    padding: 32px 16px;
    color: var(--text-muted);
}
.perf-empty i {
    font-size: 2.5rem;
    opacity: 0.35;
    margin-bottom: 12px;
    display: block;
}
.perf-btn {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 18px;
    background: linear-gradient(135deg, #ffd43b, #f59e0b);
    color: #0f172a;
    font-weight: 700;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.9rem;
}
.perf-link {
    display: inline-block;
    margin-top: 14px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
}
.perf-link:hover { text-decoration: underline; }
</style>
@endsection
