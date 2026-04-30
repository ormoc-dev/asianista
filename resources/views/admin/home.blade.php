@extends('admin.dashboard')

@section('content')

@php
    $chartLabels = ['Total Users', 'Teachers', 'Students', 'Pending', 'Lessons'];
    $chartValues = [
        (int) $totalUsers,
        (int) $teachersCount,
        (int) $studentsCount,
        (int) $pendingApprovals,
        (int) $totalLessons,
    ];
@endphp

<div class="dashboard-home">
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Welcome back, Admin</h2>
            <p>Here is a quick overview of platform activity today.</p>
        </div>
        <div class="welcome-date">
            <div class="date">{{ date('F d, Y') }}</div>
            <div class="meta">Admin Dashboard</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="value">{{ number_format($totalUsers) }}</span>
                <span class="label">Total Heroes</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-teachers">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-info">
                <span class="value">{{ number_format($teachersCount) }}</span>
                <span class="label">Active Teachers</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-students">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <span class="value">{{ number_format($studentsCount) }}</span>
                <span class="label">Students Joined</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-pending" @if($pendingApprovals > 0) style="animation: pulse 2s infinite;" @endif>
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <span class="value">{{ number_format($pendingApprovals) }}</span>
                <span class="label">Pending Approvals</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-lessons">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-info">
                <span class="value">{{ number_format($totalLessons) }}</span>
                <span class="label">Total Lessons</span>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <div class="box-header">
            <h3><i class="fas fa-chart-line"></i> Platform Snapshot</h3>
        </div>
        <div class="chart-wrap">
            <canvas
                id="adminOverviewChart"
                data-labels='@json($chartLabels)'
                data-values='@json($chartValues)'></canvas>
        </div>
    </div>

    <div class="dashboard-content-row">
        <div class="content-box">
            <div class="box-header">
                <h3><i class="fas fa-user-plus"></i> Recent Users</h3>
                <a href="{{ route('admin.user-management') }}" class="box-link">View all</a>
            </div>
            <div class="recent-users-list">
                @foreach($recentUsers as $user)
                <div class="recent-user-item">
                    <div class="user-profile">
                        <div class="user-avatar">
                            <img src="{{ asset('images/' . ($user->profile_pic ?? 'default-pp.png')) }}" alt="">
                        </div>
                        <div class="user-details">
                            <span class="name">{{ $user->name }}</span>
                            <span class="role">{{ $user->role }}</span>
                        </div>
                    </div>
                    <div class="user-meta">
                        <span class="user-badge badge-{{ $user->status ?? 'pending' }}">
                            {{ $user->status ?? 'pending' }}
                        </span>
                        <span class="user-time">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="content-box">
            <div class="box-header">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.user-management') }}" class="btn-action">
                    <i class="fas fa-user-check"></i>
                    <span>Review users</span>
                </a>
                <a href="{{ route('admin.lessons.index') }}" class="btn-action">
                    <i class="fas fa-plus-circle"></i>
                    <span>Manage lessons</span>
                </a>
                <a href="{{ route('admin.quizzes') }}" class="btn-action">
                    <i class="fas fa-file-alt"></i>
                    <span>Manage quizzes</span>
                </a>
                <a href="{{ route('admin.ai-management') }}" class="btn-action">
                    <i class="fas fa-brain"></i>
                    <span>AI management</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-home { display: flex; flex-direction: column; gap: 20px; }
    .welcome-banner {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .welcome-text h2 { font-size: 1.25rem; margin-bottom: 4px; }
    .welcome-text p { color: var(--text-secondary); font-size: 0.9rem; }
    .welcome-date { text-align: right; }
    .welcome-date .date { font-size: 1rem; font-weight: 600; }
    .welcome-date .meta { color: var(--text-muted); font-size: 0.8rem; margin-top: 2px; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin: 0;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-icon { width: 42px; height: 42px; margin: 0; border-radius: 10px; font-size: 1rem; display: flex; align-items: center; justify-content: center; }
    .stat-users { background: #dbeafe; color: #2563eb; }
    .stat-teachers { background: #ede9fe; color: #7c3aed; }
    .stat-students { background: #d1fae5; color: #059669; }
    .stat-pending { background: #fef3c7; color: #d97706; }
    .stat-lessons { background: #fee2e2; color: #dc2626; }
    .stat-info .value { display: block; font-size: 1.4rem; font-weight: 700; color: var(--text-primary); line-height: 1.1; }
    .stat-info .label { font-size: 0.78rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; }

    .chart-card, .content-box {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
    }
    .box-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .box-header h3 { display: flex; align-items: center; gap: 8px; font-size: 1rem; color: var(--text-primary); }
    .box-header h3 i { color: var(--primary); }
    .box-link { font-size: 0.82rem; text-decoration: none; color: var(--primary); }
    .chart-wrap { height: 280px; }

    .dashboard-content-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; }
    .recent-users-list, .quick-actions { display: flex; flex-direction: column; gap: 10px; }
    .recent-user-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
    }
    .user-profile { display: flex; align-items: center; gap: 10px; }
    .user-avatar { width: 34px; height: 34px; border-radius: 50%; overflow: hidden; }
    .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .user-details .name { font-size: 0.88rem; font-weight: 600; }
    .user-details .role { font-size: 0.75rem; color: var(--text-muted); text-transform: capitalize; }
    .user-meta { display: flex; align-items: center; gap: 10px; }
    .user-time { font-size: 0.75rem; color: var(--text-muted); }
    .user-badge { font-size: 0.68rem; padding: 3px 8px; border-radius: 999px; font-weight: 600; text-transform: uppercase; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .btn-action {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 0.88rem;
    }
    .btn-action:hover { border-color: #c7d2fe; background: #f8faff; }
    .btn-action i { color: var(--primary); }

    @media (max-width: 1100px) { .dashboard-content-row { grid-template-columns: 1fr; } }
    @media (max-width: 700px) {
        .welcome-banner { flex-direction: column; align-items: flex-start; gap: 8px; }
        .welcome-date { text-align: left; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 520px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('adminOverviewChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.26)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Count',
                data: values,
                borderColor: '#4f46e5',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.35,
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
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#e2e8f0',
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grace: '8%',
                    ticks: { precision: 0, color: '#64748b', font: { size: 11 } },
                    grid: { color: 'rgba(148,163,184,0.18)' },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
