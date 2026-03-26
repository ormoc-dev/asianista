@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Back Button -->
    <a href="{{ route('student.lessons') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Lessons
    </a>

    <!-- Lesson Header -->
    <div class="lesson-header-card">
        <h1>{{ $lesson->title }}</h1>
        <div class="lesson-meta">
            <span><i class="fas fa-user"></i> {{ $lesson->teacher->first_name ?? 'Teacher' }} {{ $lesson->teacher->last_name ?? '' }}</span>
            <span><i class="fas fa-calendar"></i> {{ $lesson->created_at->format('M d, Y') }}</span>
            @if($lesson->section)
                <span class="section-tag">{{ $lesson->section }}</span>
            @endif
        </div>
    </div>

    <!-- Lesson Content -->
    <div class="lesson-content-box">
        @if($lesson->content)
            <div class="lesson-text">
                {!! nl2br(e($lesson->content)) !!}
            </div>
        @else
            <div class="no-content">
                <i class="fas fa-file-alt"></i>
                <p>No written content available for this lesson.</p>
            </div>
        @endif
    </div>

    <!-- Attachments -->
    @if($lesson->file_path)
        <div class="attachment-box">
            <h3><i class="fas fa-paperclip"></i> Attachment</h3>
            <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" class="btn-download">
                <i class="fas fa-download"></i> Download File
            </a>
        </div>
    @endif
</div>

<style>
    .content-wrapper {
        padding: 20px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--accent);
    }

    .lesson-header-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 4px solid var(--accent);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .lesson-header-card h1 {
        margin: 0 0 15px 0;
        color: var(--text-dark);
        font-size: 1.6rem;
    }

    .lesson-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .lesson-meta span i {
        color: var(--accent);
        margin-right: 5px;
    }

    .section-tag {
        background: var(--accent);
        color: var(--text-dark);
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .lesson-content-box {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        min-height: 300px;
    }

    .lesson-text {
        color: var(--text-dark);
        line-height: 1.8;
        font-size: 1rem;
    }

    .lesson-text p {
        margin-bottom: 15px;
    }

    .no-content {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .no-content i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }

    .attachment-box {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .attachment-box h3 {
        margin: 0 0 15px 0;
        color: var(--text-dark);
        font-size: 1.1rem;
    }

    .attachment-box h3 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary);
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }

    .btn-download:hover {
        opacity: 0.9;
    }
</style>
@endsection
