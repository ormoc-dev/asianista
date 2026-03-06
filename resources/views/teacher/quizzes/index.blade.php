@extends('teacher.dashboard')

@section('content')
<div class="quiz-container">

    <!-- Page Header -->
    <div class="page-header">
        <h2>📝 My Quizzes & Tests</h2>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn-primary">+ Create New Quiz</a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Quiz List -->
    @if($quizzes->count() > 0)
        <div class="quiz-grid">
            @foreach($quizzes as $quiz)
                <div class="quiz-card">
                    <div class="quiz-header">
                        <h3>{{ $quiz->title }}</h3>
                        <span class="badge {{ strtolower($quiz->status) }}">
                            {{ ucfirst($quiz->status) }}
                        </span>
                    </div>

                    <p class="quiz-desc">
                        {{ $quiz->description ?? 'No description provided.' }}
                    </p>

                    <div class="quiz-info">
                        <p><strong>📅 Assign Date:</strong> {{ $quiz->assign_date ? \Carbon\Carbon::parse($quiz->assign_date)->format('M d, Y') : '—' }}</p>
                        <p><strong>⏰ Due Date:</strong> {{ $quiz->due_date ? \Carbon\Carbon::parse($quiz->due_date)->format('M d, Y') : '—' }}</p>
                        <p><strong>📎 File:</strong>
                            @if($quiz->file_path)
                                <a href="{{ asset('storage/' . $quiz->file_path) }}" target="_blank" class="file-link">View File</a>
                            @else
                                <span class="text-gray">No file uploaded</span>
                            @endif
                        </p>
                    </div>

                    <div class="quiz-footer">
                        <small>Created: {{ $quiz->created_at->format('M d, Y') }}</small>
                        <div>
                            <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn-view">✏️ Edit</a>
                            <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this quiz?')">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <p>😕 No quizzes created yet.</p>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn-primary">Create Your First Quiz</a>
        </div>
    @endif
</div>

<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #1e3a8a;
        font-size: 1.6rem;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #2563eb;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.3);
    }

    /* Alert Box */
    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 5px solid #22c55e;
        font-weight: 500;
    }

    /* Quiz Grid */
    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 20px;
    }

    .quiz-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
    }

    .quiz-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
    }

    .quiz-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .quiz-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e40af;
    }

    .badge {
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge.active { background-color: #dcfce7; color: #166534; }
    .badge.pending { background-color: #fef9c3; color: #854d0e; }
    .badge.inactive { background-color: #fee2e2; color: #991b1b; }

    .quiz-desc {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 15px;
    }

    .quiz-info p {
        font-size: 0.9rem;
        color: #334155;
        margin: 5px 0;
    }

    .file-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }

    .file-link:hover {
        text-decoration: underline;
    }

    .quiz-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 10px;
    }

    .btn-view, .btn-delete {
        border: none;
        background: none;
        color: #2563eb;
        font-weight: 500;
        cursor: pointer;
        margin-left: 8px;
        transition: all 0.2s ease;
    }

    .btn-delete {
        color: #dc2626;
    }

    .btn-view:hover, .btn-delete:hover {
        text-decoration: underline;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        background-color: #fff;
        border-radius: 12px;
        padding: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .empty-state p {
        font-size: 1.1rem;
        color: #475569;
        margin-bottom: 15px;
    }
</style>
@endsection
