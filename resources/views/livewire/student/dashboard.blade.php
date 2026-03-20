<div class="dashboard-shell-1">
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
                        <i class="fas Star xp-star"></i>
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
                        <p>{{ \Illuminate\Support\Str::limit($activeQuest->description, 100) }}</p>
                        
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
</div>
