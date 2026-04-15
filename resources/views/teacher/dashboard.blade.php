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
            <p>Pending Student Approvals</p>
        </div>
    </a>
    <a href="{{ route('teacher.quest') }}" class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['approved_students'] }}</h3>
            <p>Approved Students</p>
        </div>
    </a>
    <a href="{{ route('teacher.quest') }}" class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-scroll"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['quests_created'] }}</h3>
            <p>Quests Created</p>
        </div>
    </a>
    <a href="{{ route('teacher.lessons.index') }}" class="stat-card">
        <div class="stat-icon yellow">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['lessons_created'] }}</h3>
            <p>Lessons Created</p>
        </div>
    </a>
    <a href="{{ route('teacher.quizzes') }}" class="stat-card">
        <div class="stat-icon indigo">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['quizzes_created'] }}</h3>
            <p>Quizzes Created</p>
        </div>
    </a>
    <a href="{{ route('teacher.quizzes') }}" class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-bolt"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['active_quizzes'] }}</h3>
            <p>Active Quizzes</p>
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

@endsection
