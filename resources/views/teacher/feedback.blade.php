@extends('teacher.layouts.app')

@section('title', 'Student Feedback')

@section('content')
<div class="page-container">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success teacher-flash-auto" data-teacher-flash role="status">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-comments"></i> Student Feedback</h1>
            <p>Review student performance and provide feedback</p>
            <p style="margin: 8px 0 0; color: var(--text-muted); font-size: 0.95rem;">
                Only <strong>students you registered</strong> appear here. Quiz stats use attempts on <strong>your</strong> quizzes only.
            </p>
        </div>
    </div>

    @php
        $fbExcellent = $students->where('average_score', '>=', 90)->count();
        $fbGood = $students->where('average_score', '>=', 75)->where('average_score', '<', 90)->count();
        $fbAverage = $students->where('average_score', '>=', 60)->where('average_score', '<', 75)->count();
        $fbNeeds = $students->where('average_score', '<', 60)->count();
        $fbTotal = $students->count();
        $fbWithQuizzes = $students->where('quizzes_taken', '>', 0)->count();
        $fbBarTotal = max(1, $fbExcellent + $fbGood + $fbAverage + $fbNeeds);
        $fbPct = fn ($n) => round(($n / $fbBarTotal) * 100, 1);
    @endphp

    <!-- Performance overview (redesigned) -->
    <section class="feedback-performance-overview" aria-label="Class performance by average quiz score">
        <div class="feedback-performance-overview__head">
            <div>
                <h2 class="feedback-performance-overview__title">How your class is doing</h2>
                <p class="feedback-performance-overview__lede">Counts are based on each student’s <strong>average score</strong> across <strong>your</strong> quizzes. Students with no attempts yet fall in the lowest band (0%).</p>
            </div>
            <div class="feedback-performance-overview__meta">
                <div class="feedback-meta-pill">
                    <span class="feedback-meta-pill__value">{{ $fbTotal }}</span>
                    <span class="feedback-meta-pill__label">Students listed</span>
                </div>
                <div class="feedback-meta-pill feedback-meta-pill--accent">
                    <span class="feedback-meta-pill__value">{{ $fbWithQuizzes }}</span>
                    <span class="feedback-meta-pill__label">Took ≥1 quiz</span>
                </div>
            </div>
        </div>

        <div class="feedback-tier-bar" role="img" aria-label="Distribution of students across performance bands">
            <div class="feedback-tier-bar__track">
                @if($fbExcellent > 0)<span class="feedback-tier-bar__seg feedback-tier-bar__seg--excellent" style="width: {{ $fbPct($fbExcellent) }}%"></span>@endif
                @if($fbGood > 0)<span class="feedback-tier-bar__seg feedback-tier-bar__seg--good" style="width: {{ $fbPct($fbGood) }}%"></span>@endif
                @if($fbAverage > 0)<span class="feedback-tier-bar__seg feedback-tier-bar__seg--average" style="width: {{ $fbPct($fbAverage) }}%"></span>@endif
                @if($fbNeeds > 0)<span class="feedback-tier-bar__seg feedback-tier-bar__seg--needs" style="width: {{ $fbPct($fbNeeds) }}%"></span>@endif
            </div>
            <div class="feedback-tier-bar__legend">
                <span><i class="fas fa-square" style="color:#10b981"></i> Excellent</span>
                <span><i class="fas fa-square" style="color:#3b82f6"></i> Good</span>
                <span><i class="fas fa-square" style="color:#f59e0b"></i> Average</span>
                <span><i class="fas fa-square" style="color:#ef4444"></i> Needs help</span>
            </div>
        </div>

        <div class="feedback-tier-grid">
            <article class="feedback-tier feedback-tier--excellent">
                <div class="feedback-tier__icon" aria-hidden="true"><i class="fas fa-star"></i></div>
                <span class="feedback-tier__count">{{ $fbExcellent }}</span>
                <span class="feedback-tier__name">Excellent</span>
                <span class="feedback-tier__range">90% and above</span>
            </article>
            <article class="feedback-tier feedback-tier--good">
                <div class="feedback-tier__icon" aria-hidden="true"><i class="fas fa-thumbs-up"></i></div>
                <span class="feedback-tier__count">{{ $fbGood }}</span>
                <span class="feedback-tier__name">Good</span>
                <span class="feedback-tier__range">75% – 89%</span>
            </article>
            <article class="feedback-tier feedback-tier--average">
                <div class="feedback-tier__icon" aria-hidden="true"><i class="fas fa-chart-line"></i></div>
                <span class="feedback-tier__count">{{ $fbAverage }}</span>
                <span class="feedback-tier__name">Average</span>
                <span class="feedback-tier__range">60% – 74%</span>
            </article>
            <article class="feedback-tier feedback-tier--needs">
                <div class="feedback-tier__icon" aria-hidden="true"><i class="fas fa-life-ring"></i></div>
                <span class="feedback-tier__count">{{ $fbNeeds }}</span>
                <span class="feedback-tier__name">Needs help</span>
                <span class="feedback-tier__range">Below 60%</span>
            </article>
        </div>
    </section>

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
                        <button type="button" class="btn-feedback" data-student-id="{{ $student->id }}" data-student-name="{{ e($student->name) }}">
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

    /* Performance overview band */
    .feedback-performance-overview {
        margin-bottom: 28px;
        padding: 22px 24px 24px;
        border-radius: calc(var(--radius, 8px) + 4px);
        background: var(--bg-card);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .feedback-performance-overview__head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
    }

    .feedback-performance-overview__title {
        margin: 0 0 8px;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .feedback-performance-overview__lede {
        margin: 0;
        max-width: 720px;
        font-size: 0.88rem;
        line-height: 1.55;
        color: var(--text-muted);
    }

    .feedback-performance-overview__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .feedback-meta-pill {
        min-width: 120px;
        padding: 12px 16px;
        border-radius: 12px;
        background: var(--bg-main);
        border: 1px solid var(--border);
        text-align: center;
    }

    .feedback-meta-pill--accent {
        border-color: rgba(79, 70, 229, 0.35);
        background: linear-gradient(145deg, rgba(79, 70, 229, 0.08), rgba(79, 70, 229, 0.02));
    }

    .feedback-meta-pill__value {
        display: block;
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .feedback-meta-pill__label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }

    .feedback-tier-bar {
        margin-bottom: 20px;
    }

    .feedback-tier-bar__track {
        display: flex;
        height: 12px;
        border-radius: 999px;
        overflow: hidden;
        background: var(--bg-main);
        border: 1px solid var(--border);
    }

    .feedback-tier-bar__seg {
        display: block;
        height: 100%;
        min-width: 0;
        transition: width 0.35s ease;
    }

    .feedback-tier-bar__seg--excellent { background: linear-gradient(90deg, #059669, #10b981); }
    .feedback-tier-bar__seg--good { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .feedback-tier-bar__seg--average { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .feedback-tier-bar__seg--needs { background: linear-gradient(90deg, #dc2626, #f87171); }

    .feedback-tier-bar__legend {
        display: flex;
        flex-wrap: wrap;
        gap: 14px 20px;
        margin-top: 10px;
        font-size: 0.78rem;
        color: var(--text-muted);
    }

    .feedback-tier-bar__legend .fas {
        margin-right: 4px;
        font-size: 0.65rem;
        vertical-align: middle;
    }

    .feedback-tier-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    @media (max-width: 1100px) {
        .feedback-tier-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 520px) {
        .feedback-tier-grid {
            grid-template-columns: 1fr;
        }
    }

    .feedback-tier {
        position: relative;
        text-align: center;
        padding: 22px 14px 20px;
        border-radius: 14px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .feedback-tier:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .feedback-tier::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .feedback-tier--excellent::before { background: linear-gradient(90deg, #059669, #34d399); }
    .feedback-tier--good::before { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
    .feedback-tier--average::before { background: linear-gradient(90deg, #b45309, #fbbf24); }
    .feedback-tier--needs::before { background: linear-gradient(90deg, #b91c1c, #f87171); }

    .feedback-tier--excellent {
        background: linear-gradient(165deg, rgba(16, 185, 129, 0.12), var(--bg-main) 55%);
    }
    .feedback-tier--good {
        background: linear-gradient(165deg, rgba(59, 130, 246, 0.12), var(--bg-main) 55%);
    }
    .feedback-tier--average {
        background: linear-gradient(165deg, rgba(245, 158, 11, 0.14), var(--bg-main) 55%);
    }
    .feedback-tier--needs {
        background: linear-gradient(165deg, rgba(239, 68, 68, 0.12), var(--bg-main) 55%);
    }

    .feedback-tier__icon {
        width: 44px;
        height: 44px;
        margin: 0 auto 10px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .feedback-tier--excellent .feedback-tier__icon { background: rgba(16, 185, 129, 0.2); color: #059669; }
    .feedback-tier--good .feedback-tier__icon { background: rgba(59, 130, 246, 0.2); color: #1d4ed8; }
    .feedback-tier--average .feedback-tier__icon { background: rgba(245, 158, 11, 0.22); color: #b45309; }
    .feedback-tier--needs .feedback-tier__icon { background: rgba(239, 68, 68, 0.2); color: #b91c1c; }

    .feedback-tier__count {
        display: block;
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.03em;
        color: var(--text-primary);
    }

    .feedback-tier__name {
        display: block;
        margin-top: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .feedback-tier__range {
        display: block;
        margin-top: 4px;
        font-size: 0.78rem;
        color: var(--text-muted);
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

    document.querySelectorAll('.btn-feedback').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openFeedbackModal(this.dataset.studentId, this.dataset.studentName);
        });
    });

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
