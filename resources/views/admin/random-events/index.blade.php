@extends('admin.layouts.app')

@section('title', 'Random Events Management')
@section('page-title', 'Random Events Management')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Random Event Cards</h2>
        <a href="{{ route('admin.random-events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Event
        </a>
    </div>
    <div class="card-body">
        <p style="color: var(--text-muted); margin-bottom: 0;">
            Manage random events that teachers can draw during class. Active events are available for drawing.
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 20px;">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Target</th>
                        <th>XP</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td>{{ $event->sort_order }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $event->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($event->description, 60) }}</div>
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'positive' => 'success',
                                    'negative' => 'danger',
                                    'neutral' => 'info',
                                    'challenge' => 'warning'
                                ];
                                $typeLabels = [
                                    'positive' => 'Positive',
                                    'negative' => 'Negative',
                                    'neutral' => 'Neutral',
                                    'challenge' => 'Challenge'
                                ];
                            @endphp
                            <span class="badge badge-{{ $typeColors[$event->event_type] ?? 'info' }}">
                                {{ $typeLabels[$event->event_type] ?? $event->event_type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $targetLabels = [
                                    'single' => 'Single Player',
                                    'all' => 'All Players',
                                    'pair' => 'Pair',
                                    'random' => 'Random'
                                ];
                            @endphp
                            <span class="badge badge-purple">{{ $targetLabels[$event->target_type] ?? $event->target_type }}</span>
                        </td>
                        <td>
                            @if($event->xp_reward > 0)
                                <span style="color: #10b981; font-weight: 600;">+{{ $event->xp_reward }}</span>
                            @elseif($event->xp_penalty > 0)
                                <span style="color: #ef4444; font-weight: 600;">-{{ $event->xp_penalty }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.random-events.toggle', $event) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $event->is_active ? 'btn-success' : 'btn-secondary' }}" style="min-width: 70px;">
                                    {{ $event->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.random-events.show', $event) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.random-events.edit', $event) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.random-events.destroy', $event) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <div style="color: var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                No random events found. <a href="{{ route('admin.random-events.create') }}">Create one</a> or run the seeder.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
