<div>
    <div class="shell-top">
        <div class="shell-top-left">
            <h2>Welcome back, {{ Auth::user()->name ?? 'Teacher' }}!</h2>
            <p>Review quests, guide your party, and keep your class progressing through the adventure.</p>
        </div>
        <div class="shell-pill">
            <i class="fas fa-chess-knight"></i>
            Teaching Overview
        </div>
    </div>

    <div class="teacher-stats-grid">
        <a href="{{ route('teacher.registration') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Active Students</div>
                    <div class="stat-value">120</div>
                    <div class="stat-meta">Across all enrolled classes</div>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.quest') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-map-signs"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Active Quests</div>
                    <div class="stat-value">8</div>
                    <div class="stat-meta">Currently available to students</div>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.quest') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-scroll"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Quests Created</div>
                    <div class="stat-value">27</div>
                    <div class="stat-meta">Lifetime quests you’ve authored</div>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.lessons.index') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Lessons Created</div>
                    <div class="stat-value">15</div>
                    <div class="stat-meta">Lesson modules available</div>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.performance') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Leaderboard Highlights</div>
                    <div class="stat-value">Top 10</div>
                    <div class="stat-meta">View highest XP & rank students</div>
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.feedback') }}" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="stat-text">
                    <div class="stat-label">Feedback Messages</div>
                    <div class="stat-value">5</div>
                    <div class="stat-meta">New reflections from students</div>
                </div>
            </div>
        </a>
    </div>
</div>
