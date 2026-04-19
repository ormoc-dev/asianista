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
</style>
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
