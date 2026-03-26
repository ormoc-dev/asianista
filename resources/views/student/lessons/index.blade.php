@extends('student.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Simple Header -->
    <div class="page-header">
        <h1><i class="fas fa-book-open"></i> Lessons</h1>
        <span class="count-badge">{{ $lessons->count() }} Available</span>
    </div>

    @if($lessons->count() > 0)
        <div class="lessons-list">
            @foreach($lessons as $lesson)
                <div class="lesson-item">
                    <div class="lesson-info">
                        <h3>{{ $lesson->title }}</h3>
                        <p class="lesson-meta">
                            <span><i class="fas fa-user"></i> {{ $lesson->teacher->first_name ?? 'Teacher' }}</span>
                            <span><i class="fas fa-calendar"></i> {{ $lesson->created_at->format('M d, Y') }}</span>
                            @if($lesson->section)
                                <span class="section-tag">{{ $lesson->section }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="lesson-actions">
                        <button class="btn-view" onclick="openLessonModal({{ $lesson->id }}, '{{ addslashes($lesson->title) }}', '{{ addslashes($lesson->teacher->first_name ?? 'Teacher') }}', '{{ $lesson->created_at->format('M d, Y') }}', `{{ addslashes($lesson->content ?? '') }}`, '{{ $lesson->file_path ? asset('storage/' . $lesson->file_path) : '' }}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        @if($lesson->file_path)
                            <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" class="btn-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <p>No lessons available yet.</p>
        </div>
    @endif
</div>

<!-- Scroll Paper Modal -->
<div id="lessonModal" class="scroll-modal">
    <div class="scroll-paper">
        <!-- Scroll Top Roll -->
        <div class="scroll-roll top">
            <div class="scroll-handle left"></div>
            <div class="scroll-bar"></div>
            <div class="scroll-handle right"></div>
        </div>
        
        <!-- Scroll Content -->
        <div class="scroll-content">
            <button class="close-scroll" onclick="closeLessonModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="scroll-header">
                <h2 id="modalTitle">Lesson Title</h2>
                <div class="scroll-meta">
                    <span id="modalTeacher"><i class="fas fa-user"></i> Teacher</span>
                    <span id="modalDate"><i class="fas fa-calendar"></i> Date</span>
                </div>
            </div>
            
            <div class="scroll-body" id="modalContent">
                <!-- Content goes here -->
            </div>
            
            <div class="scroll-footer" id="modalFooter" style="display: none;">
                <a href="#" id="modalDownload" class="scroll-download" target="_blank">
                    <i class="fas fa-download"></i> Download Attachment
                </a>
            </div>
        </div>
        
        <!-- Scroll Bottom Roll -->
        <div class="scroll-roll bottom">
            <div class="scroll-handle left"></div>
            <div class="scroll-bar"></div>
            <div class="scroll-handle right"></div>
        </div>
    </div>
</div>

<style>
    .content-wrapper {
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--accent);
    }

    .page-header h1 {
        color: var(--text-dark);
        font-size: 1.5rem;
        margin: 0;
    }

    .page-header h1 i {
        color: var(--accent);
        margin-right: 10px;
    }

    .count-badge {
        background: var(--card-bg);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        color: var(--text-dark);
        border: 1px solid rgba(0,0,0,0.1);
    }

    .lessons-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .lesson-item {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .lesson-item:hover {
        transform: translateX(5px);
    }

    .lesson-info h3 {
        margin: 0 0 8px 0;
        font-size: 1.1rem;
    }

    .lesson-info h3 a {
        color: var(--text-dark);
        text-decoration: none;
        transition: color 0.2s;
    }

    .lesson-info h3 a:hover {
        color: var(--primary);
    }

    .lesson-meta {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .lesson-meta span {
        margin-right: 15px;
    }

    .section-tag {
        background: var(--accent);
        color: var(--text-dark);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .lesson-actions {
        display: flex;
        gap: 10px;
    }

    .btn-view {
        background: var(--accent);
        color: var(--text-dark);
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: opacity 0.2s;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-view:hover {
        opacity: 0.9;
    }

    .btn-download {
        background: var(--primary);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: opacity 0.2s;
    }

    .btn-download:hover {
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }

    /* Scroll Paper Modal */
    .scroll-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .scroll-modal.active {
        display: flex;
    }

    .scroll-paper {
        
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        animation: unroll 0.5s ease-out;
    }

    @keyframes unroll {
        from {
            transform: scaleY(0);
            opacity: 0;
        }
        to {
            transform: scaleY(1);
            opacity: 1;
        }
    }

    /* Scroll Rolls */
    .scroll-roll {
        display: flex;
        align-items: center;
        height: 30px;
        position: relative;
        z-index: 2;
    }

    .scroll-roll.top {
        margin-bottom: -5px;
    }

    .scroll-roll.bottom {
        margin-top: -5px;
    }

    .scroll-handle {
        width: 40px;
        height: 40px;
        background: linear-gradient(145deg, #8b4513, #a0522d);
        border-radius: 50%;
        box-shadow: 
            inset -3px -3px 5px rgba(0,0,0,0.3),
            inset 3px 3px 5px rgba(255,255,255,0.2),
            0 2px 5px rgba(0,0,0,0.3);
        position: relative;
    }

    .scroll-handle::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 12px;
        height: 12px;
        background: #5c2e0c;
        border-radius: 50%;
        box-shadow: inset 1px 1px 2px rgba(0,0,0,0.5);
    }

    .scroll-bar {
        flex: 1;
        height: 25px;
        background: linear-gradient(180deg, #f4e4c1 0%, #f5deb3 50%, #e6d2a0 100%);
        border-radius: 12px;
        box-shadow: 
            inset 0 2px 4px rgba(0,0,0,0.1),
            0 2px 4px rgba(0,0,0,0.2);
    }

    /* Scroll Content */
    .scroll-content {
        background: linear-gradient(180deg, #f5deb3 0%, #f4e4c1 5%, #faf0e6 10%, #faf0e6 90%, #f4e4c1 95%, #f5deb3 100%);
        padding: 30px 40px;
        position: relative;
        overflow-y: auto;
        max-height: calc(85vh - 60px);
        box-shadow: 
            inset 0 0 30px rgba(139, 69, 19, 0.1),
            0 0 20px rgba(0,0,0,0.2);
        border-left: 3px solid #e6d2a0;
        border-right: 3px solid #e6d2a0;
    }

    /* Old paper texture effect */
    .scroll-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(139,69,19,0.03) 2px, rgba(139,69,19,0.03) 4px);
        pointer-events: none;
    }

    .close-scroll {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(139, 69, 19, 0.1);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        color: #8b4513;
        font-size: 1.2rem;
        transition: all 0.2s;
        z-index: 10;
    }

    .close-scroll:hover {
        background: rgba(139, 69, 19, 0.2);
        transform: rotate(90deg);
    }

    .scroll-header {
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(139, 69, 19, 0.2);
        margin-bottom: 25px;
    }

    .scroll-header h2 {
        color: #5c2e0c;
        font-size: 1.5rem;
        margin: 0 0 10px 0;
        font-weight: 700;
    }

    .scroll-meta {
        display: flex;
        justify-content: center;
        gap: 20px;
        font-size: 0.85rem;
        color: #8b6914;
    }

    .scroll-meta i {
        margin-right: 5px;
    }

    .scroll-body {
        color: #4a3728;
        line-height: 1.9;
        font-size: 1rem;
        padding: 0 10px;
    }

    .scroll-body p {
        margin-bottom: 15px;
    }

    .scroll-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid rgba(139, 69, 19, 0.2);
        text-align: center;
    }

    .scroll-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #8b4513;
        color: #f5deb3;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .scroll-download:hover {
        background: #5c2e0c;
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 600px) {
        .scroll-content {
            padding: 25px;
        }

        .scroll-header h2 {
            font-size: 1.3rem;
        }

        .scroll-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<script>
    function openLessonModal(id, title, teacher, date, content, filePath) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalTeacher').innerHTML = '<i class="fas fa-user"></i> ' + teacher;
        document.getElementById('modalDate').innerHTML = '<i class="fas fa-calendar"></i> ' + date;
        
        // Format content with line breaks
        const formattedContent = content ? content.replace(/\n/g, '<br>') : '<p style="text-align: center; color: #8b6914; font-style: italic;">No content available for this lesson.</p>';
        document.getElementById('modalContent').innerHTML = formattedContent;
        
        // Show/hide download button
        const footer = document.getElementById('modalFooter');
        if (filePath) {
            footer.style.display = 'block';
            document.getElementById('modalDownload').href = filePath;
        } else {
            footer.style.display = 'none';
        }
        
        document.getElementById('lessonModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLessonModal() {
        document.getElementById('lessonModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.getElementById('lessonModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLessonModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLessonModal();
        }
    });
</script>
@endsection