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
</head>
<body>
    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div>
            <div class="sidebar-header">
                @php
                    $user = Auth::user();
                    $profilePic = $user?->profile_pic ?? 'default-pp.png';
                    $userName = $user?->name ?? 'Student';
                @endphp
                <div class="logo-circle">
                    <img src="{{ asset('images/' . $profilePic) }}" alt="Avatar" class="sidebar-logo">
                </div>
                <div class="player-tag">{{ $userName }}</div>
                
                @if ($user?->character)
                    <div class="character-name">{{ ucfirst($user->character) }}</div>
                @endif

                <div class="sidebar-level">Level <span>05</span> • 3,420 XP</div>
                <div class="xp-bar"><div class="xp-fill"></div></div>
            </div>

            <nav>
                <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('student.quest') }}" class="{{ request()->routeIs('student.quest') ? 'active' : '' }}">
                    <i class="fas fa-map-signs"></i><span>Quest</span>
                </a>
                <a href="{{ route('student.registration') }}" class="{{ request()->routeIs('student.registration') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i><span>Registration</span>
                </a>
                <a href="{{ route('student.messages') }}" class="{{ request()->routeIs('student.messages') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i><span>Messages</span>
                </a>
                <a href="{{ route('student.lessons') }}" class="{{ request()->routeIs('student.lessons') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i><span>Lessons</span>
                </a>
                <a href="{{ route('student.performance') }}" class="{{ request()->routeIs('student.performance') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Performance</span>
                </a>
                <a href="{{ route('student.feedback') }}" class="{{ request()->routeIs('student.feedback') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots"></i><span>Feedback</span>
                </a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                <a href="#" class="logout-link" onclick="event.preventDefault(); showLogoutModal();">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </a>
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
            <button onclick="sendFloatingMessage('{{ route('student.ai.chat') }}', '{{ csrf_token() }}')"><i class="fas fa-paper-plane"></i></button>
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
</body>
</html>
