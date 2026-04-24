@extends('teacher.layouts.app')

@section('title', 'Add Lesson')
@section('page-title', 'Add Lesson')

@push('styles')
<style>
    .ai-panel {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .ai-panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .ai-panel-header i {
        color: #0ea5e9;
        font-size: 1.5rem;
    }
    .ai-panel-header h3 {
        margin: 0;
        font-size: 1rem;
        color: #0369a1;
    }
    .ai-panel-header p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }
    .ai-form-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .ai-form-row .form-group {
        flex: 1;
        min-width: 200px;
        margin-bottom: 12px;
    }
    .btn-ai {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-ai:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }
    .btn-ai:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ai-loading {
        display: none;
        align-items: center;
        gap: 8px;
        color: #0369a1;
        font-size: 0.9rem;
    }
    .ai-loading.show {
        display: flex;
    }
    .content-tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 8px;
    }
    .content-tab {
        padding: 8px 16px;
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        border-radius: 6px 6px 0 0;
        transition: all 0.2s;
    }
    .content-tab:hover {
        background: var(--bg-main);
    }
    .content-tab.active {
        background: var(--primary);
        color: #fff;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Upload New Lesson</h2>
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <!-- AI Generation Panel -->
        <div class="ai-panel">
            <div class="ai-panel-header">
                <i class="fas fa-robot"></i>
                <div>
                    <h3>AI Content Generator</h3>
                    <p>Let AI help you create lesson content (optional)</p>
                </div>
            </div>
            <div class="ai-form-row">
                <div class="form-group">
                    <label class="form-label">Topic / Subject</label>
                    <input type="text" id="aiTopic" class="form-control" placeholder="e.g., Photosynthesis, World War II">
                </div>
                <div class="form-group">
                    <label class="form-label">Grade Level</label>
                    <select id="aiGradeLevel" class="form-control">
                        <option value="general">General</option>
                        @foreach($grades as $grade)
                            <option value="grade{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Lesson Type</label>
                    <select id="aiLessonType" class="form-control">
                        <option value="lecture">Lecture</option>
                        <option value="discussion">Discussion</option>
                        <option value="activity">Activity-based</option>
                        <option value="reading">Reading</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 16px; margin-top: 8px;">
                <button type="button" class="btn-ai" id="generateAiBtn" onclick="generateLessonContent()">
                    <i class="fas fa-magic"></i> Generate with AI
                </button>
                <div class="ai-loading" id="aiLoading">
                    <i class="fas fa-spinner fa-spin"></i> Generating content...
                </div>
            </div>
        </div>

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
        const initial = <?php echo json_encode(old('section_id')); ?>;
        if (g.value) {
            lessonLoadSections(initial);
        }
    }
});

async function generateLessonContent() {
    const topic = document.getElementById('aiTopic').value.trim();
    const gradeLevel = document.getElementById('aiGradeLevel').value;
    const lessonType = document.getElementById('aiLessonType').value;
    
    if (!topic) {
        teacherNotify('Please enter a topic for the lesson.', 'warning');
        return;
    }
    
    const btn = document.getElementById('generateAiBtn');
    const loading = document.getElementById('aiLoading');
    
    btn.disabled = true;
    loading.classList.add('show');
    
    try {
        const response = await fetch("{{ route('teacher.ai.generate-lesson') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                grade_level: gradeLevel,
                lesson_type: lessonType
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            const data = result.data;
            
            // Set title
            document.getElementById('lessonTitle').value = data.title || topic;
            
            // Build content
            let content = '';
            
            if (data.objectives && data.objectives.length > 0) {
                content += 'LEARNING OBJECTIVES\n';
                data.objectives.forEach((obj, i) => {
                    content += `${i + 1}. ${obj}\n`;
                });
                content += '\n';
            }
            
            if (data.introduction) {
                content += 'INTRODUCTION\n' + data.introduction + '\n\n';
            }
            
            if (data.main_content) {
                content += 'MAIN CONTENT\n' + data.main_content + '\n\n';
            }
            
            if (data.key_points && data.key_points.length > 0) {
                content += 'KEY POINTS\n';
                data.key_points.forEach(point => {
                    content += `- ${point}\n`;
                });
                content += '\n';
            }
            
            if (data.activities && data.activities.length > 0) {
                content += 'ACTIVITIES\n';
                data.activities.forEach((act, i) => {
                    content += `${i + 1}. ${act}\n`;
                });
                content += '\n';
            }
            
            if (data.summary) {
                content += 'SUMMARY\n' + data.summary;
            }
            
            document.getElementById('lessonContent').value = content;
            
            teacherNotify('Lesson content generated successfully! Review and edit as needed.', 'success');
        } else {
            teacherNotify('Failed to generate content: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        teacherNotify('An error occurred while generating content.', 'error');
    } finally {
        btn.disabled = false;
        loading.classList.remove('show');
    }
}
</script>
@endpush
