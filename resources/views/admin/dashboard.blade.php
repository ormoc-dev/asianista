<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

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

        /* avatar circle, same style as student/teacher */
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
            overflow: hidden; /* crop nicely */
        }

        .sidebar-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* fill circle */
            border-radius: 50%;
        }

        .player-tag {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--accent);
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
            transition: all 0.2s ease;
            position: relative;
        }

        nav a i {
            width: 22px;
            text-align: center;
            font-size: 1rem;
        }

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

        header h1 i { color: var(--accent); font-size: 1.3rem; }

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--accent);
            background: none;
            border: none;
            cursor: pointer;
        }

        .header-right { display: flex; align-items: center; gap: 24px; }

        .notification-icon {
            position: relative;
            font-size: 1.45rem;
            cursor: pointer;
            color: #ffffff;
        }

        .notification-icon::after {
            content: '7';
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

        .user-dropdown { position: relative; }

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

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 115%;
            min-width: 160px;
            background-color: #0b1c44;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.45);
        }

        .dropdown-menu.show { display: block; }

        .dropdown-item {
            padding: 10px 14px;
            color: #e2e8f0;
            text-decoration: none;
            display: block;
        }

        .dropdown-item:hover {
            background-color: var(--accent-dark);
            color: #0b1020;
        }

        /* Content */
        section { flex: 1; padding: 30px 40px 40px; }

        .dashboard-shell {
            background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
            border-radius: 18px;
            padding: 24px 26px 32px;
            box-shadow: 0 14px 35px rgba(15,23,42,0.35);
            border: 1px solid rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            aside { transform: translateX(-100%); }
            aside.active { transform: translateX(0); }
            .menu-toggle { display: block; }
            main { margin-left: 0; }
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
                         alt="Admin Avatar"
                         class="sidebar-logo">
                </div>
                <div class="player-tag">{{ Auth::user()->name }}</div>
            </div>

            <nav>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('admin.user-management') }}" class="{{ request()->routeIs('admin.user-management') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> User Management
                </a>
                <a href="{{ route('admin.lessons.index') }}" class="{{ request()->routeIs('admin.lessons.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Content Control
                </a>
                <a href="{{ route('admin.quizzes') }}" class="{{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}">
                    <i class="fas fa-edit"></i> Quiz & Exam Control
                </a>
                <a href="{{ route('admin.gamification') }}" class="{{ request()->routeIs('admin.gamification') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Gamification
                </a>
                <a href="{{ route('admin.ai-management') }}" class="{{ request()->routeIs('admin.ai-management') ? 'active' : '' }}">
                    <i class="fas fa-robot"></i> AI Management
                </a>
                <a href="{{ route('admin.data') }}" class="{{ request()->routeIs('admin.data') ? 'active' : '' }}">
                    <i class="fas fa-database"></i> Data Handling
                </a>
                <a href="{{ route('admin.security') }}" class="{{ request()->routeIs('admin.security') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Security
                </a>
                <a href="{{ route('admin.target-audience') }}" class="{{ request()->routeIs('admin.target-audience') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Target Audience
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
                    <i class="fas fa-user-shield"></i>
                    Level Up ASIANISTA
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
