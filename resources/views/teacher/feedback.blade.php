@extends('teacher.layouts.app')

@section('title', 'Student Feedback')

@section('content')
<div class="page-container">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-comments"></i> Student Feedback</h1>
            <p>Review student performance and provide feedback</p>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="stats-grid">
        <div class="stat-card excellent">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $students->where('average_score', '>=', 90)->count() }}</span>
                <span class="stat-label">Excellent (90%+)</span>
            </div>
        </div>
        <div class="stat-card good">
            <div class="stat-icon"><i class="fas fa-thumbs-up"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $students->where('average_score', '>=', 75)->where('average_score', '<', 90)->count() }}</span>
                <span class="stat-label">Good (75-89%)</span>
            </div>
        </div>
        <div class="stat-card average">
            <div class="stat-icon"><i class="fas fa-minus-circle"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $students->where('average_score', '>=', 60)->where('average_score', '<', 75)->count() }}</span>
                <span class="stat-label">Average (60-74%)</span>
            </div>
        </div>
        <div class="stat-card needs">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $students->where('average_score', '<', 60)->count() }}</span>
                <span class="stat-label">Needs Help (&lt;60%)</span>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="content-card">
        <div class="card-header">
            <h2><i class="fas fa-user-graduate"></i> Student Performance Overview</h2>
            <div class="header-actions">
                <input type="text" id="searchStudent" placeholder="Search students..." class="search-input">
            </div>
        </div>
        
        <div class="students-list">
            @forelse($students as $student)
                @php
                    $performanceClass = $student->average_score >= 90 ? 'excellent' : 
                        ($student->average_score >= 75 ? 'good' : 
                        ($student->average_score >= 60 ? 'average' : 'needs'));
                    $performanceLabel = $student->average_score >= 90 ? 'Excellent' : 
                        ($student->average_score >= 75 ? 'Good' : 
                        ($student->average_score >= 60 ? 'Average' : 'Needs Help'));
                @endphp
                <div class="student-card {{ $performanceClass }}" data-name="{{ strtolower($student->name) }}">
                    <div class="student-header">
                        <div class="student-avatar">
                            <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" alt="{{ $student->name }}">
                        </div>
                        <div class="student-info">
                            <h3>{{ $student->name }}</h3>
                            <span class="student-class">{{ ucfirst($student->character ?? 'Student') }}</span>
                        </div>
                        <div class="performance-badge {{ $performanceClass }}">
                            {{ $performanceLabel }}
                        </div>
                    </div>
                    
                    <div class="student-stats">
                        <div class="stat">
                            <span class="stat-label">Average</span>
                            <span class="stat-value">{{ number_format($student->average_score, 1) }}%</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Quizzes</span>
                            <span class="stat-value">{{ $student->quizzes_taken }}</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">XP</span>
                            <span class="stat-value">{{ $student->total_xp }}</span>
                        </div>
                    </div>

                    @if($student->last_attempt)
                        <div class="last-attempt">
                            <span class="attempt-label">Last Attempt:</span>
                            <span class="attempt-quiz">{{ $student->last_attempt->quiz->title ?? 'Unknown Quiz' }}</span>
                            <span class="attempt-score {{ $student->last_attempt->score >= 60 ? 'pass' : 'fail' }}">
                                {{ $student->last_attempt->score }}%
                            </span>
                            <span class="attempt-date">{{ $student->last_attempt->created_at->diffForHumans() }}</span>
                        </div>
                    @endif

                    <div class="student-actions">
                        <button class="btn-feedback" onclick="openFeedbackModal({{ $student->id }}, '{{ addslashes($student->name) }}')">
                            <i class="fas fa-comment-dots"></i> Give Feedback
                        </button>
                        <a href="{{ route('teacher.reports.student', $student->id) }}" class="btn-view">
                            <i class="fas fa-chart-line"></i> View Report
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <p>No students found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-comment-dots"></i> Give Feedback</h3>
            <button class="btn-close" onclick="closeFeedbackModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="student-info-modal">
                <span>To:</span>
                <strong id="modalStudentName">Student Name</strong>
            </div>
            <form id="feedbackForm" action="{{ route('teacher.feedback.send') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="modalStudentId">
                
                <div class="form-group">
                    <label>Feedback Type</label>
                    <div class="feedback-types">
                        <label class="type-option">
                            <input type="radio" name="type" value="praise" checked>
                            <span class="type-label praise"><i class="fas fa-star"></i> Praise</span>
                        </label>
                        <label class="type-option">
                            <input type="radio" name="type" value="improvement">
                            <span class="type-label improvement"><i class="fas fa-arrow-up"></i> Improvement</span>
                        </label>
                        <label class="type-option">
                            <input type="radio" name="type" value="concern">
                            <span class="type-label concern"><i class="fas fa-exclamation"></i> Concern</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="feedbackMessage">Message</label>
                    <textarea name="message" id="feedbackMessage" rows="5" placeholder="Write your feedback here..." required></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeFeedbackModal()">Cancel</button>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i> Send Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .page-container {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 1.5rem;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .page-header h1 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow);
        border-left: 4px solid transparent;
    }

    .stat-card.excellent { border-left-color: #10b981; }
    .stat-card.good { border-left-color: #3b82f6; }
    .stat-card.average { border-left-color: #f59e0b; }
    .stat-card.needs { border-left-color: #ef4444; }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .stat-card.excellent .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stat-card.good .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-card.average .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-card.needs .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .stat-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    /* Content Card */
    .content-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        font-size: 1rem;
        color: var(--text-primary);
    }

    .card-header h2 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .search-input {
        padding: 8px 16px;
        border: 1px solid var(--border);
        border-radius: 20px;
        font-size: 0.9rem;
        width: 250px;
        outline: none;
    }

    .search-input:focus {
        border-color: var(--primary);
    }

    /* Students List */
    .students-list {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .student-card {
        background: var(--bg-main);
        border-radius: var(--radius);
        padding: 20px;
        border-left: 4px solid transparent;
        transition: all 0.2s ease;
    }

    .student-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-lg);
    }

    .student-card.excellent { border-left-color: #10b981; }
    .student-card.good { border-left-color: #3b82f6; }
    .student-card.average { border-left-color: #f59e0b; }
    .student-card.needs { border-left-color: #ef4444; }

    .student-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-info {
        flex: 1;
    }

    .student-info h3 {
        font-size: 1rem;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .student-class {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .performance-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .performance-badge.excellent { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .performance-badge.good { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .performance-badge.average { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .performance-badge.needs { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .student-stats {
        display: flex;
        gap: 24px;
        margin-bottom: 16px;
        padding: 12px 0;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    .student-stats .stat {
        display: flex;
        flex-direction: column;
    }

    .student-stats .stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }

    .student-stats .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .last-attempt {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .attempt-label {
        color: var(--text-secondary);
    }

    .attempt-quiz {
        color: var(--text-primary);
        font-weight: 500;
    }

    .attempt-score {
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .attempt-score.pass { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .attempt-score.fail { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .attempt-date {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .student-actions {
        display: flex;
        gap: 12px;
    }

    .btn-feedback, .btn-view {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-feedback {
        background: var(--primary);
        color: white;
        border: none;
    }

    .btn-feedback:hover {
        background: var(--primary-dark);
    }

    .btn-view {
        background: var(--bg-card);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }

    .btn-view:hover {
        background: var(--bg-main);
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-card);
        border-radius: var(--radius);
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.1rem;
        color: var(--text-primary);
    }

    .modal-header h3 i {
        color: var(--accent);
        margin-right: 8px;
    }

    .btn-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.2rem;
        cursor: pointer;
        padding: 4px;
    }

    .modal-body {
        padding: 20px;
    }

    .student-info-modal {
        margin-bottom: 20px;
        padding: 12px;
        background: var(--bg-main);
        border-radius: var(--radius-sm);
    }

    .student-info-modal span {
        color: var(--text-secondary);
    }

    .student-info-modal strong {
        color: var(--text-primary);
        margin-left: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .feedback-types {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .type-option {
        cursor: pointer;
    }

    .type-option input {
        display: none;
    }

    .type-label {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        border: 2px solid var(--border);
        transition: all 0.2s;
    }

    .type-option input:checked + .type-label.praise {
        background: rgba(16, 185, 129, 0.1);
        border-color: #10b981;
        color: #10b981;
    }

    .type-option input:checked + .type-label.improvement {
        background: rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .type-option input:checked + .type-label.concern {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        resize: vertical;
        font-family: inherit;
    }

    textarea:focus {
        outline: none;
        border-color: var(--primary);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 10px 20px;
        background: var(--bg-main);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-send {
        padding: 10px 20px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Alert */
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10b981;
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        color: var(--text-muted);
    }
</style>

<script>
    // Search functionality
    document.getElementById('searchStudent').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.student-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Modal functions
    function openFeedbackModal(studentId, studentName) {
        document.getElementById('modalStudentId').value = studentId;
        document.getElementById('modalStudentName').textContent = studentName;
        document.getElementById('feedbackModal').classList.add('active');
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('active');
        document.getElementById('feedbackForm').reset();
    }

    // Close modal on outside click
    document.getElementById('feedbackModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFeedbackModal();
        }
    });
</script>
@endsection
