@extends('teacher.layouts.app')

@section('title', 'Performance Analytics')

@section('content')
<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Performance Analytics</h1>
            <p>Track student progress and class performance</p>
        </div>
    </div>

    <!-- Overall Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon students"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $overallStats['total_students'] }}</span>
                <span class="stat-label">Total Students</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon quizzes"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $overallStats['total_quizzes'] }}</span>
                <span class="stat-label">Total Quizzes</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon attempts"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $overallStats['total_attempts'] }}</span>
                <span class="stat-label">Quiz Attempts</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon average"><i class="fas fa-percentage"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($overallStats['class_average'], 1) }}%</span>
                <span class="stat-label">Class Average</span>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Student Rankings -->
        <div class="content-card">
            <div class="card-header">
                <h2><i class="fas fa-trophy"></i> Student Rankings</h2>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Quizzes</th>
                            <th>Avg Score</th>
                            <th>Total XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentRankings as $index => $ranking)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="rank-badge rank-{{ $index + 1 }}">
                                            <i class="fas fa-crown"></i> {{ $index + 1 }}
                                        </span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <div class="student-info">
                                        <span class="student-name">{{ $ranking->student->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>{{ $ranking->quizzes_taken }}</td>
                                <td>
                                    <span class="score-badge {{ $ranking->average_score >= 80 ? 'high' : ($ranking->average_score >= 60 ? 'medium' : 'low') }}">
                                        {{ number_format($ranking->average_score, 1) }}%
                                    </span>
                                </td>
                                <td><span class="xp-badge">{{ $ranking->total_xp }} XP</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-cell">
                                    <i class="fas fa-inbox"></i>
                                    <p>No quiz attempts yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quiz Statistics -->
        <div class="content-card">
            <div class="card-header">
                <h2><i class="fas fa-chart-bar"></i> Quiz Statistics</h2>
            </div>
            <div class="quiz-stats-list">
                @forelse($quizStats as $stat)
                    <div class="quiz-stat-item">
                        <div class="quiz-name">{{ $stat->quiz->title ?? 'Unknown Quiz' }}</div>
                        <div class="quiz-metrics">
                            <div class="metric">
                                <span class="metric-label">Avg</span>
                                <span class="metric-value">{{ number_format($stat->average_score, 1) }}%</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">High</span>
                                <span class="metric-value high">{{ number_format($stat->highest_score, 1) }}%</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Low</span>
                                <span class="metric-value low">{{ number_format($stat->lowest_score, 1) }}%</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Attempts</span>
                                <span class="metric-value">{{ $stat->total_attempts }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No quiz statistics available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Attempts -->
    <div class="content-card mt-4">
        <div class="card-header">
            <h2><i class="fas fa-clock"></i> Recent Quiz Attempts</h2>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Quiz</th>
                        <th>Score</th>
                        <th>Correct</th>
                        <th>XP Earned</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttempts as $attempt)
                        <tr>
                            <td>{{ $attempt->student->name ?? 'Unknown' }}</td>
                            <td>{{ $attempt->quiz->title ?? 'Unknown' }}</td>
                            <td>
                                <span class="score-badge {{ $attempt->score >= 80 ? 'high' : ($attempt->score >= 60 ? 'medium' : 'low') }}">
                                    {{ $attempt->score }}%
                                </span>
                            </td>
                            <td>{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</td>
                            <td><span class="xp-badge">+{{ $attempt->xp_earned }}</span></td>
                            <td>{{ $attempt->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">
                                <i class="fas fa-inbox"></i>
                                <p>No recent attempts</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .page-container {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 1.5rem;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .page-header h1 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .stat-icon.students { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
    .stat-icon.quizzes { background: rgba(245, 158, 11, 0.1); color: var(--accent); }
    .stat-icon.attempts { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .stat-icon.average { background: rgba(59, 130, 246, 0.1); color: var(--info); }

    .stat-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .content-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
    }

    .card-header h2 {
        font-size: 1rem;
        color: var(--text-primary);
    }

    .card-header h2 i {
        color: var(--accent);
        margin-right: 8px;
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        text-align: left;
        padding: 12px 16px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        font-weight: 600;
        background: var(--bg-main);
    }

    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
    }

    .data-table tr:hover {
        background: var(--bg-main);
    }

    /* Badges */
    .rank-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .rank-badge.rank-1 { background: linear-gradient(135deg, #ffd700, #ffb700); color: #fff; }
    .rank-badge.rank-2 { background: linear-gradient(135deg, #c0c0c0, #a0a0a0); color: #fff; }
    .rank-badge.rank-3 { background: linear-gradient(135deg, #cd7f32, #b87333); color: #fff; }

    .score-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .score-badge.high { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .score-badge.medium { background: rgba(245, 158, 11, 0.1); color: var(--accent); }
    .score-badge.low { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

    .xp-badge {
        padding: 4px 10px;
        background: rgba(79, 70, 229, 0.1);
        color: var(--primary);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Quiz Stats List */
    .quiz-stats-list {
        padding: 16px;
    }

    .quiz-stat-item {
        padding: 16px;
        border-bottom: 1px solid var(--border);
    }

    .quiz-stat-item:last-child {
        border-bottom: none;
    }

    .quiz-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .quiz-metrics {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .metric {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .metric-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .metric-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .metric-value.high { color: var(--success); }
    .metric-value.low { color: var(--danger); }

    /* Empty States */
    .empty-cell,
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }

    .empty-cell i,
    .empty-state i {
        font-size: 2rem;
        margin-bottom: 12px;
        display: block;
        color: var(--text-muted);
    }

    .mt-4 {
        margin-top: 24px;
    }
</style>
@endsection
