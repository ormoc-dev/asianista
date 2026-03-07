@extends('admin.dashboard')

@section('content')

<style>
    .dashboard-home {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .welcome-banner {
        background: linear-gradient(135deg, rgba(0,35,102,0.8), rgba(38,40,64,0.9));
        padding: 25px 35px;
        border-radius: 20px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
    }

    .welcome-text h2 {
        font-size: 1.6rem;
        margin-bottom: 5px;
    }

    .welcome-text p {
        color: #cbd5e1;
        font-size: 0.95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: radial-gradient(circle at top left, rgba(255,255,255,0.9), rgba(241,241,224,0.95));
        padding: 22px;
        border-radius: 18px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.6);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: var(--accent);
        opacity: 0.1;
        border-radius: 50%;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 15px;
    }

    .stat-users { background: rgba(59,130,246,0.15); color: #2563eb; }
    .stat-teachers { background: rgba(147,51,234,0.15); color: #9333ea; }
    .stat-students { background: rgba(16,185,129,0.15); color: #059669; }
    .stat-pending { background: rgba(245,158,11,0.15); color: #d97706; }
    .stat-lessons { background: rgba(239,68,68,0.15); color: #dc2626; }

    .stat-info .value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        display: block;
    }

    .stat-info .label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-content-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }

    .content-box {
        background: rgba(255,255,255,0.85);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.2);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
    }

    .box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(0,35,102,0.05);
    }

    .box-header h3 {
        font-size: 1.1rem;
        color: #002366;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recent-users-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .recent-user-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        background: rgba(255,255,255,0.6);
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.5);
        transition: background 0.2s;
    }

    .recent-user-item:hover {
        background: #fff;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-details .name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
        display: block;
    }

    .user-details .role {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: capitalize;
    }

    .user-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 999px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #d1fae5; color: #065f46; }

    .quick-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .btn-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        font-size: 0.9rem;
        font-weight: 500;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(255,255,255,0.8);
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: #fff;
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: var(--accent);
    }

    .btn-action i {
        font-size: 1.1rem;
        color: var(--primary);
    }

    @media (max-width: 1100px) {
        .dashboard-content-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="dashboard-home">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Welcome back, Overseer!</h2>
            <p>The realm is thriving. Here is your daily mission briefing.</p>
        </div>
        <div class="welcome-date" style="text-align:right;">
            <div style="font-size:1.2rem; font-weight:700;">{{ date('F d, Y') }}</div>
            <div style="font-size:0.85rem; color:#cbd5e1;">Level Up Admin Terminal</div>
        </div>
    </div>

    <!-- Stats Grid -->
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

    <div class="dashboard-content-row">
        <!-- Recent Heroes -->
        <div class="content-box">
            <div class="box-header">
                <h3><i class="fas fa-user-plus"></i> Newly Joined Heroes</h3>
                <a href="{{ route('admin.user-management') }}" style="font-size:0.8rem; color:var(--primary); text-decoration:none;">View All</a>
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
                            <span class="role">{{ $user->role }} • Level {{ $user->level ?? '01' }}</span>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:15px;">
                        <span class="user-badge badge-{{ $user->status ?? 'pending' }}">
                            {{ $user->status ?? 'pending' }}
                        </span>
                        <span style="font-size:0.75rem; color:#94a3b8;">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-box">
            <div class="box-header">
                <h3><i class="fas fa-bolt"></i> Realm Commands</h3>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.user-management') }}" class="btn-action">
                    <i class="fas fa-user-check"></i>
                    <span>Verify New Teachers</span>
                </a>
                <a href="{{ route('admin.lessons.index') }}" class="btn-action">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Lesson</span>
                </a>
                <a href="{{ route('admin.quizzes') }}" class="btn-action">
                    <i class="fas fa-file-alt"></i>
                    <span>Create Exam Portal</span>
                </a>
                <a href="{{ route('admin.ai-management') }}" class="btn-action">
                    <i class="fas fa-brain"></i>
                    <span>Tuning AI Assistant</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245,158,11,0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(245,158,11,0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245,158,11,0); }
}
</style>

@endsection
