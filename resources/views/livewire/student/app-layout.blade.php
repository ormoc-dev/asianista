<div>
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
                <a href="#" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
            @if(request()->routeIs('student.dashboard'))
            <div class="{{ request()->routeIs('student.dashboard') ? 'dashboard-shell-1' : 'dashboard-shell' }}">
                <div class="dashboard-left-col">
                    <div class="shell-top">
                        <div class="stats-container">
                            <div class="stat-icon-group">
                                <div class="stat-meta">
                                    <span><i class="fas fa-heart"></i> Health</span>
                                    <span>8 / 10 HP</span>
                                </div>
                                <div class="icon-row">
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="fas fa-heart hp-heart"></i>
                                    <i class="far fa-heart hp-heart empty"></i>
                                    <i class="far fa-heart hp-heart empty"></i>
                                </div>
                            </div>

                            <div class="stat-icon-group">
                                <div class="stat-meta">
                                    <span><i class="fas fa-star"></i> Experience</span>
                                    <span>Level 5 (3,420 XP)</span>
                                </div>
                                <div class="icon-row">
                                    <i class="fas fa-star xp-star"></i>
                                    <i class="fas fa-star xp-star"></i>
                                    <i class="fas fa-star xp-star"></i>
                                    <i class="fas fa-star xp-star"></i>
                                    <i class="fas fa-star xp-star"></i>
                                    <i class="far fa-star xp-star empty"></i>
                                    <i class="far fa-star xp-star empty"></i>
                                    <i class="far fa-star xp-star empty"></i>
                                    <i class="far fa-star xp-star empty"></i>
                                    <i class="far fa-star xp-star empty"></i>
                                </div>
                            </div>
                        </div>

                        <div class="powers-title">
                            <i class="fas fa-magic"></i> Available Powers
                        </div>
                        <div class="powers-grid">
                            <div class="power-item">
                                <div class="power-icon"><i class="fas fa-shield-alt"></i></div>
                                <div class="power-info">
                                    <h4>Wisdom Shield</h4>
                                    <p>Passive defense</p>
                                </div>
                            </div>
                            <div class="power-item">
                                <div class="power-icon"><i class="fas fa-fire"></i></div>
                                <div class="power-info">
                                    <h4>Logic Blast</h4>
                                    <p>Active power</p>
                                </div>
                            </div>
                            <div class="power-item">
                                <div class="power-icon"><i class="fas fa-brain"></i></div>
                                <div class="power-info">
                                    <h4>Neural surge</h4>
                                    <p>Critical boost</p>
                                </div>
                            </div>
                            <div class="power-item">
                                <div class="power-icon"><i class="fas fa-feather-alt"></i></div>
                                <div class="power-info">
                                    <h4>Swift Mind</h4>
                                    <p>Agility buff</p>
                                </div>
                            </div>
                        </div>


                        <div class="quest-card" onclick="openQuestDrawer('{{ route('student.quest.show', $activeQuest->id ?? 0) }}')" style="cursor: pointer;">
                            @if(isset($activeQuest) && $activeQuest)
                                <div class="quest-card-header">
                                    <div class="quest-card-title">
                                        <i class="fas fa-scroll"></i> Current Quest
                                    </div>
                                    <span class="quest-card-diff {{ strtolower($activeQuest->difficulty ?? 'medium') }}">
                                        {{ $activeQuest->difficulty ?? 'Medium' }}
                                    </span>
                                </div>
                                <div class="quest-card-body">
                                    <h3 style="margin-bottom: 8px; font-size: 1.1rem; color: white;">{{ $activeQuest->title }}</h3>
                                    <p>{{ Str::limit($activeQuest->description, 100) }}</p>
                                    
                                    <div class="quest-card-rewards">
                                        <div class="reward-pill xp">
                                            <i class="fas fa-bolt"></i> +{{ $activeQuest->xp_reward ?? 0 }} XP
                                        </div>
                                        <div class="reward-pill gp">
                                            <i class="fas fa-coins"></i> +{{ $activeQuest->gp_reward ?? 0 }} GP
                                        </div>
                                    </div>
                                </div>
                                <div class="quest-card-footer">
                                    <div class="btn-quest-action-preview">
                                        View Map & Adventure <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            @else
                                <div class="quest-empty-state">
                                    <i class="fas fa-scroll"></i>
                                    <p>No active quests at the moment.<br>Check back later for new adventures!</p>
                                </div>
                            @endif
                        </div> 
                    </div> <!-- End shell-top -->
                </div> <!-- End Left Column -->

                <div class="dashboard-right-col">
                    <!-- SEPARATE QUEST MAP CARD -->
                    <div class="quest-map-card">
                        
                        <div class="quest-map-card-body">
                            <div id="embedded-map-container" class="embedded-map-section">
                                <div class="map-placeholder">
                                    <i class="fas fa-compass" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                                    <p>Select your quest to reveal the path through ASIANISTA...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End dashboard-shell-1 -->
            @yield('content')
            @else
                <div class="dashboard-shell">
                    @yield('content')
                </div>
            @endif

        </section>
    </main>

</div>
