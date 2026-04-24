@extends('teacher.layouts.app')

@section('title', 'Add Lesson')
@section('page-title', 'Add Lesson')

@push('styles')
<style>
    @include('teacher.lessons.partials._ai_lesson_styles')
</style>
@endpush

@php
    $lessonQuestAiModels = config('services.quest_ai.models', []);
    $lessonQuestAiDefault = config('services.quest_ai.default');
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Upload New Lesson</h2>
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        @include('teacher.lessons.partials._ai_lesson_panel', [
            'lessonQuestAiModels' => $lessonQuestAiModels,
            'lessonQuestAiDefault' => $lessonQuestAiDefault,
            'grades' => $grades,
            'aiTopicInitial' => '',
        ])

        <form action="{{ route('teacher.lessons.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Lesson Title</label>
                <input type="text" name="title" id="lessonTitle" class="form-control" placeholder="Enter lesson title" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Grade <span style="color: var(--danger);">*</span></label>
                    <select name="grade_id" id="lessonGradeSelect" class="form-control" required>
                        <option value="">Select grade first</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ (string) old('grade_id') === (string) $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Section <span style="color: var(--danger);">*</span></label>
                    <select name="section_id" id="lessonSectionSelect" class="form-control" required>
                        <option value="">{{ old('grade_id') ? 'Select section' : 'Select grade first' }}</option>
                    </select>
                    @error('section_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin: -8px 0 16px;">Select a grade first; sections for that grade will load next.</p>

            <div class="form-group">
                <label class="form-label">Lesson Content</label>
                <textarea name="content" id="lessonContent" class="form-control" rows="10" placeholder="Enter lesson text here or use AI to generate..."></textarea>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">
                    Use the AI generator above to automatically create content, or write your own.
                </p>
            </div>

            <div class="form-group">
                <label class="form-label">Upload File (optional)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">Max size: 20MB. Supported: PDF, DOC, DOCX, PPT, PPTX</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Lesson
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@include('teacher.lessons.partials._ai_lesson_scripts', ['lessonQuestAiDefault' => $lessonQuestAiDefault])
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
    lessonInitAiModelPicker();
    const g = document.getElementById('lessonGradeSelect');
    const sectionSelect = document.getElementById('lessonSectionSelect');
    if (g && sectionSelect) {
        if (!g.value) {
            sectionSelect.disabled = true;
        }
        g.addEventListener('change', () => lessonLoadSections(null));
        const initial = <?php echo json_encode(old('section_id')); ?>;
        if (g.value) {
            lessonLoadSections(initial);
        }
    }
});
</script>
@endpush
