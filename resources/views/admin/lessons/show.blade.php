@extends('admin.dashboard')

@section('content')

<style>
    .page-container { padding: 20px; }
    .page-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        max-width: 100%;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }
    .page-header h1 {
        font-size: 1.35rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 8px 0;
    }
    .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.875rem;
        color: #6b7280;
    }
    .meta-row i { color: #3b82f6; margin-right: 6px; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .body { padding: 24px; }
    .section-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .content-box {
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        min-height: 120px;
        line-height: 1.7;
        color: #374151;
        font-size: 0.9375rem;
    }
    .empty-hint {
        color: #9ca3af;
        font-style: italic;
        margin: 0;
    }
    .attachment {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .btn-back {
        background: #f3f4f6;
        color: #374151;
    }
    .btn-back:hover { background: #e5e7eb; color: #111827; }
    .btn-primary {
        background: #3b82f6;
        color: #fff;
    }
    .btn-primary:hover { background: #2563eb; }
</style>

<div class="page-container">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1>{{ $lesson->title }}</h1>
                <div class="meta-row">
                    <span>
                        <i class="fas fa-user"></i>
                        {{ $lesson->teacher->name ?? 'Unknown' }}
                    </span>
                    <span>
                        <i class="fas fa-calendar"></i>
                        {{ $lesson->created_at->format('M d, Y g:i A') }}
                    </span>
                    @if($lesson->section)
                        <span>
                            <i class="fas fa-layer-group"></i>
                            {{ $lesson->section }}
                        </span>
                    @endif
                </div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                @php
                    $status = $lesson->status ?? 'pending';
                    $icon = $status === 'approved' ? 'fa-check' : ($status === 'rejected' ? 'fa-times' : 'fa-clock');
                @endphp
                <span class="status-badge status-{{ $status }}">
                    <i class="fas {{ $icon }}"></i>
                    {{ $status }}
                </span>
                <a href="{{ route('admin.lessons.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to list
                </a>
            </div>
        </div>

        <div class="body">
            <div class="section-label">Lesson content</div>
            <div class="content-box">
                @if($lesson->content)
                    {!! nl2br(e($lesson->content)) !!}
                @else
                    <p class="empty-hint">No written content for this lesson.</p>
                @endif
            </div>

            @if($lesson->file_path)
                <div class="attachment">
                    <div class="section-label">Attachment</div>
                    <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i>
                        Open file ({{ basename($lesson->file_path) }})
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
