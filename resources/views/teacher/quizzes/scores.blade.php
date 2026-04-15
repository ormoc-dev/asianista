@extends('teacher.layouts.app')

@section('title', 'Quiz Scores')
@section('page-title', 'Student Scores')

@push('styles')
<style>
    .scores-header {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .quiz-title-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .quiz-title-section h2 {
        margin: 0;
        color: var(--text-dark);
        font-size: 1.5rem;
    }

    .quiz-meta {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .stat-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
    }

    .stat-card.highlight {
        background: var(--primary);
        color: white;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .students-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-header {
        background: #f8fafc;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    th {
        background: #f8fafc;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    tr:hover {
        background: #fafafa;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .student-name {
        font-weight: 500;
    }

    .score-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .score-badge.excellent {
        background: #dcfce7;
        color: #166534;
    }

    .score-badge.good {
        background: #fef3c7;
        color: #92400e;
    }

    .score-badge.poor {
        background: #fee2e2;
        color: #991b1b;
    }

    .xp-badge {
        background: var(--accent);
        color: var(--text-dark);
        padding: 3px 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--card-bg);
        border-radius: 12px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 15px;
    }

    .empty-state p {
        color: var(--text-muted);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #e2e8f0;
        border-radius: 6px;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 500;
    }

    .btn-back:hover {
        background: #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="scores-header">
    <div class="quiz-title-section">
        <div>
            <h2>{{ $quiz->title }}</h2>
            <div class="quiz-meta">
                <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions</span>
                <span style="margin-left: 15px;"><i class="fas fa-tag"></i> {{ ucfirst($quiz->type) }}</span>
                @if($quiz->due_date)
                    <span style="margin-left: 15px;"><i class="fas fa-calendar"></i> Due: {{ $quiz->due_date->format('M d, Y') }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('teacher.quizzes') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card highlight">
            <span class="stat-value">{{ $totalStudents }}</span>
            <span class="stat-label">Students Taken</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $averageScore }}%</span>
            <span class="stat-label">Average Score</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $highestScore }}%</span>
            <span class="stat-label">Highest Score</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $lowestScore }}%</span>
            <span class="stat-label">Lowest Score</span>
        </div>
        <div class="stat-card">
            <span class="stat-value">{{ $passRate }}%</span>
            <span class="stat-label">Pass Rate (≥75%)</span>
        </div>
    </div>
</div>

@if($attempts->count() > 0)
    <div class="students-table">
        <div class="table-header">
            <h3><i class="fas fa-users"></i> Student Results</h3>
            <span>{{ $attempts->count() }} students</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student</th>
                    <th>Score</th>
                    <th>Correct</th>
                    <th>Date Taken</th>
                </tr>
            </thead>
            <tbody>
                @php $rank = 1; @endphp
                @foreach($attempts as $attempt)
                    <tr>
                        <td>
                            @if($rank <= 3)
                                <span style="font-size: 1.2rem;">
                                    @if($rank == 1) 🥇
                                    @elseif($rank == 2) 🥈
                                    @else 🥉
                                    @endif
                                </span>
                            @else
                                {{ $rank }}
                            @endif
                        </td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($attempt->student->firstname ?? $attempt->student->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="student-name">
                                        {{ $attempt->student->firstname }} {{ $attempt->student->lastname }}
                                    </div>
                                    <small style="color: var(--text-muted);">{{ $attempt->student->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $scoreClass = $attempt->score >= 90 ? 'excellent' : ($attempt->score >= 75 ? 'good' : 'poor');
                            @endphp
                            <span class="score-badge {{ $scoreClass }}">{{ $attempt->score }}%</span>
                        </td>
                        <td>{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</td>
                        <td>{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @php $rank++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <p>No students have taken this quiz yet.</p>
    </div>
@endif
@endsection
