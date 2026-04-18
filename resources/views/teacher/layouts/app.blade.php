<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Portal') | ASIANISTA</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #1e293b;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --radius: 12px;
            --radius-sm: 8px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background: var(--bg-card);
            color:var(--secondary);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 10px 40px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            align-items: center;
        }

        

        .sidebar-logo {
            width: 100px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
          
        }

       

     

        .sidebar-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            padding: 0 12px;
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #212122ff;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .nav-link.active {
            background: var(--primary);
            color: #fff;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* Main Content */
        .main {
            flex: 1;
            margin-left: 200px;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: var(--bg-card);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .page-title i {
            color: var(--primary);
            margin-right: 8px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-main);
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.2s;
        }

        .header-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .header-btn .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger);
            color: #fff;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        .user-dropdown {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: var(--bg-main);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-btn:hover {
            border-color: var(--primary);
        }

        .user-btn img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-btn span {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            min-width: 180px;
            display: none;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: var(--bg-main);
        }

        .dropdown-item.danger {
            color: var(--danger);
        }

        /* Content */
        .content {
            flex: 1;
            padding: 24px 32px;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .card-body {
            padding: 24px;
        }

        /* Font Awesome fallback */
        .fa, .fas, .far, .fal, .fad, .fab {
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 1rem;
        }

        /* Stats Grid */
       .stats-grid {
    display: flex;
    gap: 16px;
    margin-bottom: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.stats-grid .stat-card {
    flex: 1 0 calc(16.666% - 16px);
    min-width: 160px;
    max-width: calc(16.666% - 16px);
}


        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 15px;
            border: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-icon.yellow { background: #fef3c7; color: #d97706; }
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-icon.indigo { background: #e0e7ff; color: #4f46e5; }

        .stat-content h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-content p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: var(--bg-main);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .table td {
            font-size: 0.9rem;
        }

        .table tbody tr:hover {
            background: var(--bg-main);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-purple { background: #ede9fe; color: #7c3aed; }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success { background: #d1fae5; color: #059669; }
        .alert-danger { background: #fee2e2; color: #dc2626; }
        .alert-warning { background: #fef3c7; color: #d97706; }
        .alert-info { background: #dbeafe; color: #2563eb; }

        /* Flash messages that auto-dismiss (see teacher flash script below) */
        .teacher-flash-auto {
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .content {
                padding: 20px 16px;
            }
        }

        @media (max-width: 1400px) {
            .stats-grid .stat-card {
                flex: 1 0 calc(20% - 16px);
                max-width: calc(20% - 16px);
            }
        }

        @media (max-width: 1200px) {
            .stats-grid .stat-card {
                flex: 1 0 calc(25% - 16px);
                max-width: calc(25% - 16px);
            }
        }

        @media (max-width: 992px) {
            .stats-grid {
                flex-wrap: wrap;
            }
            
            .stats-grid .stat-card {
                flex: 1 1 calc(33.333% - 16px);
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                flex-wrap: wrap;
            }
            
            .stats-grid .stat-card {
                flex: 1 1 calc(50% - 16px);
                min-width: 140px;
                max-width: none;
            }
        }

        @media (max-width: 640px) {

            .header {
                padding: 12px 16px;
            }

            .stats-grid .stat-card {
                flex: 1 1 100%;
                min-width: unset;
                max-width: none;
            }

            .page-title {
                font-size: 1rem;
            }
        }

        /* Floating AI Widget */
        .ai-widget-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 1.4rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .ai-widget-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(79, 70, 229, 0.5);
        }

        .ai-widget-btn.active {
            background: linear-gradient(135deg, var(--danger), #dc2626);
        }

        .ai-widget-btn .pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--primary);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .ai-chat-widget {
            position: fixed;
            bottom: 96px;
            right: 24px;
            width: 380px;
            height: 520px;
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border);
            z-index: 999;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        .ai-chat-widget.show {
            display: flex;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-widget-header {
            padding: 16px 20px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-widget-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
        }

        .ai-widget-header h4 {
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        .ai-widget-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            margin: 0;
        }

        .ai-widget-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ai-widget-messages::-webkit-scrollbar {
            width: 4px;
        }

        .ai-widget-messages::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        .ai-widget-msg {
            max-width: 85%;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 0.875rem;
            line-height: 1.4;
            animation: fadeIn 0.2s ease;
        }

        .ai-widget-msg.ai {
            align-self: flex-start;
            background: var(--bg-main);
            color: var(--text-primary);
            border-bottom-left-radius: 4px;
        }

        .ai-widget-msg.user {
            align-self: flex-end;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .ai-widget-typing {
            display: none;
            align-self: flex-start;
            background: var(--bg-main);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .ai-widget-input {
            padding: 12px 16px;
            background: var(--bg-main);
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
        }

        .ai-widget-input input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 0.875rem;
            outline: none;
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .ai-widget-input input:focus {
            border-color: var(--primary);
        }

        .ai-widget-input button {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .ai-widget-input button:hover {
            transform: scale(1.05);
        }

        .ai-widget-input button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 480px) {
            .ai-chat-widget {
                width: calc(100vw - 48px);
                right: 24px;
                left: 24px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sidebar-logo">
              
            </div>
        </div>

        

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('teacher.quest') }}" class="nav-link {{ request()->routeIs('teacher.quest*') ? 'active' : '' }}">
                    <i class="fas fa-map-signs"></i> Quests
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Content</div>
                <a href="{{ route('teacher.lessons.index') }}" class="nav-link {{ request()->routeIs('teacher.lessons*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i> Lessons
                </a>
                <a href="{{ route('teacher.quizzes') }}" class="nav-link {{ request()->routeIs('teacher.quizzes*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Quizzes
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="{{ route('teacher.registration') }}" class="nav-link {{ request()->routeIs('teacher.registration*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Registration
                </a>
                <a href="{{ route('teacher.gamification.index') }}" class="nav-link {{ request()->routeIs('teacher.gamification*') ? 'active' : '' }}">
                    <i class="fas fa-trophy"></i> Gamification
                </a>
                <a href="{{ route('teacher.random-events.index') }}" class="nav-link {{ request()->routeIs('teacher.random-events*') ? 'active' : '' }}">
                    <i class="fas fa-dice"></i> Random Events
                </a>
                <a href="{{ route('teacher.reports.scores') }}" class="nav-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Analytics</div>
                <a href="{{ route('teacher.performance') }}" class="nav-link {{ request()->routeIs('teacher.performance*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Performance
                </a>
                <a href="{{ route('teacher.feedback') }}" class="nav-link {{ request()->routeIs('teacher.feedback*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> Feedback
                </a>
                <a href="{{ route('teacher.messages') }}" class="nav-link {{ request()->routeIs('teacher.messages*') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots"></i> Messages
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            © 2025 ASIANISTA
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="header-right">
                <button class="header-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </button>

                <div class="user-dropdown">
                    <button class="user-btn" onclick="toggleDropdown()">
                        <img src="{{ asset('images/' . (Auth::user()?->profile_pic ?? 'default-pp.png')) }}" alt="Avatar">
                        <span>{{ Auth::user()?->name ?? 'Guest' }}</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="dropdown-item danger" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success teacher-flash-auto" data-teacher-flash role="status" aria-live="polite">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger teacher-flash-auto" data-teacher-flash role="alert" aria-live="assertive">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Floating AI Widget -->
    <div class="ai-chat-widget" id="aiChatWidget">
        <div class="ai-widget-header">
            <div class="ai-widget-avatar">
                <i class="fas fa-hat-wizard"></i>
            </div>
            <div>
                <h4>Arcane Advisor</h4>
                <p><i class="fas fa-circle" style="color: #10b981; font-size: 0.5rem;"></i> Online</p>
            </div>
        </div>
        <div class="ai-widget-messages" id="aiWidgetMessages">
            <div class="ai-widget-msg ai">
                Greetings! I'm the Arcane Advisor. How can I assist you today?
            </div>
        </div>
        <div class="ai-widget-typing" id="aiWidgetTyping">
            <i class="fas fa-magic fa-spin"></i> Consulting the arcane...
        </div>
        <div class="ai-widget-input">
            <input type="text" id="aiWidgetInput" placeholder="Ask something..." autocomplete="off">
            <button id="aiWidgetSend" onclick="sendAiMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Floating AI Button -->
    <button class="ai-widget-btn" id="aiWidgetBtn" onclick="toggleAiWidget()">
        <span class="pulse-ring"></span>
        <i class="fas fa-hat-wizard" id="aiWidgetIcon"></i>
    </button>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const btn = document.querySelector('.user-btn');
            if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.menu-toggle');
            if (window.innerWidth <= 1024 && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Floating AI Widget Functions
        let aiChatHistory = [];

        function toggleAiWidget() {
            const widget = document.getElementById('aiChatWidget');
            const btn = document.getElementById('aiWidgetBtn');
            const icon = document.getElementById('aiWidgetIcon');
            
            widget.classList.toggle('show');
            btn.classList.toggle('active');
            
            if (widget.classList.contains('show')) {
                icon.className = 'fas fa-times';
                document.getElementById('aiWidgetInput').focus();
            } else {
                icon.className = 'fas fa-hat-wizard';
            }
        }

        function appendAiMessage(role, text) {
            const container = document.getElementById('aiWidgetMessages');
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('ai-widget-msg', role);
            msgDiv.innerText = text;
            container.appendChild(msgDiv);
            container.scrollTop = container.scrollHeight;
            
            if (role !== 'system') {
                aiChatHistory.push({ role: role === 'ai' ? 'assistant' : 'user', content: text });
            }
        }

        async function sendAiMessage() {
            const input = document.getElementById('aiWidgetInput');
            const btn = document.getElementById('aiWidgetSend');
            const typing = document.getElementById('aiWidgetTyping');
            const text = input.value.trim();
            
            if (!text) return;

            input.value = '';
            appendAiMessage('user', text);

            btn.disabled = true;
            typing.style.display = 'block';

            try {
                const response = await fetch("{{ route('teacher.ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text,
                        history: aiChatHistory
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    appendAiMessage('ai', result.reply);
                } else {
                    appendAiMessage('ai', 'Connection interrupted. Please try again.');
                }
            } catch (error) {
                console.error('AI Widget Error:', error);
                appendAiMessage('ai', 'An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                typing.style.display = 'none';
            }
        }

        // Handle Enter key in AI widget input
        document.getElementById('aiWidgetInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendAiMessage();
            }
        });

        /** Auto-dismiss CRUD / flash notifications after 5s (teacher area) */
        (function () {
            var DISMISS_MS = 5000;
            function fadeRemove(el) {
                if (!el || el.getAttribute('data-flash-removing') === '1') return;
                el.setAttribute('data-flash-removing', '1');
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    if (el.parentNode) el.parentNode.removeChild(el);
                }, 350);
            }
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.teacher-flash-auto').forEach(function (el) {
                    setTimeout(function () { fadeRemove(el); }, DISMISS_MS);
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
