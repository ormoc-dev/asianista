@extends('teacher.layouts.app')

@section('title', 'Quests')
@section('page-title', 'Quests')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px;">
        <h2 class="card-title">Quest Board</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <a href="{{ route('teacher.quest.clone-library') }}" class="btn btn-secondary">
                <i class="fas fa-copy"></i> Clone from library
            </a>
            <a href="{{ route('teacher.quest.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Quest
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group" style="margin-bottom: 0;">
            <input type="text" class="form-control" placeholder="Search quests..." id="questSearch" onkeyup="filterQuests()">
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table" id="questTable">
                <thead>
                    <tr>
                        <th>Quest Title</th>
                        <th>Grade & Section</th>
                        <th>Difficulty</th>
                        <th>Rewards</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quests as $quest)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $quest->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($quest->description, 50) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $quest->grade->name ?? 'N/A' }}</span>
                            <span class="badge badge-purple">{{ $quest->section->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @php
                                $diffColors = ['easy' => 'success', 'medium' => 'warning', 'hard' => 'danger'];
                            @endphp
                            <span class="badge badge-{{ $diffColors[$quest->difficulty] ?? 'info' }}">
                                {{ ucfirst($quest->difficulty ?? 'Medium') }}
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--primary); font-weight: 600;"><i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }}</span>
                            <span style="color: var(--accent); font-weight: 600; margin-left: 8px;"><i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }}</span>
                        </td>
                        <td>
                            {{ $quest->due_date ? \Carbon\Carbon::parse($quest->due_date)->format('M d, Y') : '—' }}
                        </td>
                        <td style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <a href="{{ route('teacher.quest.show', $quest->id) }}" class="btn btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('teacher.quest.edit', $quest->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-map" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No quests created yet. Click "Create Quest" to start!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterQuests() {
    const input = document.getElementById('questSearch');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('questTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j] && cells[j].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}
</script>
@endpush
@endsection
