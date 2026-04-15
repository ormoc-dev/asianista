@extends('admin.dashboard')

@section('content')

<style>
    .page-container {
        padding: 20px;
    }

    .page-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        font-size: 1.5rem;
        color: #3b82f6;
    }

    .page-title h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .page-title p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 4px 0 0;
    }

    .page-date {
        font-size: 0.75rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .alert {
        margin: 16px 24px;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .data-table thead {
        background: #f9fafb;
    }

    .data-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
    }

    .item-title {
        font-weight: 500;
        color: #1f2937;
    }

    .item-meta {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 2px;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

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

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-btns {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-approve {
        background: #10b981;
        color: #fff;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #ef4444;
        color: #fff;
    }

    .btn-reject:hover {
        background: #dc2626;
    }

    .btn-view {
        background: #3b82f6;
        color: #fff;
    }

    .btn-view:hover {
        background: #2563eb;
    }

    .page-footer {
        padding: 16px 24px;
        text-align: center;
        font-size: 0.8rem;
        color: #9ca3af;
        border-top: 1px solid #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: #d1d5db;
        margin-bottom: 12px;
    }
</style>

<div class="page-container">
    <div class="page-card">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-book"></i>
                <div>
                    <h2>Lessons</h2>
                    <p>View lessons uploaded by teachers</p>
                </div>
            </div>
            <span class="page-date">{{ now()->format('M d, Y') }}</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Lesson Title</th>
                        <th>Teacher</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr>
                            <td>
                                <div class="item-title">{{ $lesson->title }}</div>
                                <div class="item-meta">{{ $lesson->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <img src="{{ asset('images/' . ($lesson->teacher->profile_pic ?? 'default-pp.png')) }}" class="user-avatar" alt="">
                                    <span>{{ $lesson->teacher->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: #6b7280;">
                                    {{ $lesson->section ?? 'All Sections' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $status = $lesson->status ?? 'pending';
                                    $icon = $status === 'approved' ? 'fa-check' : ($status === 'rejected' ? 'fa-times' : 'fa-clock');
                                @endphp
                                <span class="status-badge status-{{ $status }}">
                                    <i class="fas {{ $icon }}"></i>
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.lessons.show', $lesson) }}" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($lesson->file_path)
                                        <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-view" style="background:#6366f1;">
                                            <i class="fas fa-file"></i> File
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-book-open"></i>
                                    <p>No lessons found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            Total: <strong>{{ $lessons->count() }}</strong> lessons
        </div>
    </div>
</div>

@endsection
