@extends('teacher.layouts.app')

@section('title', 'Quizzes')
@section('page-title', 'Quizzes')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Your Quizzes</h2>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Quiz
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
                        <th>Type</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $quiz->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($quiz->description, 40) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($quiz->type) }}</span>
                        </td>
                        <td>
                            @if($quiz->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </td>
                        <td>{{ $quiz->due_date ? \Carbon\Carbon::parse($quiz->due_date)->format('M d, Y') : '—' }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this quiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-clipboard" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No quizzes yet. Click "Create Quiz" to start.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
