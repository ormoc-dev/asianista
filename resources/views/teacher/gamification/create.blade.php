@extends('teacher.layouts.app')

@section('title', 'Create Challenge')
@section('page-title', 'Create Challenge')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create New Challenge</h2>
        <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.gamification.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label"><i class="fas fa-trophy" style="color: var(--accent);"></i> Challenge Title</label>
                <input type="text" name="title" class="form-control" placeholder="Ex: Complete 5 Lessons" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-coins" style="color: var(--accent);"></i> Points</label>
                <input type="number" name="points" class="form-control" placeholder="Ex: 200" value="{{ old('points') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-graduation-cap"></i> Grade <span style="color: var(--danger);">*</span></label>
                    <select name="grade_id" id="gradeSelect" class="form-control" required>
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ (string) old('grade_id') === (string) $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-users"></i> Section <span style="color: var(--danger);">*</span></label>
                    <select name="section_id" id="sectionSelect" class="form-control" required>
                        <option value="">Select Section</option>
                    </select>
                    @error('section_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: -8px 0 16px;">Only students in this class will see this challenge.</p>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-align-left"></i> Description (Optional)</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe what students need to achieve...">{{ old('description') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Challenge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function gamifyLoadSections(selectedId) {
    const gradeEl = document.getElementById('gradeSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    if (!gradeEl || !sectionSelect) return;
    const gradeId = gradeEl.value;
    if (!gradeId) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        return;
    }
    sectionSelect.innerHTML = '<option value="">Loading...</option>';
    sectionSelect.disabled = true;
    fetch(`{{ url('/api/grades') }}/${gradeId}/sections`)
        .then(r => r.json())
        .then(data => {
            const sections = Array.isArray(data) ? data : [];
            let html = '<option value="">Select Section</option>';
            sections.forEach(s => {
                const sel = selectedId && String(selectedId) === String(s.id) ? ' selected' : '';
                html += `<option value="${s.id}"${sel}>${s.name}</option>`;
            });
            sectionSelect.innerHTML = html;
            sectionSelect.disabled = false;
        })
        .catch(() => {
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            sectionSelect.disabled = false;
        });
}
document.addEventListener('DOMContentLoaded', () => {
    const g = document.getElementById('gradeSelect');
    if (g) {
        g.addEventListener('change', () => gamifyLoadSections(null));
        const initial = <?php echo json_encode(old('section_id')); ?>;
        if (g.value) {
            gamifyLoadSections(initial);
        }
    }
});
</script>
@endpush
