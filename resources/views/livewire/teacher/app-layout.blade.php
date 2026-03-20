<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Side | ASIANISTA</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- FAVICON IN BROWSER TAB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @livewireStyles
    @stack('styles')
    
    <link rel="stylesheet" href="{{ asset('css/teacher-dashboard.css') }}">
</head>
<body>
    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div>
            <div class="sidebar-header">
                @php
                    $user = Auth::user();
                    $profilePic = $user?->profile_pic ?? 'default-pp.png';
                    $userName = $user?->name ?? 'Teacher';
                @endphp
                <div class="logo-circle">
                    <img src="{{ asset('images/' . $profilePic) }}" alt="Avatar" class="sidebar-logo">
                </div>
                <div class="player-tag">{{ $userName }}</div>
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

        <div class="sidebar-footer">© 2025 Level Up ASIANISTA</div>
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
                        {{ Auth::user()?->name ?? 'Guest' }}
                        <i class="fas fa-chevron-down" style="font-size:0.75rem;"></i>
                    </button>
                    <div id="userDropdownMenu" class="dropdown-menu">
                        <a href="#" class="dropdown-item">Profile</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <section>
            <div class="dashboard-shell">
                {{ $slot }}
            </div>
        </section>
    </main>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="{{ asset('js/teacher-dashboard.js') }}"></script>
    @stack('scripts')
</body>
</html>
