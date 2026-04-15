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

            <div class="form-group">
                <label class="form-label">Section</label>
                <select name="section" class="form-control" required>
                    <option value="">Select Section</option>
                    @php
                        $sections = ['Grade 7 - A', 'Grade 7 - B', 'Grade 8 - A', 'Grade 8 - B', 'Grade 9 - A', 'Grade 9 - B', 'Grade 10 - A', 'Grade 10 - B', 'Grade 11 - A', 'Grade 11 - B', 'Grade 12 - A', 'Grade 12 - B'];
                    @endphp
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ $lesson->section === $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>

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
