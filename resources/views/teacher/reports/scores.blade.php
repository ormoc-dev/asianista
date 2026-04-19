@extends('teacher.layouts.app')

@section('title', 'Student Scores')
@section('page-title', 'Student Scores')

@section('content')
<p style="margin: 0 0 16px; color: var(--text-muted); font-size: 0.95rem;">
    Students listed are <strong>those you registered</strong>. Quest activity columns include only attempts on <strong>your</strong> quests.
</p>
<!-- Simple Stats -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body">
        <form method="get" action="{{ route('teacher.reports.scores') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 20px;">
            <div class="form-group" style="margin: 0; min-width: 160px;">
                <label class="form-label" style="margin-bottom: 4px;">Grade</label>
                <select name="grade_id" id="reportGrade" class="form-control">
                    <option value="">All grades</option>
                    @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ (string) ($gradeId ?? '') === (string) $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin: 0; min-width: 160px;">
                <label class="form-label" style="margin-bottom: 4px;">Section</label>
                <select name="section_id" id="reportSection" class="form-control">
                    <option value="">All sections</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" {{ (string) ($sectionId ?? '') === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
            @if($gradeId || $sectionId)
                <a href="{{ route('teacher.reports.scores') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div>
                <span style="color: var(--text-muted); font-size: 0.9rem;">Students</span>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ $classAverage['total_students'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Student Scores Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Student Rankings</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>HP</th>
                        <th>Quests Done</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    @php
                        $stats = $questStats->get($student->id);
                        $rank = $index + 1;
                    @endphp
                    <tr>
                        <td>{{ $rank }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ asset($student->profile_pic ?: 'images/default-pp.png') }}" 
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 500;">{{ $student->first_name }} {{ $student->last_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->grade->name ?? '—' }}</td>
                        <td>{{ $student->section->name ?? '—' }}</td>
                        <td>{{ $student->hp }}</td>
                        <td>{{ $stats ? $stats->completed_quests : 0 }}</td>
                        <td>
                            <a href="{{ route('teacher.reports.student', $student) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const gradeEl = document.getElementById('reportGrade');
    const sectionEl = document.getElementById('reportSection');
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
