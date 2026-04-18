@extends('teacher.layouts.app')

@section('title', 'Edit Lesson')
@section('page-title', 'Edit Lesson')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Lesson</h2>
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-group">
                <label class="form-label">Lesson Title</label>
                <input type="text" name="title" class="form-control" value="{{ $lesson->title }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Grade <span style="color: var(--danger);">*</span></label>
                    <select name="grade_id" id="lessonGradeSelect" class="form-control" required>
                        <option value="">Select grade first</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ (string) old('grade_id', $initialGradeId) === (string) $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Section <span style="color: var(--danger);">*</span></label>
                    <select name="section_id" id="lessonSectionSelect" class="form-control" required>
                        <option value="">{{ old('grade_id', $initialGradeId) ? 'Select section' : 'Select grade first' }}</option>
                    </select>
                    @error('section_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: -8px 0 16px;">Select a grade first; sections for that grade will load next.</p>

            <div class="form-group">
                <label class="form-label">Lesson Content</label>
                <textarea name="content" class="form-control" rows="8">{{ $lesson->content }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Replace File (optional)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
                @if($lesson->file_path)
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">
                    Current file: <a href="{{ route('teacher.lessons.download', basename($lesson->file_path)) }}" style="color: var(--primary);">{{ basename($lesson->file_path) }}</a>
                </p>
                @endif
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Lesson
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lessonLoadSections(selectedId) {
    const gradeEl = document.getElementById('lessonGradeSelect');
    const sectionSelect = document.getElementById('lessonSectionSelect');
    if (!gradeEl || !sectionSelect) return;
    const gradeId = gradeEl.value;
    if (!gradeId) {
        sectionSelect.innerHTML = '<option value="">Select grade first</option>';
        sectionSelect.disabled = true;
        return;
    }
    sectionSelect.disabled = false;
    sectionSelect.innerHTML = '<option value="">Loading...</option>';
    fetch(`{{ url('/api/grades') }}/${gradeId}/sections`)
        .then(r => r.json())
        .then(data => {
            const sections = Array.isArray(data) ? data : [];
            let html = '<option value="">Select section</option>';
            sections.forEach(s => {
                const sel = selectedId != null && String(selectedId) === String(s.id) ? ' selected' : '';
                html += `<option value="${s.id}"${sel}>${s.name}</option>`;
            });
            sectionSelect.innerHTML = html;
        })
        .catch(() => {
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
        });
}
document.addEventListener('DOMContentLoaded', () => {
    const g = document.getElementById('lessonGradeSelect');
    const sectionSelect = document.getElementById('lessonSectionSelect');
    if (g && sectionSelect) {
        if (!g.value) {
            sectionSelect.disabled = true;
        }
        g.addEventListener('change', () => lessonLoadSections(null));
        const initial = <?php echo json_encode(old('section_id', $initialSectionId)); ?>;
        if (g.value) {
            lessonLoadSections(initial);
        }
    }
});
</script>
@endpush
