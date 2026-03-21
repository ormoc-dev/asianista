@extends('teacher.layouts.app')

@section('title', 'Lessons')
@section('page-title', 'Lessons')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Your Lessons</h2>
        <a href="{{ route('teacher.lessons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Lesson
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $lesson->title }}</div>
                        </td>
                        <td>{{ $lesson->section ?? 'N/A' }}</td>
                        <td>
                            @if($lesson->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($lesson->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                @if($lesson->file_path)
                                <a href="{{ route('teacher.lessons.download', basename($lesson->file_path)) }}" class="btn btn-sm btn-secondary" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                                <a href="{{ route('teacher.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('teacher.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this lesson?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No lessons yet. Click "Add Lesson" to create one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
