@extends('teacher.layouts.app')

@section('title', 'Add Lesson')
@section('page-title', 'Add Lesson')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Upload New Lesson</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.lessons.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Lesson Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter lesson title" required>
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Section</label>
                <select name="section" class="form-control" required>
                    <option value="">Select Section</option>
                    <option value="Grade 7 - A">Grade 7 - A</option>
                    <option value="Grade 7 - B">Grade 7 - B</option>
                    <option value="Grade 8 - A">Grade 8 - A</option>
                    <option value="Grade 8 - B">Grade 8 - B</option>
                    <option value="Grade 9 - A">Grade 9 - A</option>
                    <option value="Grade 9 - B">Grade 9 - B</option>
                    <option value="Grade 10 - A">Grade 10 - A</option>
                    <option value="Grade 10 - B">Grade 10 - B</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Lesson Content (optional)</label>
                <textarea name="content" class="form-control" rows="5" placeholder="Enter lesson text here..."></textarea>
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
