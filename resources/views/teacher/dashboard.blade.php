<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <!-- FAVICON IN BROWSER TAB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
        :root {
            --primary: #002366;
            --secondary: #262840;
            --light-bg: #BFC5DB;
            --card-bg: #F1F1E0;
            --accent: #ffd43b;
            --accent-dark: #f5c400;
            --text-dark: #0b1020;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            background: url('/images/BACKGROUND.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at top, rgba(191,197,219,0.6), transparent 55%),
                        linear-gradient(to bottom, rgba(0,35,102,0.7), rgba(0,35,102,0.2));
            z-index: -1;
        }

        /* SIDEBAR */
        aside {
            width: 270px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: #e0e7ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            box-shadow: 12px 0 24px rgba(0,0,0,0.35);
            z-index: 20;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px 15px 10px;
            text-align: center;
        }

        /* 🔄 Match student avatar sizing & behavior */
        .logo-circle {
            width: 140px;
            height: 140px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 20%, #ffffff, #e5ecff);
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 15px rgba(255,212,59,0.5);
            overflow: hidden; /* crop image like student */
        }

        .sidebar-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* fill circle nicely */
            border-radius: 50%;
        }

        .player-tag {
            font-size: 0.8rem; text-transform: uppercase;
            letter-spacing: 1.2px; color: var(--accent);
        }

        nav { padding: 15px 18px 20px; flex: 1; }

        nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; margin-bottom: 8px;
            text-decoration: none; color: #e2e8f0;
            border-radius: 12px; font-size: 0.92rem;
            font-weight: 500; /* 🔄 match student font weight */
            transition: all 0.2s ease;
        }

        nav a i { width: 22px; text-align: center; font-size: 1rem; }

        nav a:hover,
        nav a.active {
            background: rgba(15,23,42,0.7);
            color: #fff;
            transform: translateX(6px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.4);
        }

        .sidebar-footer {
            padding: 14px 10px 18px;
            font-size: 0.8rem;
            text-align: center;
            color: #cbd5e1;
            background: rgba(15,23,42,0.75);
        }

        /* MAIN */
        main { flex: 1; margin-left: 270px; display: flex; flex-direction: column; }

        header {
            padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 15;
            backdrop-filter: blur(20px);
            background: linear-gradient(90deg, rgba(0,35,102,0.96), rgba(38,40,64,0.96));
            box-shadow: 0 6px 16px rgba(15,23,42,0.5);
        }

        header h1 {
            color: #e5edff; font-size: 1.15rem; font-weight: 600;
            display: flex; align-items: center; gap: 8px;
        }

        header h1 i { color: var(--accent); font-size: 1.3rem; }

        .menu-toggle {
            display: none; font-size: 1.5rem; color: var(--accent);
            background: none; border: none; cursor: pointer;
        }

        .header-right { display: flex; align-items: center; gap: 24px; }

        .notification-icon {
            position: relative; cursor: pointer; color: white; font-size: 1.45rem;
        }

        .notification-icon::after {
            content: '3';
            position: absolute; top: -6px; right: -9px;
            background: #e11d48; color: white;
            width: 18px; height: 18px; border-radius: 50%;
            font-size: 0.7rem; font-weight: 600;
            display: flex; justify-content: center; align-items: center;
        }

        .user-dropdown { position: relative; }

        /* 🔄 Match student header button sizing */
        .user-name {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            padding: 8px 16px; border-radius: 999px; border: none;
            color: #0b1020; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            font-size: 0.95rem; /* same as student */
            box-shadow: 0 4px 10px rgba(0,0,0,0.35);
        }

        .dropdown-menu {
            display: none; position: absolute; right: 0; top: 115%;
            background: #0b1c44; border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.45);
            overflow: hidden; min-width: 160px; z-index: 30;
        }

        .dropdown-menu.show { display: block; }

        .dropdown-item {
            padding: 10px 14px; display: block;
            color: #e2e8f0; text-decoration: none;
        }

        .dropdown-item:hover {
            background: var(--accent-dark);
            color: #0b1020;
        }

        section { flex: 1; padding: 30px 40px 40px; }

        .dashboard-shell {
            background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
            border-radius: 18px;
            padding: 24px 26px 32px;
            box-shadow: 0 14px 35px rgba(15,23,42,0.35);
            border: 1px solid rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
        }

        /* HERO (top banner) – like student dash */
        .shell-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .shell-top-left h2 {
            font-size: 1.1rem;
            color: var(--primary);
        }

        .shell-top-left p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .shell-pill {
            padding: 8px 16px;
            border-radius: 999px;
            background: rgba(0,35,102,0.07);
            font-size: 0.8rem;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .shell-pill i {
            color: var(--accent-dark);
        }

        /* CLICKABLE STATS GRID */
        .teacher-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 10px;
        }

        .stat-card-link {
            text-decoration: none;
            color: inherit;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 6px 16px rgba(15,23,42,0.20);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            right: -40px;
            top: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,212,59,0.35), transparent 60%);
        }

        .stat-icon {
            z-index: 1;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(0,35,102,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .stat-text {
            z-index: 1;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 2px;
        }

        .stat-meta {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(15,23,42,0.35);
        }

        @media (max-width: 992px) {
            .teacher-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            aside { transform: translateX(-100%); }
            aside.active { transform: translateX(0); }
            .menu-toggle { display: block; }
            main { margin-left: 0; }
            header { padding: 12px 16px; }

            .shell-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .teacher-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div>
            <div class="sidebar-header">
    @php
        $profilePic = Auth::user()->profile_pic ?? 'default-pp.png';
    @endphp

    <div class="logo-circle">
        <img src="{{ asset('images/' . $profilePic) }}"
             alt="Teacher Avatar"
             class="sidebar-logo">
    </div>

    <!-- <div class="player-tag">Teacher Portal</div> -->
    <div class="player-tag">{{ Auth::user()->name }}</div>
</div>


            <nav>
                <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('teacher.quest') }}" class="{{ request()->routeIs('teacher.quest') ? 'active' : '' }}">
                    <i class="fas fa-map-signs"></i> Manage Quests
                </a>
                <a href="{{ route('teacher.registration') }}" class="{{ request()->routeIs('teacher.registration') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i> Registration
                </a>
                <a href="{{ route('teacher.lessons.index') }}" class="{{ request()->routeIs('teacher.lessons.index') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i> Lessons
                </a>
                <a href="{{ route('teacher.quizzes') }}" class="{{ request()->routeIs('teacher.quizzes') ? 'active' : '' }}">
                    <i class="fas fa-scroll"></i> Quizzes
                </a>
                <a href="{{ route('teacher.gamification.index') }}" class="{{ request()->routeIs('teacher.gamification.index') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Gamification
                </a>
                <a href="{{ route('teacher.performance') }}" class="{{ request()->routeIs('teacher.performance') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Performance
                </a>
                <a href="{{ route('teacher.feedback') }}" class="{{ request()->routeIs('teacher.feedback') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots"></i> Feedback
                </a>
                <a href="{{ route('teacher.reports') }}" class="{{ request()->routeIs('teacher.reports') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Reports
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            © 2025 Level Up ASIANISTA
        </div>
    </aside>

    <!-- MAIN -->
    <main>
        <header>
            <div style="display:flex; align-items:center; gap:10px;">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <h1><i class="fas fa-chalkboard-teacher"></i> Level Up ASIANISTA • Teacher</h1>
            </div>

            <div class="header-right">
                <div class="notification-icon"><i class="fas fa-bell"></i></div>

                <div class="user-dropdown">
                    <button class="user-name" onclick="toggleDropdown()">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->name }}
                        <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
                    </button>
                    <div id="userDropdownMenu" class="dropdown-menu">
                        <a href="#" class="dropdown-item">Profile</a>
                        <a href="{{ url('/') }}" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <section>
            <div class="dashboard-shell">

                {{-- Show hero & stats only on the main teacher dashboard --}}
                @if (request()->routeIs('teacher.dashboard'))
                    <div class="shell-top">
                        <div class="shell-top-left">
                            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
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
                @endif

                {{-- Page-specific content --}}
                @yield('content')

            </div>
        </section>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        function toggleDropdown() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }
        document.addEventListener('click', (e) => {
            const btn = document.querySelector('.user-name');
            const menu = document.getElementById('userDropdownMenu');
            if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    </script>

</body>
</html>
