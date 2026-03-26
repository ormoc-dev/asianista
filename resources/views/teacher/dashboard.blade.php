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
            <h3>120</h3>
            <p>Active Students</p>
        </div>
    </a>
    <a href="{{ route('teacher.quest') }}" class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-map-signs"></i>
        </div>
        <div class="stat-content">
            <h3>8</h3>
            <p>Active Quests</p>
        </div>
    </a>
    <a href="{{ route('teacher.quest') }}" class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-scroll"></i>
        </div>
        <div class="stat-content">
            <h3>27</h3>
            <p>Quests Created</p>
        </div>
    </a>
    <a href="{{ route('teacher.lessons.index') }}" class="stat-card">
        <div class="stat-icon yellow">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="stat-content">
            <h3>15</h3>
            <p>Lessons Created</p>
        </div>
    </a>
    <a href="{{ route('teacher.gamification.index') }}" class="stat-card">
        <div class="stat-icon indigo">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="stat-content">
            <h3>Top 10</h3>
            <p>Leaderboard</p>
        </div>
    </a>
    <a href="{{ route('teacher.feedback') }}" class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-comment-dots"></i>
        </div>
        <div class="stat-content">
            <h3>5</h3>
            <p>New Feedback</p>
        </div>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-bolt" style="color: var(--accent);"></i> Quick Actions</h2>
    </div>
    <div class="card-body">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('teacher.quest.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Quest
            </a>
            <a href="{{ route('teacher.lessons.create') }}" class="btn btn-secondary">
                <i class="fas fa-book"></i> Add Lesson
            </a>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-secondary">
                <i class="fas fa-clipboard"></i> Create Quiz
            </a>
            <a href="{{ route('teacher.gamification.create') }}" class="btn btn-secondary">
                <i class="fas fa-star"></i> New Challenge
            </a>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-clock" style="color: var(--info);"></i> Recent Activity</h2>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #d1fae5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p style="font-weight: 500; font-size: 0.9rem;">Quest "Algebra Adventure" completed</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">2 hours ago</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <p style="font-weight: 500; font-size: 0.9rem;">New student registered</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">5 hours ago</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <p style="font-weight: 500; font-size: 0.9rem;">Student earned 500 XP badge</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Yesterday</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-trophy" style="color: var(--accent);"></i> Top Students</h2>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-main); border-radius: var(--radius-sm);">
                    <span style="font-weight: 700; color: var(--accent); width: 24px;">1</span>
                    <img src="{{ asset('images/default-pp.png') }}" style="width: 36px; height: 36px; border-radius: 50%;">
                    <div style="flex: 1;">
                        <p style="font-weight: 500; font-size: 0.9rem;">Maria Santos</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted);">Level 12</p>
                    </div>
                    <span style="font-weight: 700; color: var(--primary);">2,450 XP</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-main); border-radius: var(--radius-sm);">
                    <span style="font-weight: 700; color: #94a3b8; width: 24px;">2</span>
                    <img src="{{ asset('images/default-pp.png') }}" style="width: 36px; height: 36px; border-radius: 50%;">
                    <div style="flex: 1;">
                        <p style="font-weight: 500; font-size: 0.9rem;">Juan Dela Cruz</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted);">Level 11</p>
                    </div>
                    <span style="font-weight: 700; color: var(--primary);">2,180 XP</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-main); border-radius: var(--radius-sm);">
                    <span style="font-weight: 700; color: #b45309; width: 24px;">3</span>
                    <img src="{{ asset('images/default-pp.png') }}" style="width: 36px; height: 36px; border-radius: 50%;">
                    <div style="flex: 1;">
                        <p style="font-weight: 500; font-size: 0.9rem;">Ana Reyes</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted);">Level 10</p>
                    </div>
                    <span style="font-weight: 700; color: var(--primary);">1,920 XP</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
