@extends('student.dashboard')

@section('content')
<div class="quest-play-page">
    <div class="play-header">
        <div class="play-header-left">
            <a href="{{ route('student.quest.show', $quest->id) }}" class="btn-exit">
                <i class="fas fa-times"></i> Exit Quest
            </a>
            <div class="play-quest-info">
                <h2>{{ $quest->title }}</h2>
                <div class="play-quest-meta">
                    <span class="step-counter">Objective {{ $attempt->quest->questions->pluck('id')->search($question->id) + 1 }} of {{ $attempt->quest->questions->count() }}</span>
                    <span class="lvl-badge"><i class="fas fa-medal"></i> LVL {{ $question->level }}</span>
                </div>
            </div>
        </div>

        <div class="play-header-right">
            @if($quest->time_limit_minutes)
            <div class="timer-display" id="quest-timer">
                <i class="fas fa-clock"></i> <span id="timer-count">{{ $quest->time_limit_minutes }}:00</span>
            </div>
            @endif
            <div class="reward-preview">
                <span class="reward-item"><i class="fas fa-star"></i> {{ $question->points }} XP</span>
            </div>
        </div>
    </div>

    <div class="play-content-container">
        <!-- Question Area -->
        <div class="question-card">
            <div class="question-type-badge">{{ str_replace('_', ' ', ucfirst($question->type)) }}</div>
            
            <!-- Active Power Hint Display -->
            <div id="active-power-hint" class="power-active-hint">
                <i class="fas fa-magic"></i> <span id="hint-text"></span>
            </div>

            <div class="question-text">
                <h3>{!! nl2br(e($question->question)) !!}</h3>
            </div>

            <form id="quest-answer-form" class="answer-options-area">
                @csrf
                @php
                    $options = is_array($question->options) ? $question->options : (is_string($question->options) ? json_decode($question->options, true) : []);
                @endphp
                @if($question->type === 'multiple_choice' && !empty($options))
                    <div class="options-grid">
                        @foreach($options as $index => $option)
                        <label class="option-item" id="option-{{ $index }}">
                            <input type="radio" name="answer" value="{{ $option }}" required>
                            <span class="option-box">
                                <span class="option-letter">{{ chr(65 + $index) }}</span>
                                <span class="option-text">{{ $option }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                @elseif($question->type === 'true_false')
                    <div class="options-grid tf">
                        <label class="option-item tf-true">
                            <input type="radio" name="answer" value="True" required>
                            <span class="option-box">
                                <i class="fas fa-check-circle"></i> TRUE
                            </span>
                        </label>
                        <label class="option-item tf-false">
                            <input type="radio" name="answer" value="False" required>
                            <span class="option-box">
                                <i class="fas fa-times-circle"></i> FALSE
                            </span>
                        </label>
                    </div>
                @else
                    <div class="text-answer-input">
                        <textarea name="answer" placeholder="Enter your answer here..." required></textarea>
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn-submit-answer" id="submit-btn">
                        Submit Answer <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar / Visuals -->
        <div class="play-sidebar">
            @php
                $user = Auth::user();
                $currentHP = $user?->hp ?? 0;
                $currentAP = $user?->ap ?? 0;
                $characterData = $user?->getCharacterData() ?? [];
                $powers = is_array($characterData) && isset($characterData['abilities']) ? $characterData['abilities'] : [];
                $currentLevel = $question->level;
                $usedPowers = $attempt->usedPowers->where('level', $currentLevel)->pluck('power_name')->toArray();
            @endphp

            <!-- HP/AP Stats Card -->
            <div class="stats-card">
                <h4><i class="fas fa-user-cog"></i> Hero Status</h4>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-label"><i class="fas fa-heart"></i> HP</span>
                        <div class="stat-bar">
                            <div class="stat-fill hp-fill" style="width: {{ min(($currentHP / 100) * 100, 100) }}%;"></div>
                        </div>
                        <span class="stat-value">{{ $currentHP }}</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-label"><i class="fas fa-bolt"></i> AP</span>
                        <div class="stat-bar">
                            <div class="stat-fill ap-fill" style="width: {{ min(($currentAP / 100) * 100, 100) }}%;"></div>
                        </div>
                        <span class="stat-value">{{ $currentAP }}</span>
                    </div>
                </div>
                <div class="character-badge">
                    <i class="fas fa-dragon"></i> {{ $characterData['name'] ?? 'Hero' }}
                </div>
            </div>

            <!-- Powers Card -->
            <div class="powers-card">
                <h4><i class="fas fa-magic"></i> Powers</h4>
                <div class="powers-list">
                    @forelse($powers as $powerName => $powerDesc)
                        @php
                            $isUsed = in_array($powerName, $usedPowers);
                        @endphp
                        <button type="button" class="power-btn {{ $isUsed ? 'used' : '' }}" 
                                @if(!$isUsed) onclick="usePower('{{ $powerName }}', '{{ addslashes($powerDesc) }}')" @endif
                                title="{{ $powerDesc }}" 
                                {{ $isUsed ? 'disabled' : '' }}>
                            <span class="power-icon-small">
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
                            </span>
                            <span class="power-name">{{ $powerName }}</span>
                            @if($isUsed)
                                <span class="power-used-badge"><i class="fas fa-check"></i> Used</span>
                            @endif
                        </button>
                    @empty
                        <p class="no-powers">No powers available</p>
                    @endforelse
                </div>
            </div>

            <!-- Mini Map showing progress -->
            <div class="mini-map-card">
                @php
                    $positions = [
                        ['left' => 50, 'top' => 86],
                        ['left' => 25, 'top' => 55],
                        ['left' => 15, 'top' => 66],
                        ['left' => 40, 'top' => 40],
                        ['left' => 55, 'top' => 60],
                        ['left' => 75, 'top' => 45],
                        ['left' => 75, 'top' => 80],
                        ['left' => 85, 'top' => 65],
                        ['left' => 80, 'top' => 20],
                    ];
                    $currentLvl = $question->level;
                    $totalLvls = $quest->level;
                    $pos = $positions[($currentLvl - 1) % count($positions)];
                    $progressPercent = ($currentLvl / $totalLvls) * 100;
                    
                    // Map image
                    $mapImage = $quest->map_image ?? 'quest_map_bg.png';
                    $mapImageUrl = str_starts_with($mapImage, 'quest_maps/') 
                        ? asset('storage/' . $mapImage) 
                        : asset('images/' . $mapImage);
                @endphp
                <h4>World Progress</h4>
                <div class="mini-map-visual" style="background-image: url('{{ $mapImageUrl }}'); background-size: cover; background-position: center;">
                    <div class="map-particles">
                        <span class="particle p1"></span>
                        <span class="particle p2"></span>
                        <span class="particle p3"></span>
                    </div>
                    <div class="current-node-pulse" style="left: {{ $pos['left'] }}%; top: {{ $pos['top'] }}%;"></div>
                </div>
                <div class="progress-footer">
                    <div class="mini-progress-bar">
                        <div class="mini-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- Hint Card (can be AI assisted later) -->
            <div class="hint-card">
                <i class="fas fa-lightbulb"></i>
                <p>Need help? Think about the core principles of ASIANISTA.</p>
            </div>
        </div>
    </div>
</div>

<!-- BATTLE MODAL -->
<div id="quest-feedback-modal" class="battle-overlay">
    <div class="battle-card" id="battle-card">
        <div class="battle-scene">

            <!-- RED ENEMY DRAGON (faces RIGHT to breathe on hero) -->
            <div class="fighter dragon-side">
                <div class="dragon-wrap" id="dragon-sprite">
                    <svg class="dragon-svg" id="enemy-dragon-svg" viewBox="0 0 220 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Wing back left -->
                        <path d="M80 90 Q20 30 10 10 Q50 55 90 80 Z" fill="#7f1d1d" opacity="0.8"/>
                        <!-- Wing back right -->
                        <path d="M110 85 Q170 25 195 5 Q155 50 105 78 Z" fill="#991b1b" opacity="0.7"/>
                        <!-- Tail -->
                        <path d="M50 145 Q30 175 10 190 Q25 165 42 148 Z" fill="#b91c1c"/>
                        <path d="M50 145 Q15 185 0 210 Q20 180 48 150 Z" fill="#7f1d1d"/>
                        <!-- Body -->
                        <ellipse cx="90" cy="130" rx="50" ry="38" fill="url(#redBodyGrad)"/>
                        <!-- Belly scales -->
                        <ellipse cx="90" cy="140" rx="35" ry="24" fill="#fca5a5" opacity="0.5"/>
                        <!-- Chest spikes -->
                        <polygon points="70,105 75,88 80,105" fill="#fbbf24"/>
                        <polygon points="85,98 90,80 95,98" fill="#fbbf24"/>
                        <polygon points="100,102 105,85 110,102" fill="#fbbf24"/>
                        <!-- Front leg -->
                        <rect x="65" y="158" width="18" height="32" rx="8" fill="#b91c1c"/>
                        <path d="M63 188 L55 200 M70 192 L65 204 M79 190 L75 203" stroke="#7f1d1d" stroke-width="4" stroke-linecap="round"/>
                        <!-- Back leg -->
                        <rect x="108" y="160" width="18" height="30" rx="8" fill="#b91c1c"/>
                        <path d="M106 188 L98 200 M114 192 L110 204 M122 190 L120 203" stroke="#7f1d1d" stroke-width="4" stroke-linecap="round"/>
                        <!-- Neck -->
                        <path d="M110 100 Q145 75 155 62" stroke="#b91c1c" stroke-width="28" stroke-linecap="round" fill="none"/>
                        <path d="M110 100 Q145 75 155 62" stroke="#fca5a5" stroke-width="12" stroke-linecap="round" fill="none" opacity="0.4"/>
                        <!-- Head -->
                        <ellipse cx="168" cy="58" rx="28" ry="20" fill="#b91c1c"/>
                        <!-- Lower jaw -->
                        <path d="M145 62 Q165 78 192 70" fill="#991b1b" stroke="#7f1d1d" stroke-width="1"/>
                        <!-- Teeth -->
                        <polygon points="152,64 155,74 158,64" fill="white"/>
                        <polygon points="160,66 163,76 166,66" fill="white"/>
                        <polygon points="168,66 171,76 174,66" fill="white"/>
                        <!-- Snout / nostril -->
                        <ellipse cx="193" cy="55" rx="8" ry="6" fill="#991b1b"/>
                        <circle cx="197" cy="53" r="3" fill="#450a0a"/>
                        <!-- Eye -->
                        <circle cx="172" cy="48" r="8" fill="#fbbf24"/>
                        <circle cx="174" cy="47" r="4" fill="#1c1917"/>
                        <circle cx="175" cy="46" r="1.5" fill="white"/>
                        <!-- Horns -->
                        <path d="M160 40 Q158 20 165 10" stroke="#78350f" stroke-width="5" stroke-linecap="round" fill="none"/>
                        <path d="M168 36 Q170 15 178 8" stroke="#78350f" stroke-width="5" stroke-linecap="round" fill="none"/>
                        <!-- Back spines -->
                        <polygon points="100,95 105,75 110,95" fill="#ef4444"/>
                        <polygon points="112,92 117,70 122,92" fill="#ef4444"/>
                        <!-- Defs -->
                        <defs>
                            <radialGradient id="redBodyGrad" cx="40%" cy="40%">
                                <stop offset="0%" stop-color="#ef4444"/>
                                <stop offset="100%" stop-color="#7f1d1d"/>
                            </radialGradient>
                        </defs>
                    </svg>
                    <!-- Enemy dragon fire (shoots right) -->
                    <div class="dragon-fire" id="dragon-fire">
                        <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg" class="fire-svg">
                            <ellipse cx="30" cy="30" rx="30" ry="18" fill="#fbbf24" class="fire-blob fb1"/>
                            <ellipse cx="70" cy="28" rx="25" ry="15" fill="#f97316" class="fire-blob fb2"/>
                            <ellipse cx="105" cy="30" rx="22" ry="13" fill="#ef4444" class="fire-blob fb3"/>
                            <ellipse cx="138" cy="30" rx="18" ry="10" fill="#dc2626" class="fire-blob fb4"/>
                            <ellipse cx="166" cy="30" rx="12" ry="7"  fill="#b91c1c" class="fire-blob fb5"/>
                            <ellipse cx="190" cy="30" rx="8"  rx="8" ry="4"  fill="#7f1d1d" class="fire-blob fb6"/>
                            <!-- Inner bright core -->
                            <ellipse cx="40" cy="30" rx="18" ry="8" fill="#fef9c3" opacity="0.7"/>
                        </svg>
                    </div>
                </div>
                <div class="fighter-label">🔴 Dark Dragon</div>
            </div>

            <!-- VS -->
            <div class="vs-badge" id="vs-badge">VS</div>

            <!-- BLUE HERO DRAGON (faces LEFT to breathe on enemy) -->
            <div class="fighter hero-side">
                <div class="dragon-wrap" id="hero-sprite">
                    <svg class="dragon-svg hero-dragon-svg" viewBox="0 0 220 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Wing back right -->
                        <path d="M140 90 Q200 30 210 10 Q170 55 130 80 Z" fill="#1e3a8a" opacity="0.8"/>
                        <!-- Wing back left -->
                        <path d="M110 85 Q50 25 25 5 Q65 50 115 78 Z" fill="#1d4ed8" opacity="0.7"/>
                        <!-- Tail -->
                        <path d="M170 145 Q190 175 210 190 Q195 165 178 148 Z" fill="#1d4ed8"/>
                        <path d="M170 145 Q205 185 220 210 Q200 180 172 150 Z" fill="#1e3a8a"/>
                        <!-- Body -->
                        <ellipse cx="130" cy="130" rx="50" ry="38" fill="url(#blueBodyGrad)"/>
                        <!-- Belly scales -->
                        <ellipse cx="130" cy="140" rx="35" ry="24" fill="#bfdbfe" opacity="0.5"/>
                        <!-- Chest spikes -->
                        <polygon points="120,105 115,88 110,105" fill="#fbbf24"/>
                        <polygon points="135,98 130,80 125,98" fill="#fbbf24"/>
                        <polygon points="150,102 145,85 140,102" fill="#fbbf24"/>
                        <!-- Front leg -->
                        <rect x="137" y="158" width="18" height="32" rx="8" fill="#1d4ed8"/>
                        <path d="M157 188 L165 200 M150 192 L155 204 M141 190 L145 203" stroke="#1e3a8a" stroke-width="4" stroke-linecap="round"/>
                        <!-- Back leg -->
                        <rect x="94" y="160" width="18" height="30" rx="8" fill="#1d4ed8"/>
                        <path d="M114 188 L122 200 M106 192 L110 204 M98 190 L100 203" stroke="#1e3a8a" stroke-width="4" stroke-linecap="round"/>
                        <!-- Neck -->
                        <path d="M110 100 Q75 75 65 62" stroke="#1d4ed8" stroke-width="28" stroke-linecap="round" fill="none"/>
                        <path d="M110 100 Q75 75 65 62" stroke="#bfdbfe" stroke-width="12" stroke-linecap="round" fill="none" opacity="0.4"/>
                        <!-- Head (faces left) -->
                        <ellipse cx="52" cy="58" rx="28" ry="20" fill="#1d4ed8"/>
                        <!-- Lower jaw -->
                        <path d="M75 62 Q55 78 28 70" fill="#1e3a8a" stroke="#1e3a8a" stroke-width="1"/>
                        <!-- Teeth -->
                        <polygon points="68,64 65,74 62,64" fill="white"/>
                        <polygon points="60,66 57,76 54,66" fill="white"/>
                        <polygon points="52,66 49,76 46,66" fill="white"/>
                        <!-- Snout / nostril -->
                        <ellipse cx="27" cy="55" rx="8" ry="6" fill="#1e3a8a"/>
                        <circle cx="23" cy="53" r="3" fill="#0c1445"/>
                        <!-- Eye -->
                        <circle cx="48" cy="48" r="8" fill="#fbbf24"/>
                        <circle cx="46" cy="47" r="4" fill="#1c1917"/>
                        <circle cx="45" cy="46" r="1.5" fill="white"/>
                        <!-- Horns -->
                        <path d="M60 40 Q62 20 55 10" stroke="#1e3a8a" stroke-width="5" stroke-linecap="round" fill="none"/>
                        <path d="M52 36 Q50 15 42 8" stroke="#1d4ed8" stroke-width="5" stroke-linecap="round" fill="none"/>
                        <!-- Back spines -->
                        <polygon points="120,95 115,75 110,95" fill="#60a5fa"/>
                        <polygon points="108,92 103,70 98,92" fill="#60a5fa"/>
                        <!-- Gold accent scales -->
                        <circle cx="130" cy="115" r="5" fill="#fbbf24" opacity="0.6"/>
                        <circle cx="120" cy="108" r="4" fill="#fbbf24" opacity="0.4"/>
                        <defs>
                            <radialGradient id="blueBodyGrad" cx="60%" cy="40%">
                                <stop offset="0%" stop-color="#3b82f6"/>
                                <stop offset="100%" stop-color="#1e3a8a"/>
                            </radialGradient>
                        </defs>
                    </svg>
                    <!-- Hero dragon fire (shoots LEFT toward enemy) -->
                    <div class="hero-fire" id="hero-fire">
                        <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg" class="fire-svg hero-fire-svg">
                            <ellipse cx="170" cy="30" rx="30" ry="18" fill="#a78bfa" class="fire-blob fb1"/>
                            <ellipse cx="130" cy="28" rx="25" ry="15" fill="#8b5cf6" class="fire-blob fb2"/>
                            <ellipse cx="95"  cy="30" rx="22" ry="13" fill="#7c3aed" class="fire-blob fb3"/>
                            <ellipse cx="62"  cy="30" rx="18" ry="10" fill="#6d28d9" class="fire-blob fb4"/>
                            <ellipse cx="34"  cy="30" rx="12" ry="7"  fill="#5b21b6" class="fire-blob fb5"/>
                            <ellipse cx="10"  cy="30" rx="8"  ry="4"  fill="#4c1d95" class="fire-blob fb6"/>
                            <!-- Inner bright core -->
                            <ellipse cx="158" cy="30" rx="18" ry="8" fill="#f5f3ff" opacity="0.7"/>
                        </svg>
                    </div>
                </div>
                <div class="fighter-label">🔵 Hero Dragon</div>
            </div>
        </div>

        <div class="battle-result" id="battle-result">
            <h2 id="battle-title">Victory!</h2>
            <p id="battle-message">You slayed the challenge!</p>
            <button id="modal-next-btn" class="btn-battle-action">⚔️ Move Forward</button>
        </div>
    </div>
</div>


<style>
    .quest-play-page {
        display: flex;
        flex-direction: column;
        gap: 30px;
        height: 100%;
    }

    .play-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 20px 30px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,35,102,0.05);
    }

    .play-header-left { display: flex; align-items: center; gap: 30px; }
    
    .btn-exit {
        color: #ef4444;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fee2e2;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .btn-exit:hover { background: #fca5a5; transform: scale(1.05); }

    .play-quest-info h2 { font-size: 1.4rem; color: var(--primary); font-weight: 800; margin-bottom: 4px; }
    .step-counter { font-size: 0.8rem; font-weight: 700; color: var(--secondary); opacity: 0.8; }
    .lvl-badge { 
        background: rgba(255, 212, 59, 0.15); 
        color: var(--accent-dark); 
        padding: 3px 12px; 
        border-radius: 8px; 
        font-size: 0.75rem; 
        font-weight: 800; 
        border: 1px solid rgba(255, 212, 59, 0.3);
        margin-left: 10px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .reward-preview .reward-item {
        background: #fffbeb;
        color: #d97706;
        padding: 8px 20px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.9rem;
        border: 1px solid #fde68a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .play-content-container {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 30px;
        flex: 1;
    }

    .question-card {
        background: white;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .question-type-badge {
        position: absolute;
        top: 25px;
        right: 40px;
        background: var(--primary);
        color: white;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .question-text h3 {
        font-size: 1.8rem;
        color: var(--primary);
        line-height: 1.4;
        margin-bottom: 40px;
        font-weight: 800;
        max-width: 90%;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .option-item { cursor: pointer; }
    .option-item input { display: none; }

    .option-box {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px 25px;
        background: #f8fafc;
        border: 2px solid transparent;
        border-radius: 20px;
        transition: all 0.2s;
    }

    .option-letter {
        width: 35px;
        height: 35px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--secondary);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }

    .option-text { font-size: 1rem; font-weight: 700; color: var(--primary); }

    .option-item:hover .option-box { background: #f1f5f9; transform: translateY(-3px); }
    .option-item input:checked + .option-box { 
        background: #eff6ff; 
        border-color: #3b82f6; 
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
    }
    .option-item input:checked + .option-box .option-letter { background: #3b82f6; color: white; }

    /* True/False specific */
    .options-grid.tf { grid-template-columns: repeat(2, 1fr); gap: 30px; }
    .tf .option-box { justify-content: center; padding: 40px; flex-direction: column; gap: 10px; font-weight: 800; font-size: 1.2rem; }
    .tf .option-box i { font-size: 2.5rem; }
    .tf-true .option-box { color: #10b981; }
    .tf-false .option-box { color: #ef4444; }
    .tf-true input:checked + .option-box { background: #ecfdf5; border-color: #10b981; }
    .tf-false input:checked + .option-box { background: #fef2f2; border-color: #ef4444; }

    /* Text answer */
    .text-answer-input textarea {
        width: 100%;
        min-height: 150px;
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        padding: 25px;
        font-family: inherit;
        font-size: 1.1rem;
        resize: none;
        margin-bottom: 40px;
        transition: border-color 0.2s;
    }
    .text-answer-input textarea:focus { outline: none; border-color: var(--accent); }

    .form-actions { display: flex; justify-content: flex-end; }

    .btn-submit-answer {
        background: var(--primary);
        color: white;
        border: none;
        padding: 18px 45px;
        border-radius: 18px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .btn-submit-answer:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,0,0,0.15); }

    .stats-card, .powers-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .stats-card h4, .powers-card h4 {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hero-stats {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 15px;
    }

    .hero-stat {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--secondary);
        width: 50px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-label i { font-size: 0.7rem; }
    .stat-label .fa-heart { color: #ef4444; }
    .stat-label .fa-bolt { color: #3b82f6; }

    .stat-bar {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .stat-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .hp-fill { background: linear-gradient(90deg, #ef4444, #f87171); }
    .ap-fill { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

    .stat-value {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--primary);
        width: 30px;
        text-align: right;
    }

    .character-badge {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .powers-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .power-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: #f8fafc;
        border: 2px solid transparent;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        width: 100%;
    }

    .power-btn:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        transform: translateY(-2px);
    }

    .power-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .power-btn.used {
        opacity: 0.6;
        background: #e2e8f0;
    }

    .power-used-badge {
        margin-left: auto;
        font-size: 0.7rem;
        color: #10b981;
        font-weight: 700;
    }

    .timer-display {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-right: 15px;
        animation: timer-pulse 1s infinite;
    }

    @keyframes timer-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    }

    .timer-display.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        animation: timer-warning 0.5s infinite;
    }

    @keyframes timer-warning {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .power-icon-small {
        width: 35px;
        height: 35px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .power-name {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary);
    }

    .no-powers {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-align: center;
        padding: 20px;
    }

    /* Power Active Effects */
    .power-active-hint {
        background: #fef3c7;
        border: 2px solid #fbbf24;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        display: none;
    }

    .power-active-hint.show {
        display: block;
        animation: hint-pulse 2s infinite;
    }

    @keyframes hint-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
    }

    .power-active-hint i {
        color: #f59e0b;
        margin-right: 8px;
    }

    .power-active-hint strong {
        color: #92400e;
    }

    /* Eliminated option styling for Arcane Analysis */
    .option-item.eliminated {
        opacity: 0.4;
        pointer-events: none;
    }

    .option-item.eliminated .option-box {
        background: #fee2e2;
        text-decoration: line-through;
    }
    .mini-map-card {
        background: white;
        border-radius: 25px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .mini-map-card h4 { font-size: 0.9rem; font-weight: 800; color: var(--primary); margin-bottom: 15px; }
    
    .mini-map-visual {
        position: relative;
        width: 100%;
        aspect-ratio: 16/10;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .map-particles {
        position: absolute;
        inset: 0;
        overflow: hidden;
    }
    
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        animation: float-particle 8s infinite ease-in-out;
    }
    
    .particle.p1 { left: 20%; top: 30%; animation-delay: 0s; }
    .particle.p2 { left: 60%; top: 50%; animation-delay: 2s; }
    .particle.p3 { left: 80%; top: 20%; animation-delay: 4s; }
    
    @keyframes float-particle {
        0%, 100% { transform: translateY(0) scale(1); opacity: 0.6; }
        50% { transform: translateY(-20px) scale(1.5); opacity: 1; }
    }
    
    .current-node-pulse {
        position: absolute;
        width: 15px;
        height: 15px;
        background: var(--accent);
        border-radius: 50%;
        box-shadow: 0 0 15px var(--accent);
        transform: translate(-50%, -50%);
        animation: pulse-mini 1.5s infinite;
    }

    @keyframes pulse-mini {
        0% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(255,212,59,0.8); }
        70% { transform: translate(-50%, -50%) scale(1.5); box-shadow: 0 0 0 10px rgba(255,212,59,0); }
        100% { transform: translate(-50%, -50%) scale(1); }
    }

    .mini-progress-bar { height: 6px; background: #eee; border-radius: 3px; overflow: hidden; }
    .mini-fill { height: 100%; background: var(--accent); }

    .hint-card {
        background: #f0f9ff;
        border: 1px dashed #7dd3fc;
        padding: 20px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .hint-card i { color: #0ea5e9; font-size: 1.2rem; }
    .hint-card p { font-size: 0.85rem; color: #0369a1; font-weight: 600; line-height: 1.4; }

    /* ===== BATTLE MODAL ===== */
    .battle-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .battle-card {
        background: transparent; /* "dont put background" */
        border: none;
        padding: 20px;
        width: 1000px;
        max-width: 95vw;
        animation: battle-drop 0.4s ease-out forwards;
    }

    @keyframes battle-drop {
        from { transform: scale(0.8) translateY(-30px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    .battle-scene {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 30px;
        min-height: 180px;
    }

    .fighter { display: flex; flex-direction: column; align-items: center; gap: 12px; }
    .fighter-label { font-size: 0.8rem; font-weight: 800; color: rgba(255,255,255,0.6); letter-spacing: 1px; text-transform: uppercase; }

    .vs-badge {
        font-size: 2rem;
        font-weight: 900;
        color: var(--accent);
        text-shadow: 0 0 20px rgba(255,212,59,0.6);
        animation: vs-pulse 1s infinite;
    }
    @keyframes vs-pulse {
        0%,100% { transform: scale(1); text-shadow: 0 0 10px rgba(255,212,59,0.4); }
        50%      { transform: scale(1.1); text-shadow: 0 0 30px rgba(255,212,59,0.9); }
    }

    /* SVG DRAGONS */
    .dragon-wrap {
        position: relative;
        width: 280px;
        filter: drop-shadow(0 0 30px rgba(0,0,0,0.5));
    }

    .dragon-svg {
        width: 100%;
        height: auto;
        display: block;
        animation: dragon-float 3s ease-in-out infinite;
    }

    @keyframes dragon-float {
        0%, 100% { transform: translateY(0) rotate(0); }
        50%      { transform: translateY(-10px) rotate(1deg); }
    }

    .hero-dragon-svg {
        filter: drop-shadow(0 0 40px rgba(59,130,246,0.4));
    }

    /* FIRE SVG ANIMATIONS */
    .dragon-fire, .hero-fire {
        position: absolute;
        width: 450px;
        pointer-events: none;
        opacity: 0;
        z-index: 10;
    }

    .dragon-fire { left: 260px; top: 20px; }
    .hero-fire   { right: 260px; top: 20px; }

    .dragon-fire.active, .hero-fire.active {
        opacity: 1;
        transition: opacity 0.1s ease;
    }

    .fire-svg { width: 100%; height: auto; }

    .fire-blob {
        animation: fire-flicker 0.2s infinite alternate;
        transform-origin: center;
    }

    @keyframes fire-flicker {
        to { transform: scale(1.1) translateY(-2px); filter: brightness(1.3); }
    }

    /* Hit reactions */
    .dragon-wrap.hit {
        animation: dragon-shake 0.5s ease-out forwards;
    }

    @keyframes dragon-shake {
        0%   { transform: translateX(0); filter: brightness(3) saturate(0); }
        20%  { transform: translateX(-15px); }
        40%  { transform: translateX(12px); }
        60%  { transform: translateX(-8px); }
        80%  { transform: translateX(4px); }
        100% { transform: translateX(0); filter: brightness(1) saturate(1); }
    }

    /* Battle result area */
    .battle-result {
        text-align: center;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 20px;
        padding: 30px;
        margin-top: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }
    .battle-result h2 { font-size: 2rem; font-weight: 900; margin-bottom: 8px; }
    .battle-result p  { font-size: 1rem; margin-bottom: 25px; font-weight: 600; }

    .battle-result.victory h2 { color: var(--accent); text-shadow: 0 0 20px rgba(255,212,59,0.5); }
    .battle-result.victory p  { color: rgba(255,255,255,0.7); }
    .battle-result.defeat  h2 { color: #f87171; text-shadow: 0 0 20px rgba(248,113,113,0.5); }
    .battle-result.defeat  p  { color: rgba(255,255,255,0.6); }

    .btn-battle-action {
        width: 100%;
        padding: 16px;
        border: none;
        border-radius: 18px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .battle-result.victory .btn-battle-action { background: linear-gradient(135deg, var(--accent), #f59e0b); color: #0f172a; }
    .battle-result.defeat  .btn-battle-action { background: linear-gradient(135deg, #ef4444, #b91c1c); color: white; }
    .btn-battle-action:hover { transform: translateY(-2px) scale(1.02); }
</style>

<script>
// Power usage tracking
let activePower = null;
let eliminatedOptions = [];
let timeRemaining = {{ $quest->time_limit_minutes ? $quest->time_limit_minutes * 60 : 0 }};
let timerInterval = null;
let extraTimeAdded = 0;

// Initialize timer if time limit exists
@if($quest->time_limit_minutes)
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
});
@endif

function startTimer() {
    if (timeRemaining <= 0) return;
    
    const timerDisplay = document.getElementById('timer-count');
    const timerContainer = document.getElementById('quest-timer');
    
    timerInterval = setInterval(() => {
        timeRemaining--;
        
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        // Warning when less than 30 seconds
        if (timeRemaining <= 30 && !timerContainer.classList.contains('warning')) {
            timerContainer.classList.add('warning');
        }
        
        // Time's up
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            handleTimeUp();
        }
    }, 1000);
}

function handleTimeUp() {
    // Disable submit button
    const submitBtn = document.getElementById('submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-clock"></i> Time\'s Up!';
    }
    
    // Call timeout endpoint to move to next question
    fetch('{{ route("student.quest.timeout", [$quest->id, $question->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Update HP display
            if (result.new_hp !== undefined) {
                updateHPDisplay(result.new_hp);
            }
            showBattle('defeat', result.message, () => window.location.href = result.next_url);
        } else {
            showBattle('defeat', 'Time\'s up!', () => window.location.href = result.next_url || '{{ route("student.quest.show", $quest->id) }}');
        }
    })
    .catch(error => {
        console.error('Timeout error:', error);
        showBattle('defeat', 'Time\'s up!', () => {
            window.location.href = '{{ route("student.quest.show", $quest->id) }}';
        });
    });
}

function addExtraTime() {
    extraTimeAdded += 30; // Add 30 seconds
    timeRemaining += 30;
    
    // Remove warning if time is now sufficient
    const timerContainer = document.getElementById('quest-timer');
    if (timeRemaining > 30) {
        timerContainer.classList.remove('warning');
    }
}

function usePower(powerName, powerDesc) {
    const hintBox = document.getElementById('active-power-hint');
    const hintText = document.getElementById('hint-text');
    
    // Record power usage on server
    recordPowerUsage(powerName);
    
    switch(powerName.toLowerCase()) {
        case 'spell of insight':
            // Mage: Show hint
            hintText.innerHTML = '<strong>Spell of Insight:</strong> ' + getHintForQuestion();
            hintBox.classList.add('show');
            activePower = 'insight';
            break;
            
        case 'arcane analysis':
            // Mage: Eliminate one wrong answer
            if (eliminateWrongAnswer()) {
                hintText.innerHTML = '<strong>Arcane Analysis:</strong> One incorrect option has been eliminated!';
                hintBox.classList.add('show');
                activePower = 'analysis';
            }
            break;
            
        case 'time warp':
            // Mage: Add extra time (if timer exists)
            hintText.innerHTML = '<strong>Time Warp:</strong> Extra 30 seconds granted!';
            hintBox.classList.add('show');
            activePower = 'timewarp';
            addExtraTime();
            break;
            
        case 'power strike':
            // Warrior: Double points for next correct answer
            hintText.innerHTML = '<strong>Power Strike:</strong> Next correct answer worth double points!';
            hintBox.classList.add('show');
            activePower = 'powerstrike';
            break;
            
        case 'shield guard':
            // Warrior: Prevent point loss
            hintText.innerHTML = '<strong>Shield Guard:</strong> Protected from HP loss on next wrong answer!';
            hintBox.classList.add('show');
            activePower = 'shield';
            break;
            
            case 'revive':
            // Healer: Allow retry
            hintText.innerHTML = '<strong>Revive:</strong> You may retry this question if answered incorrectly!';
            hintBox.classList.add('show');
            activePower = 'revive';
            break;
            
        case 'focus aura':
            // Healer: Second attempt allowed
            hintText.innerHTML = '<strong>Focus Aura:</strong> You have a second chance on this question!';
            hintBox.classList.add('show');
            activePower = 'focus';
            break;
            
        default:
            // Other powers - show description
            hintText.innerHTML = '<strong>' + powerName + ':</strong> ' + powerDesc;
            hintBox.classList.add('show');
    }
    
    // Disable the power button after use
    const btn = event.target.closest('.power-btn');
    btn.disabled = true;
    btn.classList.add('used');
    
    // Add "Used" badge
    const badge = document.createElement('span');
    badge.className = 'power-used-badge';
    badge.innerHTML = '<i class="fas fa-check"></i> Used';
    btn.appendChild(badge);
}

function recordPowerUsage(powerName) {
    // Send AJAX request to record power usage
    fetch('{{ route("student.quest.use-power", [$quest->id, $attempt->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            power_name: powerName,
            level: {{ $question->level }}
        })
    }).catch(error => console.error('Error recording power usage:', error));
}

function getHintForQuestion() {
    // This would ideally come from the backend
    // For now, provide generic helpful hints
    const hints = [
        'Read the question carefully and look for key words.',
        'Eliminate obviously wrong answers first.',
        'Think about what you learned in the lessons.',
        'Consider the context of the question.',
        'Trust your first instinct - it\'s often correct!'
    ];
    return hints[Math.floor(Math.random() * hints.length)];
}

function eliminateWrongAnswer() {
    const options = document.querySelectorAll('.option-item input[type="radio"]');
    if (options.length === 0) return false;
    
    // Get available wrong options (randomly select one to eliminate)
    const availableOptions = Array.from(options).filter(opt => !opt.checked && !opt.closest('.option-item').classList.contains('eliminated'));
    if (availableOptions.length > 1) {
        const toEliminate = availableOptions[Math.floor(Math.random() * availableOptions.length)];
        toEliminate.closest('.option-item').classList.add('eliminated');
        eliminatedOptions.push(toEliminate.value);
        return true;
    }
    return false;
}

function addExtraTime() {
    // Placeholder for time extension logic
    // This would integrate with a timer system if implemented
    console.log('Extra time added!');
}

function showBattle(outcome, message, onContinue) {
    const modal    = document.getElementById('quest-feedback-modal');
    const result   = document.getElementById('battle-result');
    const title    = document.getElementById('battle-title');
    const msg      = document.getElementById('battle-message');
    const nextBtn  = document.getElementById('modal-next-btn');
    const dragonFire = document.getElementById('dragon-fire');
    const heroFire   = document.getElementById('hero-fire');
    const heroSprite = document.getElementById('hero-sprite');
    const dragonSprite = document.getElementById('dragon-sprite');

    // Reset any previous state
    dragonFire.classList.remove('active');
    heroFire.classList.remove('active');
    heroSprite.classList.remove('hit');
    dragonSprite.classList.remove('hit');
    result.className = 'battle-result';

    title.innerText = outcome === 'victory' ? '⚔️ Victory!' : '💀 Defeat!';
    msg.innerText   = message;
    nextBtn.innerText = outcome === 'victory' ? '🏆 Move Forward!' : '🔄 Try Again';
    nextBtn.onclick = onContinue;
    result.classList.add(outcome);

    modal.style.display = 'flex';

    // Delay so modal animation plays first
    setTimeout(() => {
        if (outcome === 'defeat') {
            // Dragon blows fire → hero gets hit
            dragonFire.classList.add('active');
            setTimeout(() => heroSprite.classList.add('hit'), 300);
        } else {
            // Hero fires back → dragon gets hit
            heroFire.classList.add('active');
            setTimeout(() => dragonSprite.classList.add('hit'), 300);
        }
    }, 300);
}

document.getElementById('quest-answer-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking Lore...';

    const formData = new FormData(this);
    
    // Add active power info
    if (activePower) {
        formData.append('active_power', activePower);
    }
    
    const modal = document.getElementById('quest-feedback-modal');
    const modalNextBtn = document.getElementById('modal-next-btn');

    try {
        const response = await fetch("{{ route('student.quest.submit', [$quest->id, $question->id]) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (result.success) {
            // Update HP display if provided
            if (result.new_hp !== undefined) {
                updateHPDisplay(result.new_hp);
            }
            showBattle('victory', result.message, () => window.location.href = result.next_url);
        } else {
            // Update HP display if HP was deducted
            if (result.new_hp !== undefined) {
                updateHPDisplay(result.new_hp);
            }
            
            // Check if Shield Guard or Revive/Focus Aura is active
            if (activePower === 'shield' || activePower === 'revive' || activePower === 'focus') {
                showBattle('defeat', result.message + ' (Power protected you from HP loss!)', () => {
                    modal.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    document.getElementById('dragon-fire').classList.remove('active');
                    document.getElementById('hero-fire').classList.remove('active');
                    document.getElementById('hero-sprite').classList.remove('hit');
                });
            } else {
                showBattle('defeat', result.message, () => {
                    modal.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    document.getElementById('dragon-fire').classList.remove('active');
                    document.getElementById('hero-fire').classList.remove('active');
                    document.getElementById('hero-sprite').classList.remove('hit');
                });
            }
        }
    } catch (error) {
        console.error('Submission error:', error);
    }
});

function updateHPDisplay(newHP) {
    const hpFill = document.querySelector('.hp-fill');
    const hpValue = document.querySelector('.stat-value');
    if (hpFill && hpValue) {
        hpFill.style.width = Math.min((newHP / 100) * 100, 100) + '%';
        hpValue.textContent = newHP;
    }
}
</script>
@endsection
