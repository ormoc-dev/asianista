<div class="dashboard-shell-1">
    @php
        $user = Auth::user();
        $currentHP = $user?->hp ?? 0;
        $currentAP = $user?->ap ?? 0;
        $maxHP = 100;
        $maxAP = 100;
        $characterData = $user?->getCharacterData();
        $powers = $characterData['abilities'] ?? [];
    @endphp
    <div class="dashboard-left-col">
        <div class="shell-top">
            <div class="stats-container">
                <div class="stat-icon-group">
                    <div class="stat-meta">
                        <span><i class="fas fa-heart"></i> Health (HP)</span>
                        <span>{{ $currentHP }} / {{ $maxHP }} HP</span>
                    </div>
                    <div class="icon-row">
                        @for($i = 0; $i < 10; $i++)
                            @if($i < ceil($currentHP / 10))
                                <i class="fas fa-heart hp-heart"></i>
                            @else
                                <i class="far fa-heart hp-heart empty"></i>
                            @endif
                        @endfor
                    </div>
                </div>

                <div class="stat-icon-group">
                    <div class="stat-meta">
                        <span><i class="fas fa-bolt"></i> Action Points (AP)</span>
                        <span>{{ $currentAP }} / {{ $maxAP }} AP</span>
                    </div>
                    <div class="icon-row">
                        @for($i = 0; $i < 10; $i++)
                            @if($i < ceil($currentAP / 10))
                                <i class="fas fa-bolt xp-star" style="color: #3b82f6;"></i>
                            @else
                                <i class="far fa-bolt xp-star empty" style="color: #e2e8f0;"></i>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>

            <div class="powers-title">
                <i class="fas fa-magic"></i> {{ $characterData['name'] ?? 'Character' }} Powers
            </div>
            <div class="powers-grid">
                @forelse($powers as $powerName => $powerDesc)
                    <div class="power-item" title="{{ $powerDesc }}">
                        <div class="power-icon">
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
                        <div class="power-info">
                            <h4>{{ $powerName }}</h4>
                            <p>{{ \Illuminate\Support\Str::limit($powerDesc, 40) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="power-item">
                        <div class="power-icon"><i class="fas fa-question"></i></div>
                        <div class="power-info">
                            <h4>No Powers</h4>
                            <p>Complete registration to unlock powers</p>
                        </div>
                    </div>
                @endforelse
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
