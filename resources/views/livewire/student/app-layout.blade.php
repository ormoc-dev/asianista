<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Realm | ASIANISTA</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- FAVICON IN BROWSER TAB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.2.0/turbolinks.js" defer></script>
    
    @livewireStyles
    @stack('styles')
    
    <link rel="stylesheet" href="{{ asset('css/student-dashboard.css') }}">
    
    <!-- Level Details Modal Styles -->
    <style>
        #levelDetailsModal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #levelDetailsModal .level-details-modal {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            width: 100%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 212, 59, 0.2);
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            border-radius: 20px;
            padding: 30px;
        }

        #levelDetailsModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        #levelDetailsModal .modal-header h3 {
            color: #ffd43b;
            margin: 0;
            font-size: 1.5rem;
        }

        #levelDetailsModal .btn-close-modal {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        #levelDetailsModal .btn-close-modal:hover { color: #fff; }

        #levelDetailsModal .modal-questions-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #levelDetailsModal .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
        }

        #levelDetailsModal .btn-ok {
            background: linear-gradient(135deg, #ffd43b, #d97706);
            color: #0b1020;
            padding: 10px 22px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
            transition: 0.2s;
        }

        #levelDetailsModal .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 212, 59, 0.4);
        }
        
        /* Quest Map Card Body Styles */
        .quest-map-card-body {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            min-height: 500px;
            width: 100%;
        }

        .embedded-map-section {
            width: 100%;
            height: 100%;
            min-height: 450px;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .embedded-map-section.active {
            opacity: 1;
        }

        .map-placeholder {
            text-align: center;
            color: rgba(255, 255, 255, 0.2);
        }

        /* Clean map background for dashboard card embedding */
        .embedded-map-section .map-frame,
        .embedded-map-section .map-exploration-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        
        /* Wrapper for embedded map */
        .embedded-map-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .embedded-map-wrap .map-exploration-area {
            width: 100% !important;
            height: auto !important;
            position: relative !important;
            display: block !important;
        }
        
        /* Preserve map background image in embedded view */
        .embedded-map-wrap .map-frame {
            width: 100% !important;
            height: 100% !important;
            min-height: 450px !important;
            background-size: cover !important;
            background-position: center !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            position: relative !important;
        }
        
        .embedded-map-wrap .interactive-landmarks {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 10 !important;
        }
        
        .embedded-map-wrap .map-action-card {
            display: none !important;
        }
        
        /* SVG path layer in embedded map */
        .embedded-map-wrap .map-svg-layer {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            pointer-events: none !important;
            z-index: 5 !important;
        }
        
        /* Landmark nodes in embedded map */
        .embedded-map-wrap .landmark-node {
            position: absolute !important;
            transform: translate(-50%, -50%) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            cursor: pointer !important;
        }
        
        .embedded-map-wrap .node-icon {
            width: 50px !important;
            height: 50px !important;
            background: #fff !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.3rem !important;
            color: #64748b !important;
            box-shadow: 0 8px 15px rgba(0,0,0,0.2) !important;
            border: 3px solid #eee !important;
        }
        
        .embedded-map-wrap .node-icon.active {
            background: #ffd43b !important;
            color: #1e293b !important;
            border-color: #fff !important;
            box-shadow: 0 0 25px rgba(255,212,59,0.6) !important;
        }
        
        .embedded-map-wrap .node-icon.locked {
            background: #cbd5e1 !important;
            color: #94a3b8 !important;
            border-color: #f1f5f9 !important;
        }
        
        .embedded-map-wrap .node-icon.finish {
            background: #1e293b !important;
            color: #fbbf24 !important;
            border-color: #334155 !important;
        }
        
        .embedded-map-wrap .node-tag {
            margin-top: 10px !important;
            background: rgba(0, 0, 0, 0.8) !important;
            color: white !important;
            padding: 4px 12px !important;
            border-radius: 6px !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            white-space: nowrap !important;
        }

        /* Sidebar header — brand logo */
        .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
        }

        .sidebar-brand-link {
            display: block;
            text-align: center;
            line-height: 0;
        }

        .sidebar-brand-logo {
            width: 100%;
            max-width: 220px;
            height: auto;
            vertical-align: middle;
        }

        /* Fills space under header; nav scrolls when items exceed viewport */
        .sidebar-main {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-nav {
            padding: 15px 12px 20px;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.25);
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-label {
            display: block;
            font-size: 0.65rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin-bottom: 4px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            color: #334155;
            border-radius: 10px;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            background: #f1f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .nav-icon i {
            font-size: 0.95rem;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(0, 0, 0, 0.04);
            color: #0b1020;
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon {
            background: rgba(251, 191, 36, 0.2);
        }

        .nav-item:hover .nav-icon i,
        .nav-item.active .nav-icon i {
            color: #d97706;
        }

        .nav-item.logout {
            color: #dc2626;
        }

        .nav-item.logout .nav-icon i {
            color: #dc2626;
        }

        .nav-item.logout:hover {
            background: rgba(248, 113, 113, 0.1);
            color: #b91c1c;
        }

        .nav-item.logout:hover .nav-icon {
            background: rgba(248, 113, 113, 0.12);
        }

        /* Collapsed Sidebar */
        aside.collapsed .sidebar-header {
            padding: 16px 8px;
        }

        aside.collapsed .sidebar-brand-logo {
            max-width: 56px;
        }

        aside.collapsed .nav-label,
        aside.collapsed .nav-item span {
            display: none;
        }

        aside.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }

        aside.collapsed .nav-icon {
            width: 40px;
            height: 40px;
        }

        aside.collapsed .nav-section {
            margin-bottom: 10px;
        }

        aside.collapsed .sidebar-footer {
            font-size: 0.5rem;
            padding: 10px 5px;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div class="sidebar-main">
            <div class="sidebar-header">
                <a href="{{ route('student.dashboard') }}" class="sidebar-brand-link" title="Level Up ASIANISTA">
                    <img src="{{ asset('images/logo.png') }}" alt="Level Up ASIANISTA" class="sidebar-brand-logo">
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-label">Main</span>
                    <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('student.quest') }}" class="nav-item {{ request()->routeIs('student.quest') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-map-signs"></i></div>
                        <span>Quests</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Learning</span>
                    <a href="{{ route('student.lessons') }}" class="nav-item {{ request()->routeIs('student.lessons') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
                        <span>Lessons</span>
                    </a>
                    <a href="{{ route('student.quizzes') }}" class="nav-item {{ request()->routeIs('student.quizzes') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-clipboard-check"></i></div>
                        <span>Quizzes</span>
                    </a>
                    <a href="{{ route('student.performance') }}" class="nav-item {{ request()->routeIs('student.performance') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
                        <span>Performance</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Social</span>
                    <a href="{{ route('student.messages') }}" class="nav-item {{ request()->routeIs('student.messages') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-comments"></i></div>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('student.feedback') }}" class="nav-item {{ request()->routeIs('student.feedback') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-comment-dots"></i></div>
                        <span>Feedback</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Account</span>
                    <a href="{{ route('student.registration') }}" class="nav-item {{ request()->routeIs('student.registration') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-id-card"></i></div>
                        <span>Registration</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    <a href="#" class="nav-item logout" onclick="event.preventDefault(); showLogoutModal();">
                        <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>

        <div class="sidebar-footer">© 2025 Level Up ASIANISTA</div>
    </aside>

    <!-- MAIN -->
    <main id="main-content">
        <header>
            <div class="header-left">
                <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h1><i class="fas fa-gamepad"></i> Student Realm</h1>
            </div>
            <div class="header-right">
                <div class="user-pill">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::user()?->name ?? 'Student' }}</span>
                </div>
            </div>
        </header>

        <section>
            {{ $slot }}
        </section>
    </main>

    <!-- FLOATING AI ASSISTANT -->
    <div class="ai-floating-btn" onclick="toggleAIChat()">
        <i class="fas fa-robot"></i>
    </div>

    <div class="ai-chat-window" id="ai-chat-window">
        <div class="ai-chat-header">
            <div class="info">
                <div class="avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <h4>Neural Sage</h4>
                    <p><i class="fas fa-circle" style="color: #10b981; font-size: 0.5rem;"></i> Online</p>
                </div>
            </div>
            <button class="close-chat" onclick="toggleAIChat()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="chat-messages" id="floating-chat-messages">
            <div class="message ai">
                Greetings! I am the Neural Sage. How can I assist you in your quest today?
            </div>
        </div>

        <div class="typing-indicator" id="floating-typing-indicator">
            The Sage is weaving wisdom...
        </div>

        <div class="chat-input-area">
            <input type="text" id="floating-chat-input" placeholder="Ask anything..." autocomplete="off">
            <button type="button" id="floating-chat-send-btn" data-chat-route="{{ route('student.ai.chat') }}" data-csrf="{{ csrf_token() }}"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logoutConfirmationModal" class="student-modal-overlay" style="display: none;">
        <div class="student-modal-box">
            <div class="student-modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Confirm Logout</h3>
            </div>
            <div class="student-modal-body">
                <p>Are you sure you want to end your adventure for today?</p>
            </div>
            <div class="student-modal-footer">
                <button onclick="closeLogoutModal()" class="btn-student-cancel">Cancel</button>
                <button onclick="document.getElementById('logout-form').submit();" class="btn-student-logout">Logout</button>
            </div>
        </div>
    </div>

    @livewireScripts
    <script src="{{ asset('js/student-dashboard.js') }}"></script>
    @stack('scripts')
    
    <!-- Level Details Modal Functions -->
    <script>
        function showLevelDetails(level, questions) {
            const modal = document.getElementById('levelDetailsModal');
            const levelSpan = document.getElementById('modalLevel');
            const list = document.getElementById('modalQuestionsList');
            
            if (levelSpan) levelSpan.textContent = level;
            if (list) {
                list.innerHTML = '';
                if (questions && questions.length > 0) {
                    questions.forEach((q, i) => {
                        const card = document.createElement('div');
                        card.className = 'modal-question-card';
                        card.style.cssText = 'background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; margin-bottom: 10px;';
                        card.innerHTML = `
                            <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                                <span style="font-size: 0.65rem; font-weight: 800; background: rgba(59,130,246,0.2); color: #60a5fa; padding: 3px 8px; border-radius: 5px; text-transform: uppercase;">${q.type ? q.type.replace('-', ' ') : 'Question'}</span>
                                <span style="font-size: 0.65rem; font-weight: 800; background: rgba(255,212,59,0.2); color: #ffd43b; padding: 3px 8px; border-radius: 5px; text-transform: uppercase;">${q.points} PTS</span>
                            </div>
                            <p style="font-size: 1rem; color: #e2e8f0; line-height: 1.5; margin: 0;">${q.question}</p>
                        `;
                        list.appendChild(card);
                    });
                } else {
                    list.innerHTML = '<p style="color: #94a3b8; text-align: center;">No questions available for this level.</p>';
                }
            }
            if (modal) modal.style.display = 'flex';
        }

        function closeLevelModal() {
            const modal = document.getElementById('levelDetailsModal');
            if (modal) modal.style.display = 'none';
        }

        // Close on overlay click
        window.addEventListener('click', function(event) {
            const levelModal = document.getElementById('levelDetailsModal');
            if (event.target == levelModal) {
                closeLevelModal();
            }
        });
    </script>
</body>
</html>
