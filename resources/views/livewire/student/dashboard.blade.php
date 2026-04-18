<div class="rpg-dashboard">
    @php
        $user = Auth::user();
        $currentHP = $user?->hp ?? 0;
        $currentAP = $user?->ap ?? 0;
        $maxHP = 100;
        $maxAP = 100;
        $userXP = $user?->xp ?? 0;
        $userLevel = floor($userXP / 100) + 1;
        $xpForNextLevel = $userLevel * 100;
        $xpProgress = $userXP % 100;
        $hpBarPct = min(100, max(0, $maxHP > 0 ? ($currentHP / $maxHP) * 100 : 0));
        $apBarPct = min(100, max(0, $maxAP > 0 ? ($currentAP / $maxAP) * 100 : 0));
        $xpBarPct = min(100, max(0, $xpProgress));
        $characterData = $user?->getCharacterData();
        $powers = $characterData['abilities'] ?? [];
    @endphp

   

    <!-- MAIN DASHBOARD GRID -->
    <div class="dashboard-grid">
        <!-- LEFT COLUMN: COMBINED CHARACTER PANEL -->
        <div class="dashboard-column left-column">
            <div class="rpg-panel character-panel">
                <!-- CHARACTER HEADER -->
                <div class="character-header">
                    <div class="character-avatar-ring">
                        <img src="{{ asset('images/' . ($user?->profile_pic ?? 'default-pp.png')) }}" alt="Avatar" class="character-avatar">
                        <div class="character-level-badge">{{ $userLevel }}</div>
                    </div>
                    <div class="character-info">
                        <h2 class="character-name">{{ $user?->name ?? 'Adventurer' }}</h2>
                        <span class="character-class">{{ $characterData['name'] ?? 'Novice' }}</span>
                    </div>
                </div>

                <!-- SCROLLABLE CONTENT -->
                <div class="character-scrollable">
                    <!-- STATUS BARS SECTION -->
                    <div class="section-title"><i class="fas fa-chart-bar"></i> Status</div>
                    <div class="compact-stats">
                        <!-- HP Stat -->
                        <div class="compact-stat hp-stat">
                            <div class="compact-stat-header">
                                <span class="compact-stat-label"><i class="fas fa-heart"></i> HP</span>
                                <span class="compact-stat-value">{{ $currentHP }}/{{ $maxHP }}</span>
                            </div>
                            <div class="compact-progress-bg">
                                <svg class="compact-progress-svg" viewBox="0 0 100 8" preserveAspectRatio="none" width="100%" height="8" aria-hidden="true">
                                    <rect class="compact-svg-fill hp-svg" x="0" y="0" height="8" rx="4" width="{{ min(100, max(0, $hpBarPct)) }}" />
                                </svg>
                            </div>
                        </div>

                        <!-- AP Stat -->
                        <div class="compact-stat ap-stat">
                            <div class="compact-stat-header">
                                <span class="compact-stat-label"><i class="fas fa-bolt"></i> AP</span>
                                <span class="compact-stat-value">{{ $currentAP }}/{{ $maxAP }}</span>
                            </div>
                            <div class="compact-progress-bg">
                                <svg class="compact-progress-svg" viewBox="0 0 100 8" preserveAspectRatio="none" width="100%" height="8" aria-hidden="true">
                                    <rect class="compact-svg-fill ap-svg" x="0" y="0" height="8" rx="4" width="{{ min(100, max(0, $apBarPct)) }}" />
                                </svg>
                            </div>
                        </div>

                        <!-- XP Stat -->
                        <div class="compact-stat xp-stat">
                            <div class="compact-stat-header">
                                <span class="compact-stat-label"><i class="fas fa-star"></i> XP</span>
                                <span class="compact-stat-value">{{ $userXP }} / {{ $xpForNextLevel }}</span>
                            </div>
                            <div class="compact-progress-bg">
                                <svg class="compact-progress-svg" viewBox="0 0 100 8" preserveAspectRatio="none" width="100%" height="8" aria-hidden="true">
                                    <rect class="compact-svg-fill xp-svg" x="0" y="0" height="8" rx="4" width="{{ min(100, max(0, $xpBarPct)) }}" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- POWERS SECTION -->
                    <div class="section-title"><i class="fas fa-magic"></i> Powers</div>
                    <div class="compact-powers">
                        @forelse($powers as $powerName => $powerDesc)
                            <div class="compact-power" title="{{ $powerDesc }}">
                                <div class="compact-power-icon">
                                    @switch(strtolower($powerName))
                                        @case('spell of insight') @case('power strike') @case('healing light')
                                            <i class="fas fa-hand-sparkles"></i>
                                            @break
                                        @case('mana boost') @case('streak master') @case('team blessing')
                                            <i class="fas fa-arrow-up"></i>
                                            @break
                                        @case('time warp') @case('shield guard') @case('revive')
                                            <i class="fas fa-shield-alt"></i>
                                            @break
                                        @case('knowledge burst') @case('battle rush') @case('focus aura')
                                            <i class="fas fa-bolt"></i>
                                            @break
                                        @case('arcane analysis') @case('challenge duel') @case('wisdom share')
                                            <i class="fas fa-brain"></i>
                                            @break
                                        @default
                                            <i class="fas fa-star"></i>
                                    @endswitch
                                </div>
                                <span class="compact-power-name">{{ $powerName }}</span>
                            </div>
                        @empty
                            <div class="compact-power locked">
                                <div class="compact-power-icon"><i class="fas fa-lock"></i></div>
                                <span class="compact-power-name">No Powers</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- ACTIVE QUEST SECTION -->
                    <div class="section-title"><i class="fas fa-scroll"></i> Active Quest</div>
                    @if(isset($activeQuest) && $activeQuest)
                        <div class="compact-quest" data-quest-url="{{ route('student.quest.show', $activeQuest->id) }}" onclick="openQuestDrawer(this.dataset.questUrl)">
                            <div class="compact-quest-header">
                                <span class="compact-quest-title">{{ \Illuminate\Support\Str::limit($activeQuest->title, 30) }}</span>
                                <span class="compact-quest-diff {{ strtolower($activeQuest->difficulty ?? 'medium') }}">{{ $activeQuest->difficulty ?? 'Medium' }}</span>
                            </div>
                            <p class="compact-quest-desc">{{ \Illuminate\Support\Str::limit($activeQuest->description, 80) }}</p>
                            <div class="compact-quest-rewards">
                                <span class="compact-reward xp"><i class="fas fa-bolt"></i> +{{ $activeQuest->xp_reward ?? 0 }}</span>
                                <span class="compact-reward gp"><i class="fas fa-coins"></i> +{{ $activeQuest->gp_reward ?? 0 }}</span>
                            </div>
                            <div class="compact-quest-action">
                                <span>Start Quest</span>
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    @else
                        <div class="compact-quest-empty">
                            <i class="fas fa-scroll"></i>
                            <p>No active quests available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: MAP -->
        <div class="dashboard-column right-column">
            <div class="rpg-panel map-panel">
                <div class="panel-header">
                    <div class="panel-icon"><i class="fas fa-map-marked-alt"></i></div>
                    <h3 class="panel-title">Quest Map</h3>
                </div>
                <div class="map-container">
                    <div id="embedded-map-container" class="embedded-map-section">
                        <div class="map-placeholder">
                            <div class="placeholder-icon">
                                <i class="fas fa-compass"></i>
                            </div>
                            <h4>Adventure Awaits</h4>
                            <p>Select a quest to reveal your path through ASIANISTA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
