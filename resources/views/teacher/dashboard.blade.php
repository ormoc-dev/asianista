@extends('teacher.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <a href="{{ route('teacher.registration') }}" class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['pending_students'] }}</h3>
            <p>Pending student approvals</p>
            <p class="stat-hint">Students you registered</p>
        </div>
    </a>
    <a href="{{ route('teacher.students.approved') }}" class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['approved_students'] }}</h3>
            <p>Approved students</p>
            <p class="stat-hint">Your roster</p>
        </div>
    </a>
    <a href="{{ route('teacher.quest') }}" class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-scroll"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['quests_created'] }}</h3>
            <p>Quests created</p>
            <p class="stat-hint">Your quests</p>
        </div>
    </a>
    <a href="{{ route('teacher.lessons.index') }}" class="stat-card">
        <div class="stat-icon yellow">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['lessons_created'] }}</h3>
            <p>Lessons created</p>
            <p class="stat-hint">Your uploads</p>
        </div>
    </a>
    <a href="{{ route('teacher.quizzes') }}" class="stat-card">
        <div class="stat-icon indigo">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['quizzes_created'] }}</h3>
            <p>Quizzes created</p>
            <p class="stat-hint">Your library</p>
        </div>
    </a>
    <a href="{{ route('teacher.quizzes') }}" class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['pending_quizzes'] }}</h3>
            <p>Quizzes pending review</p>
            <p class="stat-hint">Awaiting admin</p>
        </div>
    </a>
    <a href="{{ route('teacher.quizzes') }}" class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-bolt"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['active_quizzes'] }}</h3>
            <p>Active quizzes</p>
            <p class="stat-hint">Live for students</p>
        </div>
    </a>
</div>

@php
    $trendLabels = ['Pending', 'Approved', 'Quests', 'Lessons', 'Quizzes', 'Pending Quiz', 'Active Quiz'];
    $trendValues = [
        (int) ($stats['pending_students'] ?? 0),
        (int) ($stats['approved_students'] ?? 0),
        (int) ($stats['quests_created'] ?? 0),
        (int) ($stats['lessons_created'] ?? 0),
        (int) ($stats['quizzes_created'] ?? 0),
        (int) ($stats['pending_quizzes'] ?? 0),
        (int) ($stats['active_quizzes'] ?? 0),
    ];
@endphp

<div class="card dashboard-trend-card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-chart-line" style="color: var(--primary);"></i> Dashboard Trend
        </h2>
    </div>
    <div class="card-body">
        <p class="trend-subtitle">Quick view of your current dashboard metrics.</p>
        <div class="trend-graph-wrapper">
            <canvas
                id="dashboardTrendChart"
                class="trend-chart-canvas"
                aria-label="Dashboard trend chart"
                data-labels='@json($trendLabels)'
                data-values='@json($trendValues)'></canvas>
        </div>
        <div class="trend-legend">
            @foreach($trendLabels as $index => $label)
                <div class="trend-legend-item">
                    <span class="trend-dot"></span>
                    <span>{{ $label }}: {{ $trendValues[$index] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .stat-hint {
        margin: 6px 0 0;
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 400;
        line-height: 1.3;
    }
    .stat-icon.orange {
        background: rgba(234, 88, 12, 0.12);
        color: #ea580c;
    }
    .dashboard-trend-card {
        margin-bottom: 20px;
    }
    .trend-subtitle {
        margin: 0 0 12px;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    .trend-graph-wrapper {
        height: 280px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: linear-gradient(180deg, rgba(79, 70, 229, 0.03) 0%, rgba(255, 255, 255, 0.2) 100%);
        padding: 12px;
    }
    .trend-chart-canvas {
        width: 100%;
        height: 100%;
    }
    .trend-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        margin-top: 14px;
    }
    .trend-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-secondary);
        font-size: 0.82rem;
    }
    .trend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('dashboardTrendChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const context = canvas.getContext('2d');
    const gradient = context.createLinearGradient(0, 0, 0, canvas.height || 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

    new Chart(context, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Current Count',
                data: values,
                borderColor: '#4f46e5',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.38,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#e2e8f0',
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function (ctx) {
                            return 'Count: ' + ctx.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grace: '8%',
                    ticks: {
                        precision: 0,
                        color: '#64748b',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.18)'
                    },
                    border: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-bolt" style="color: var(--accent);"></i> Quick Actions</h2>
    </div>
    <div class="card-body">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('teacher.registration') }}" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Registration
            </a>
            <a href="{{ route('teacher.students.approved') }}" class="btn btn-secondary">
                <i class="fas fa-user-check"></i> Approved students
            </a>
            <a href="{{ route('teacher.quest.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create quest
            </a>
            <a href="{{ route('teacher.quest.clone-library') }}" class="btn btn-secondary">
                <i class="fas fa-copy"></i> Clone quest
            </a>
            <a href="{{ route('teacher.lessons.create') }}" class="btn btn-secondary">
                <i class="fas fa-book"></i> Add lesson
            </a>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-secondary">
                <i class="fas fa-clipboard"></i> Create quiz
            </a>
            <a href="{{ route('teacher.gamification.create') }}" class="btn btn-secondary">
                <i class="fas fa-star"></i> New challenge
            </a>
        </div>
    </div>
</div>

@endsection
