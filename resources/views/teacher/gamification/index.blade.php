@extends('teacher.layouts.app')

@section('title', 'Gamification')
@section('page-title', 'Gamification')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Class Leaderboard</h2>
        <a href="{{ route('teacher.gamification.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Challenge
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Leaderboard -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-medal" style="color: var(--accent);"></i> Top Students</h2>
        </div>
        <div class="card-body">
            @forelse($students as $index => $student)
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-main); border-radius: var(--radius-sm); margin-bottom: 8px;">
                <span style="font-weight: 700; width: 24px; {{ $index == 0 ? 'color: var(--accent);' : '' }}">{{ $index + 1 }}</span>
                <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" style="width: 36px; height: 36px; border-radius: 50%;">
                <div style="flex: 1;">
                    <p style="font-weight: 500; font-size: 0.9rem;">{{ $student->name }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted);">Level {{ $student->level ?? 1 }}</p>
                </div>
                <span style="font-weight: 700; color: var(--primary);">{{ $student->points_sum_value ?? 0 }} XP</span>
            </div>
            @empty
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">No students yet</p>
            @endforelse
        </div>
    </div>

    <!-- Challenges -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-bolt" style="color: var(--warning);"></i> Active Challenges</h2>
        </div>
        <div class="card-body">
            @forelse($challenges as $challenge)
            <div style="padding: 16px; background: var(--bg-main); border-radius: var(--radius-sm); margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4 style="font-weight: 600; margin-bottom: 4px;">{{ $challenge->title }}</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $challenge->description }}</p>
                    </div>
                    <span class="badge badge-success">+{{ $challenge->points }} XP</span>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <a href="{{ route('teacher.gamification.edit', $challenge->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('teacher.gamification.destroy', $challenge->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">No challenges yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
