@extends('teacher.layouts.app')

@section('title', 'Student Details')
@section('page-title', $student->first_name . ' ' . $student->last_name)

@section('content')
<!-- Student Info Card -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 20px;">
            <img src="{{ asset($student->profile_pic ?: 'images/default-pp.png') }}" 
                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            <div>
                <h2 style="margin: 0;">{{ $student->first_name }} {{ $student->last_name }}</h2>
                <p style="margin: 5px 0; color: var(--text-muted);">{{ $student->email }}</p>
                <span style="display: inline-block; padding: 4px 12px; background: var(--bg-main); border-radius: 12px; font-size: 0.85rem;">
                    {{ $student->character ?: 'No Class' }}
                </span>
            </div>
            <div style="margin-left: auto; display: flex; gap: 30px; text-align: center;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: 600; color: var(--accent);">{{ $student->level }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Level</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 600; color: #10b981;">{{ $student->xp }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">XP</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 600; color: #ef4444;">{{ $student->hp }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">HP</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 600; color: #3b82f6;">{{ $student->ap }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">AP</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quest Attempts -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quest History</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quest</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questAttempts as $attempt)
                    <tr>
                        <td>{{ $attempt->quest->title ?? 'Unknown Quest' }}</td>
                        <td>
                            @if($attempt->status === 'completed')
                                <span style="color: #10b981; font-weight: 500;">Completed</span>
                            @elseif($attempt->status === 'started')
                                <span style="color: #f59e0b;">In Progress</span>
                            @else
                                <span style="color: var(--text-muted);">{{ $attempt->status }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 600;">
                            @if($attempt->score)
                                {{ $attempt->score }} XP
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($attempt->completed_at)
                                {{ $attempt->completed_at->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            No quest attempts yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Random Events History -->
@if($xpHistory && $xpHistory->count() > 0)
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">Random Events History</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>XP Change</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($xpHistory as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>
                            @if($event->xp_reward > 0)
                                <span style="color: #10b981; font-weight: 600;">+{{ $event->xp_reward }} XP</span>
                            @elseif($event->xp_penalty > 0)
                                <span style="color: #ef4444; font-weight: 600;">-{{ $event->xp_penalty }} XP</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->started_at)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div style="margin-top: 20px;">
    <a href="{{ route('teacher.reports.scores') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Rankings
    </a>
</div>
@endsection
