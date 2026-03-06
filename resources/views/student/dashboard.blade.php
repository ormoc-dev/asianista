<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Google Fonts -->
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: url('/images/BACKGROUND.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-dark);
            position: relative;
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
            top: 0;
            left: 0;
            bottom: 0;
            box-shadow: 12px 0 24px rgba(0, 0, 0, 0.35);
            z-index: 20;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px 15px 10px;
            text-align: center;
        }

        .logo-circle {
            width: 140px;
            height: 140px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 20%, #ffffff, #e5ecff);
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 15px rgba(255, 212, 59, 0.5);
            overflow: hidden; /* ✅ crop avatar nicely */
        }

        .sidebar-logo {
            width: 100%;
            height: 100%;
            object-fit: cover; /* ✅ make sure avatar fills the circle */
            border-radius: 50%;
        }

        .player-tag {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--accent);
        }

        .sidebar-level {
            font-size: 0.85rem;
            margin-top: 6px;
            color: #dbeafe;
        }

        .sidebar-level span {
            color: var(--accent);
            font-weight: 600;
        }

        .xp-bar {
            margin: 15px auto 0;
            width: 85%;
            height: 8px;
            border-radius: 999px;
            background: rgba(15,23,42,0.6);
            overflow: hidden;
        }

        .xp-fill {
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, var(--accent), #ffe066, var(--accent-dark));
            box-shadow: 0 0 12px rgba(255, 212, 59, 0.9);
        }

        nav {
            padding: 15px 18px 20px;
            flex: 1;
        }

        nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            color: #e2e8f0;
            border-radius: 12px;
            position: relative;
            transition: all 0.2s ease;
        }

        nav a i {
            width: 22px;
            text-align: center;
            font-size: 1rem;
        }

        nav a::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(circle at 0 0, rgba(255, 212, 59, 0.3), transparent 55%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        nav a:hover,
        nav a.active {
            background: rgba(15,23,42,0.7);
            color: #fff;
            transform: translateX(6px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.4);
        }

        nav a:hover::before,
        nav a.active::before {
            opacity: 1;
        }

        nav a span {
            position: relative;
            z-index: 1;
        }

        .sidebar-footer {
            padding: 14px 10px 18px;
            font-size: 0.8rem;
            text-align: center;
            color: #cbd5e1;
            background: rgba(15,23,42,0.75);
        }

        /* MAIN */
        main {
            flex: 1;
            margin-left: 270px;
            display: flex;
            flex-direction: column;
        }

        header {
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 15;
            backdrop-filter: blur(20px);
            background: linear-gradient(90deg, rgba(0,35,102,0.96), rgba(38,40,64,0.96));
            box-shadow: 0 6px 16px rgba(15,23,42,0.5);
        }

        header h1 {
            color: #e5edff;
            font-size: 1.15rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        header h1 i {
            color: var(--accent);
            font-size: 1.3rem;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--accent);
            margin-right: 10px;
            cursor: pointer;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .notification-icon {
            position: relative;
            font-size: 1.45rem;
            cursor: pointer;
            color: #ffffff;
        }

        .notification-icon::after {
            content: '3';
            position: absolute;
            top: -6px;
            right: -9px;
            background-color: #e11d48;
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
            font-weight: 600;
        }

        .user-dropdown {
            position: relative;
        }

        .user-name {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: none;
            color: #0b1020;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.35);
        }

        .user-name i {
            background: rgba(11,16,32,0.1);
            border-radius: 50%;
            padding: 4px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 115%;
            min-width: 160px;
            background-color: #0b1c44;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.45);
            overflow: hidden;
            z-index: 30;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px 14px;
            color: #e2e8f0;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background-color: var(--accent-dark);
            color: #0b1020;
        }

        section {
            flex: 1;
            padding: 30px 40px 40px;
        }

        .dashboard-shell {
            background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
            border-radius: 18px;
            padding: 24px 26px 32px;
            box-shadow: 0 14px 35px rgba(15,23,42,0.35);
            border: 1px solid rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
        }

        .shell-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
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

        .shell-pill i{
            color: var(--accent-dark);
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 6px 16px rgba(15,23,42,0.20);
            position: relative;
            overflow: hidden;
        }

        .card::after {
            content: "";
            position: absolute;
            right: -40px;
            top: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,212,59,0.35), transparent 60%);
        }

        .card h2 {
            color: var(--primary);
            font-size: 1.05rem;
            margin-bottom: 8px;
        }

        .card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: none;
            color: #0b1020;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 999px;
            cursor: pointer;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.35);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--accent-dark), var(--accent));
        }

        @media (max-width: 992px) {
            section {
                padding: 20px 18px 26px;
            }
            .dashboard-shell {
                padding: 18px 16px 24px;
            }
        }

        @media (max-width: 768px) {
            aside {
                transform: translateX(-100%);
            }
            aside.active {
                transform: translateX(0);
            }
            .menu-toggle {
                display: block;
            }
            main {
                margin-left: 0;
            }
            header {
                padding: 12px 16px;
            }
            .shell-top {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        .character-name {
    font-size: 0.85rem;
    margin-top: 4px;
    color: #dbeafe;
    font-weight: 500;
    letter-spacing: 0.5px;
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
                    {{-- ✅ show student's profile picture --}}
                    <img src="{{ asset('images/' . $profilePic) }}"
                         alt="Student Avatar"
                         class="sidebar-logo">
                </div>
                <!-- <div class="player-tag">Student Portal</div> -->
                <div class="player-tag">{{ Auth::user()->name }}</div>

{{-- Character Name (only if exists) --}}
@if (Auth::user()->character)
    <div class="character-name">
        {{ ucfirst(Auth::user()->character) }}
    </div>
@endif
                <div class="sidebar-level">Level <span>05</span> • XP 3,420</div>
                <div class="xp-bar">
                    <div class="xp-fill"></div>
                </div>
            </div>

            <nav>
                <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('student.quest') }}" class="{{ request()->routeIs('student.quest') ? 'active' : '' }}">
                    <i class="fas fa-map-signs"></i><span>Quest</span>
                </a>
                <a href="{{ route('student.registration') }}" class="{{ request()->routeIs('student.registration') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i><span>Registration &amp; Account</span>
                </a>
                <!-- <a href="{{ route('student.quizzes') }}" class="{{ request()->routeIs('student.quizzes') ? 'active' : '' }}">
                    <i class="fas fa-scroll"></i><span>Assessment &amp; Activities</span>
                </a> -->
                <a href="{{ route('student.messages') }}" class="{{ request()->routeIs('student.messages') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i><span>Messages</span>
                </a>
                <a href="{{ route('student.lessons') }}" class="{{ request()->routeIs('student.lessons') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i><span>Lesson Access</span>
                </a>
                <a href="{{ route('student.ai-support') }}" class="{{ request()->routeIs('student.ai-support') ? 'active' : '' }}">
                    <i class="fas fa-robot"></i><span>AI Support</span>
                </a>
                <a href="{{ route('student.performance') }}" class="{{ request()->routeIs('student.performance') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Performance</span>
                </a>
                <a href="{{ route('student.feedback') }}" class="{{ request()->routeIs('student.feedback') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots"></i><span>Feedback</span>
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
                <h1>
                    <i class="fas fa-gamepad"></i>
                    Level Up ASIANISTA • Student
                </h1>
            </div>

            <div class="header-right">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>

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

                {{-- Show hero only on the main dashboard page --}}
                @if (request()->routeIs('student.dashboard'))
                    <div class="shell-top">
                        <div class="shell-top-left">
                            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
                            <p>Continue your journey, complete quests, and earn more XP.</p>
                        </div>
                        <div class="shell-pill">
                            <i class="fas fa-bolt"></i>
                            Daily Streak: <strong>3</strong> days
                        </div>
                    </div>
                @endif

                {{-- Page-specific content (Quest, Lessons, etc.) --}}
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

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdownMenu');
            const btn = document.querySelector('.user-name');
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    </script>
</body>
</html>
