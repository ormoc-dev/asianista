@extends('teacher.layouts.app')

@section('title', 'Gamification')
@section('page-title', 'Gamification')

@section('content')
<style>
    .gamify-lb-rank { font-weight: 700; width: 24px; display: inline-block; }
    .gamify-lb-rank--top { color: var(--accent); }
    .btn-loading-spinner {
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 50%;
        display: inline-block;
        animation: btnSpin 0.7s linear infinite;
        vertical-align: middle;
        margin-right: 6px;
    }
    @keyframes btnSpin {
        to { transform: rotate(360deg); }
    }
</style>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div>
            <h2 class="card-title">Class Leaderboard</h2>
            <p style="margin: 8px 0 0; color: var(--text-muted); font-size: 0.9rem; max-width: 720px;">
                Students shown are <strong>yours</strong> (registered under your account). Quest points in the ranking count only from <strong>your</strong> quests. Challenges below are <strong>challenges you created</strong>.
            </p>
        </div>
        <a href="{{ route('teacher.gamification.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Challenge
        </a>
    </div>
    <div class="card-body" style="padding-top: 0;">
        <form method="get" action="{{ route('teacher.gamification.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div class="form-group" style="margin: 0; min-width: 160px;">
                <label class="form-label" style="margin-bottom: 4px;">Grade</label>
                <select name="grade_id" id="filterGrade" class="form-control">
                    <option value="">All grades</option>
                    @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ (string) ($gradeId ?? '') === (string) $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin: 0; min-width: 160px;">
                <label class="form-label" style="margin-bottom: 4px;">Section</label>
                <select name="section_id" id="filterSection" class="form-control">
                    <option value="">All sections</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ (string) ($sectionId ?? '') === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
            @if($gradeId || $sectionId)
                <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
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
                <span class="gamify-lb-rank{{ $index === 0 ? ' gamify-lb-rank--top' : '' }}">{{ $index + 1 }}</span>
                <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" style="width: 36px; height: 36px; border-radius: 50%;">
                <div style="flex: 1;">
                    <p style="font-weight: 500; font-size: 0.9rem;">{{ $student->first_name }} {{ $student->last_name }}</p>
                    <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">
                        HP: <strong>{{ (int) $student->hp }}</strong> |
                        XP: <strong>{{ (int) $student->xp }}</strong> |
                        AP: <strong>{{ (int) $student->ap }}</strong>
                    </p>
                </div>
                <details>
                    <summary class="btn btn-sm btn-secondary" style="cursor: pointer; list-style: none;">Edit</summary>
                    <form method="POST" action="{{ route('teacher.gamification.students.stats.update', $student->id) }}" class="js-stats-form" style="margin-top: 8px; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); min-width: 260px; background: var(--bg-card);">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="grade_id" value="{{ $gradeId }}">
                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                        <div class="form-group" style="margin-bottom: 8px;">
                            <label class="form-label" style="margin-bottom: 4px;">HP</label>
                            <input type="number" name="hp" min="0" max="9999" value="{{ old('hp', (int) $student->hp) }}" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 8px;">
                            <label class="form-label" style="margin-bottom: 4px;">XP</label>
                            <input type="number" name="xp" min="0" max="999999" value="{{ old('xp', (int) $student->xp) }}" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label" style="margin-bottom: 4px;">AP</label>
                            <input type="number" name="ap" min="0" max="9999" value="{{ old('ap', (int) $student->ap) }}" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary js-stats-save-btn">
                            <span class="js-btn-default-text">Save</span>
                            <span class="js-btn-loading-text" style="display: none;">
                                <span class="btn-loading-spinner"></span>Saving...
                            </span>
                        </button>
                    </form>
                </details>
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
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px;">{{ $challenge->description }}</p>
                        @if($challenge->grade_id || $challenge->section_id)
                            <span class="badge badge-secondary" style="font-size: 0.75rem;">{{ $challenge->grade->name ?? '—' }}</span>
                            <span class="badge badge-info" style="font-size: 0.75rem;">{{ $challenge->section->name ?? '—' }}</span>
                        @else
                            <span class="badge badge-warning" style="font-size: 0.75rem;">All classes</span>
                        @endif
                    </div>
                    <span class="badge badge-success">+{{ $challenge->points }} pts</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-stats-form').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('.js-stats-save-btn');
            if (!button) return;

            const defaultText = button.querySelector('.js-btn-default-text');
            const loadingText = button.querySelector('.js-btn-loading-text');

            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            if (defaultText) defaultText.style.display = 'none';
            if (loadingText) loadingText.style.display = 'inline';
        });
    });

    const gradeEl = document.getElementById('filterGrade');
    const sectionEl = document.getElementById('filterSection');
    if (!gradeEl || !sectionEl) return;
    gradeEl.addEventListener('change', () => {
        const gradeId = gradeEl.value;
        if (!gradeId) {
            sectionEl.innerHTML = '<option value="">All sections</option>';
            return;
        }
        sectionEl.disabled = true;
        fetch(`{{ url('/api/grades') }}/${gradeId}/sections`)
            .then(r => r.json())
            .then(data => {
                const sections = Array.isArray(data) ? data : [];
                let html = '<option value="">All sections</option>';
                sections.forEach(s => {
                    html += `<option value="${s.id}">${s.name}</option>`;
                });
                sectionEl.innerHTML = html;
                sectionEl.disabled = false;
            })
            .catch(() => { sectionEl.disabled = false; });
    });
});
</script>
@endpush
