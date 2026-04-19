@extends('teacher.layouts.app')

@section('title', 'Clone a quest')
@section('page-title', 'Clone a quest')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
        <div>
            <h2 class="card-title"><i class="fas fa-copy" style="color: var(--primary);"></i> Quest library</h2>
            <p style="margin: 8px 0 0; color: var(--text-muted); font-size: 0.9rem; max-width: 640px;">
                These quests belong to other teachers (or shared legacy content). They do not appear on your main quest board until you clone a copy—then the new quest is yours to edit and assign.
            </p>
        </div>
        <a href="{{ route('teacher.quest') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> My quests
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quest</th>
                        <th>From</th>
                        <th>Grade &amp; section</th>
                        <th>Due</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quests as $quest)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $quest->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ \Illuminate\Support\Str::limit($quest->description, 80) }}</div>
                        </td>
                        <td>
                            @if($quest->teacher_id && $quest->teacher)
                                <span style="font-size: 0.9rem;">{{ $quest->teacher->name ?? 'Teacher #'.$quest->teacher_id }}</span>
                            @else
                                <span class="badge badge-secondary">Shared / legacy</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $quest->grade->name ?? '—' }}</span>
                            <span class="badge badge-purple">{{ $quest->section->name ?? '—' }}</span>
                        </td>
                        <td>{{ $quest->due_date ? \Carbon\Carbon::parse($quest->due_date)->format('M d, Y') : '—' }}</td>
                        <td>
                            <form action="{{ route('teacher.quest.clone', $quest) }}" method="POST" class="js-loading-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary js-loading-button" data-loading-text="Cloning...">
                                    <i class="fas fa-copy"></i> Clone
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 12px; display: block;"></i>
                            Nothing to clone yet. When other teachers publish quests, they will appear here.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
