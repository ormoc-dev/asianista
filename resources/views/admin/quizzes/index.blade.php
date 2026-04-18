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
        color: #8b5cf6;
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

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
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

    .type-tag {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: #f3f4f6;
        color: #6b7280;
        text-transform: capitalize;
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

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
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
                <i class="fas fa-clipboard-list"></i>
                <div>
                    <h2>Quizzes</h2>
                    <p>Review and manage quizzes and assessments</p>
                </div>
            </div>
            <span class="page-date">{{ now()->format('M d, Y') }}</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Mentor</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td>
                                <div class="item-title">{{ $quiz->title }}</div>
                                <div class="item-meta">{{ $quiz->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($quiz->grade_id || $quiz->section_id)
                                    <span class="type-tag">{{ $quiz->grade->name ?? '—' }}</span>
                                    <span class="type-tag">{{ $quiz->section->name ?? '—' }}</span>
                                @else
                                    <span class="type-tag">All classes</span>
                                @endif
                            </td>
                            <td>
                                <span class="type-tag">{{ $quiz->type }}</span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <img src="{{ asset('images/' . ($quiz->teacher->profile_pic ?? 'default-pp.png')) }}" class="user-avatar" alt="">
                                    <span>{{ $quiz->teacher->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $status = $quiz->status ?? 'pending';
                                    $icon = $status === 'active' ? 'fa-check' : ($status === 'inactive' ? 'fa-times' : 'fa-clock');
                                    $displayStatus = $status === 'active' ? 'Approved' : ($status === 'inactive' ? 'Rejected' : 'Pending');
                                @endphp
                                <span class="status-badge status-{{ $status }}">
                                    <i class="fas {{ $icon }}"></i>
                                    {{ $displayStatus }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    @if($quiz->status === 'pending')
                                        <form action="{{ route('admin.quizzes.approve', $quiz->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-approve">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.quizzes.reject', $quiz->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-reject">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    @endif

                                    @if($quiz->file_path)
                                        <a href="{{ asset('storage/' . $quiz->file_path) }}" target="_blank" class="btn btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p>No quizzes found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            Total: <strong>{{ $quizzes->count() }}</strong> quizzes
        </div>
    </div>
</div>

@endsection
