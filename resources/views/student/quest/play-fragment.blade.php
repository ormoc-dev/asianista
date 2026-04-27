<div class="quest-play-page">
    @php
        $user = Auth::user();
        $currentHP = $user?->hp ?? 0;
        $currentAP = $user?->ap ?? 0;
        $characterData = $user?->getCharacterData() ?? [];
        $maxHP = (int) ($characterData['hp'] ?? 100);
        $maxAP = (int) ($characterData['ap'] ?? 100);
        $hpForBars = min($currentHP, $maxHP);
        $hpBarPct = $maxHP > 0 ? min(100, max(0, (int) round(($hpForBars / $maxHP) * 100))) : 0;
        $apBarPct = $maxAP > 0 ? min(100, max(0, (int) round(($currentAP / $maxAP) * 100))) : 0;
        $powers = is_array($characterData) && isset($characterData['abilities']) ? $characterData['abilities'] : [];
        $currentLevel = $question->level;
        $usedPowers = $attempt->usedPowers->where('level', $currentLevel)->pluck('power_name')->toArray();
        $orderedQuestions = $quest->questions->sort(function ($a, $b) {
            return $a->level <=> $b->level ?: $a->id <=> $b->id;
        })->values();
        $currentLevelQuestions = $orderedQuestions->where('level', $question->level)->values();
        $bossMaxHp = max(1, $currentLevelQuestions->count());
        $outcomes = is_array($attempt->question_outcomes ?? null) ? $attempt->question_outcomes : [];
        $bossCurrentHp = $bossMaxHp;
        foreach ($currentLevelQuestions as $lvlQuestion) {
            if ((int) $lvlQuestion->id >= (int) $question->id) {
                break;
            }
            $key = (string) $lvlQuestion->id;
            if (($outcomes[$key] ?? null) === true || ($outcomes[$lvlQuestion->id] ?? null) === true) {
                $bossCurrentHp--;
            }
        }
        $bossCurrentHp = max(0, $bossCurrentHp);
        $bossImageFiles = ['b1.png', 'b2.png', 'b3.png', 'b4.png', 'b5.png'];
        $bossNames = ['Fire Stalker', 'Stormfang', 'Night Warden', 'Venomclaw', 'Abyss Roar'];
        $bossSeed = abs(crc32($quest->id . '-' . $question->level));
        $bossIndex = $bossSeed % count($bossImageFiles);
        $bossImageUrl = asset('images/boss/' . $bossImageFiles[$bossIndex]);
        $bossName = $bossNames[$bossIndex] . ' Lvl ' . $question->level;
        $studentName = $user?->name ?? 'Hero';
        $studentProfileUrl = asset('images/' . ($user->profile_pic ?? 'default-pp.png'));
        $battleBgFiles = ['battle_bg/bg1.jpg', 'battle_bg/bg2.png', 'battle_bg/bg3.png', 'battle_bg/bg4.png', 'battle_bg/bg5.png'];
        $battleBgSeed = abs(crc32($quest->id . '-' . $question->level . '-' . $question->id));
        $battleBgUrl = asset('images/' . $battleBgFiles[$battleBgSeed % count($battleBgFiles)]);
        $levelPins = \App\Models\QuestMapLayout::pinsForQuest($quest);
        $mapImage = $quest->map_image ?? 'quest_map_bg.png';
        $mapImageUrl = str_starts_with($mapImage, 'quest_maps/')
            ? asset('storage/' . $mapImage)
            : asset('images/' . $mapImage);
        $qIndexInLevel = $currentLevelQuestions->search(fn ($q) => (int) $q->id === (int) $question->id);
        $qIndexInLevel = $qIndexInLevel === false ? 0 : (int) $qIndexInLevel;
        $questionOrdinalInLevel = $qIndexInLevel + 1;
        $heroCharacter = strtolower(trim((string) (($user?->character) ?? 'warrior')));
        if (! in_array($heroCharacter, ['warrior', 'mage', 'healer'], true)) {
            $heroCharacter = 'warrior';
        }
    @endphp
    <div id="quest-play-config" hidden
        data-time-seconds="{{ (int) ($quest->time_limit_minutes ? $quest->time_limit_minutes * 60 : 0) }}"
        data-csrf="{{ csrf_token() }}"
        data-timeout-url="{{ route('student.quest.timeout', [$quest->id, $question->id]) }}"
        data-use-power-url="{{ route('student.quest.use-power', [$quest->id, $attempt->id]) }}"
        data-submit-url="{{ route('student.quest.submit', [$quest->id, $question->id]) }}"
        data-quest-show-url="{{ route('student.quest.show', $quest->id) }}"
        data-question-level="{{ (int) $question->level }}"
        data-question-type="{{ $question->type }}"
        data-correct-answer="{{ e($question->answer) }}"
        data-boss-name="{{ $bossName }}"
        data-student-name="{{ $studentName }}"
        data-boss-max-hp="{{ $bossMaxHp }}"
        data-boss-current-hp="{{ $bossCurrentHp }}"
        data-hero-character="{{ $heroCharacter }}"
        data-require-fullscreen="{{ ($quest->require_fullscreen ?? false) ? '1' : '0' }}"
        data-quest-id="{{ (int) $quest->id }}"></div>

    <div class="play-content-container">
        <!-- Battle arena + question modal layer -->
        <div class="battle-arena-card">
            <div class="battle-arena">
                <div class="battle-arena-bg" style="background-image: url('{{ $battleBgUrl }}');"></div>
                <div class="battle-arena-vignette" aria-hidden="true"></div>
                    <div class="battle-referee" id="battle-referee" aria-hidden="true">
                        <model-viewer
                            class="battle-referee__model"
                            src="{{ asset('images/referee/tiny_planet_friends_3d-tinyplanet-2829.glb') }}"
                            camera-controls
                            interaction-prompt="none"
                            exposure="1"
                            shadow-intensity="0.8"></model-viewer>
                    </div>
                    <div id="active-power-hint" class="power-active-hint power-active-hint--arena">
                        <i class="fas fa-magic"></i> <span id="hint-text"></span>
                    </div>

                <div class="battle-fighters-row">
                    <div class="fighter battle-fighter">
                        <div class="fighter-stand fighter-stand--boss fighter-portrait-wrap" id="dragon-sprite">
                            <img src="{{ $bossImageUrl }}" alt="" class="fighter-portrait-img fighter-portrait-boss">
                            <div class="combat-fx combat-fx--boss" id="dragon-fire" aria-hidden="true">
                                <div class="combat-fx__beam combat-fx__beam--shadow" id="boss-beam"></div>
                                <div class="combat-fx__ring combat-fx__ring--violet" id="boss-ring"></div>
                            </div>
                        </div>
                    </div>

                    <div class="vs-badge battle-vs">VS</div>

                    <div class="fighter battle-fighter">
                        <div class="fighter-stand fighter-stand--hero fighter-portrait-wrap" id="hero-sprite">
                            <img src="{{ $studentProfileUrl }}" alt="" class="fighter-portrait-img fighter-portrait-hero">
                            <div class="combat-fx combat-fx--hero" id="hero-fire" aria-hidden="true">
                                <div class="combat-fx__beam combat-fx__beam--mind" id="hero-beam"></div>
                                <div class="combat-fx__ring combat-fx__ring--cyan" id="hero-ring"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quest-battle-hud battle-hud-bottom">
                    <div class="hud-hp-row">
                        <div class="hud-hp-card">
                            <div class="hud-hp-title"><span id="hud-boss-name-bottom">{{ $bossName }}</span> HP</div>
                            <div class="hud-hp-track">
                                <div id="hud-boss-hp-fill" class="hud-hp-fill boss" data-width="{{ (int) round(($bossCurrentHp / max(1, $bossMaxHp)) * 100) }}"></div>
                            </div>
                            <div class="hud-hp-value"><span id="hud-boss-hp-value">{{ $bossCurrentHp }}</span> / {{ $bossMaxHp }}</div>
                        </div>
                        <div class="hud-hp-card">
                            <div class="hud-hp-title"><span id="hud-student-name-bottom">{{ $studentName }}</span> HP</div>
                            <div class="hud-hp-track">
                                <div id="hud-student-hp-fill" class="hud-hp-fill hero" data-width="{{ $hpBarPct }}"></div>
                            </div>
                            <div class="hud-hp-value"><span id="hud-student-hp-value">{{ $hpForBars }}</span> / {{ $maxHP }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="battle-question-panel" class="battle-question-panel">
                <div class="battle-question-inner">
                    <header class="q-sheet-head">
                        <div class="q-sheet-head-main">
                            <p class="q-sheet-kicker">Challenge</p>
                            <div class="q-sheet-meta-row">
                                <span class="q-progress-pill">Round {{ $questionOrdinalInLevel }} / {{ $bossMaxHp }}</span>
                                @if($quest->hp_penalty)
                                <span class="q-damage-pill"><i class="fas fa-heart-broken"></i> −{{ $quest->hp_penalty }} HP if wrong</span>
                                @endif
                            </div>
                        </div>
                        <span class="q-sheet-type-chip">{{ str_replace('_', ' ', ucfirst($question->type)) }}</span>
                    </header>

                    <div class="q-sheet-body">
                        <div class="question-text question-text--sheet">
                            <h3>{!! nl2br(e($question->question)) !!}</h3>
                        </div>

                        <form id="quest-answer-form" class="answer-options-area answer-options-area--sheet">
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

                            <div class="form-actions form-actions--sheet">
                                <button type="submit" class="btn-submit-answer btn-submit-answer--sheet" id="submit-btn">
                                    Lock in answer <i class="fas fa-bolt"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: mission + stats + powers (scrollable) -->
        <div class="play-sidebar">
            <div class="play-sidebar-mission">
                <span class="btn-exit btn-exit--sidebar btn-exit--locked" role="note" tabindex="-1" title="You cannot leave mid-challenge from this screen. Complete the round or finish the quest from the victory flow.">
                    <i class="fas fa-lock" aria-hidden="true"></i> Exit locked
                </span>
                <p class="btn-exit-hint">Stay in this challenge until you continue after a result. Your progress is saved automatically.</p>
                <h2 class="play-sidebar-mission__title">{{ $quest->title }}</h2>
                <div class="play-sidebar-mission__meta">
                    <span class="step-counter">Objective {{ $attempt->quest->questions->pluck('id')->search($question->id) + 1 }} of {{ $attempt->quest->questions->count() }}</span>
                    <span class="lvl-badge lvl-badge--sidebar"><i class="fas fa-medal"></i> LVL {{ $question->level }}</span>
                </div>
                <div class="play-sidebar-mission__actions">
                    @if($quest->time_limit_minutes)
                    <div class="timer-display timer-display--sidebar" id="quest-timer">
                        <i class="fas fa-clock"></i> <span id="timer-count">{{ $quest->time_limit_minutes }}:00</span>
                    </div>
                    @endif
                    <div class="reward-preview reward-preview--sidebar">
                        <span class="reward-item"><i class="fas fa-star"></i> {{ $question->points }} XP</span>
                    </div>
                </div>
            </div>

            <!-- HP/AP Stats Card -->
            <div class="stats-card">
                <div id="hero-stat-max" data-max-hp="{{ $maxHP }}" data-max-ap="{{ $maxAP }}" hidden></div>
                <h4><i class="fas fa-user-cog"></i> Hero Status</h4>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-label"><i class="fas fa-heart"></i> HP</span>
                        <div class="stat-bar">
                            <svg class="stat-bar-svg" viewBox="0 0 100 8" preserveAspectRatio="none" width="100%" height="8" aria-hidden="true">
                                <defs>
                                    <linearGradient id="questPlayHpGrad" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#ef4444" />
                                        <stop offset="100%" stop-color="#f87171" />
                                    </linearGradient>
                                </defs>
                                <rect class="hp-fill" x="0" y="0" height="8" rx="4" fill="url(#questPlayHpGrad)" width="{{ $hpBarPct }}" />
                            </svg>
                        </div>
                        <span class="stat-value js-hp-value">{{ $currentHP }}</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-label"><i class="fas fa-bolt"></i> AP</span>
                        <div class="stat-bar">
                            <svg class="stat-bar-svg" viewBox="0 0 100 8" preserveAspectRatio="none" width="100%" height="8" aria-hidden="true">
                                <defs>
                                    <linearGradient id="questPlayApGrad" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#3b82f6" />
                                        <stop offset="100%" stop-color="#60a5fa" />
                                    </linearGradient>
                                </defs>
                                <rect class="ap-fill" x="0" y="0" height="8" rx="4" fill="url(#questPlayApGrad)" width="{{ $apBarPct }}" />
                            </svg>
                        </div>
                        <span class="stat-value js-ap-value">{{ $currentAP }}</span>
                    </div>
                </div>
                <div class="character-badge">
                    <i class="fas fa-dragon"></i> {{ $characterData['name'] ?? 'Hero' }}
                </div>
            </div>

            <!-- Background music (student controls volume / play) -->
            <div class="music-card">
                <h4><i class="fas fa-music"></i> Battle music</h4>
                <div class="music-card-controls">
                    <button type="button" id="quest-music-toggle" class="music-toggle-btn" aria-pressed="false" aria-label="Play background music">
                        <i class="fas fa-play" aria-hidden="true"></i>
                    </button>
                    <div class="music-volume-wrap">
                        <span class="music-volume-icon" aria-hidden="true"><i class="fas fa-volume-up"></i></span>
                        <input type="range" id="quest-music-volume" class="music-volume-slider" min="0" max="100" value="45" aria-label="Background music volume">
                    </div>
                </div>
                <p class="music-hint">Controls the track above (no page reload between objectives). Tap play if your browser pauses after a gesture.</p>
            </div>

            <!-- Powers Card -->
            <div class="powers-card">
                <h4><i class="fas fa-magic"></i> Powers</h4>
                <div class="powers-list" id="quest-powers-list">
                    @forelse($powers as $powerName => $powerDesc)
                        @php
                            $isUsed = in_array($powerName, $usedPowers);
                            $powerApCost = \App\Models\User::apCostForPower($powerName);
                        @endphp
                        <button type="button" class="power-btn {{ $isUsed ? 'used' : '' }}"
                                @if(!$isUsed) data-power-name="{{ $powerName }}" data-power-desc="{{ e($powerDesc) }}" @endif
                                title="{{ $powerDesc }} (Costs {{ $powerApCost }} AP)"
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
                            @if(!$isUsed)
                                <span class="power-ap-tag">{{ $powerApCost }} AP</span>
                            @endif
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
                    $currentLvl = $question->level;
                    $totalLvls = $quest->level;
                    $pos = $levelPins[$currentLvl - 1] ?? ($levelPins[0] ?? ['left' => 50, 'top' => 50]);
                    $progressPercent = $totalLvls > 0 ? ($currentLvl / $totalLvls) * 100 : 0;
                @endphp
                <h4>World Progress</h4>
                <div class="mini-map-visual"
                     data-quest-id="{{ (int) $quest->id }}"
                     data-current-level="{{ (int) $currentLvl }}"
                     data-total-levels="{{ (int) $totalLvls }}">
                    <img src="{{ $mapImageUrl }}" alt="" class="mini-map-bg-img" width="800" height="500" decoding="async">
                    <div class="map-particles">
                        <span class="particle p1"></span>
                        <span class="particle p2"></span>
                        <span class="particle p3"></span>
                    </div>
                    @foreach($levelPins as $pinIndex => $pin)
                        <span class="world-progress-pin" hidden
                              data-level="{{ $pinIndex + 1 }}"
                              data-left="{{ sprintf('%.3f', (float) ($pin['left'] ?? 50)) }}"
                              data-top="{{ sprintf('%.3f', (float) ($pin['top'] ?? 50)) }}"></span>
                    @endforeach
                    <div class="map-hero-marker" aria-hidden="true">
                        <i class="fas fa-walking"></i>
                    </div>
                    <div class="current-node-pulse" data-left="{{ sprintf('%.3f', (float) ($pos['left'] ?? 50)) }}" data-top="{{ sprintf('%.3f', (float) ($pos['top'] ?? 50)) }}"></div>
                </div>
                <div class="progress-footer">
                    <div class="mini-progress-bar">
                        <svg class="mini-progress-svg" viewBox="0 0 100 6" preserveAspectRatio="none" width="100%" height="6" aria-hidden="true">
                            <rect class="mini-fill" x="0" y="0" height="6" rx="3" width="{{ min(100, max(0, (int) round($progressPercent))) }}" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Hint Card (can be AI assisted later) -->
            <div class="hint-card">
                <i class="fas fa-lightbulb"></i>
                <p>Need help? Think about the core principles of ASIANISTA.</p>
            </div>
        </div>

        <div id="quest-feedback-modal" class="battle-feedback-layer" aria-hidden="true">
            <div class="battle-feedback-backdrop"></div>
            <div class="battle-feedback-sheet" id="battle-card">
                <div class="battle-result" id="battle-result">
                    <div class="battle-result__badge" id="battle-result-badge" aria-hidden="true"></div>
                    <p class="battle-result__eyebrow" id="battle-result-eyebrow">Outcome</p>
                    <h2 class="battle-result__title" id="battle-title">Victory!</h2>
                    <p class="battle-result__message" id="battle-message">You slayed the challenge!</p>
                    <button type="button" id="modal-next-btn" class="btn-battle-action btn-battle-action--result">
                        <span class="btn-battle-action__text">Continue</span>
                        <i class="fas fa-chevron-right btn-battle-action__icon"></i>
                    </button>
                </div>
                <div class="battle-level-transition" id="battle-level-transition" hidden>
                    <p class="battle-level-transition__eyebrow">Level Cleared</p>
                    <h3 class="battle-level-transition__title">Travelling to Next Level...</h3>
                    <div class="battle-level-transition__map" id="battle-level-transition-map" data-total-levels="{{ max(1, (int) $quest->level) }}">
                        <img src="{{ $mapImageUrl }}" alt="" class="battle-level-transition__map-img" width="800" height="500" decoding="async">
                        @foreach($levelPins as $pinIndex => $pin)
                            <span class="battle-level-pin" hidden
                                  data-level="{{ $pinIndex + 1 }}"
                                  data-left="{{ sprintf('%.3f', (float) ($pin['left'] ?? 50)) }}"
                                  data-top="{{ sprintf('%.3f', (float) ($pin['top'] ?? 50)) }}"></span>
                        @endforeach
                        <span class="battle-level-pin-active" id="battle-level-pin-active"></span>
                        <span class="battle-level-hero" id="battle-level-hero">
                            <img src="{{ $studentProfileUrl }}" alt="Hero avatar">
                        </span>
                    </div>
                    <div class="battle-level-transition__countdown-wrap" id="battle-level-countdown-wrap" aria-live="polite" aria-atomic="true" hidden>
                        <span class="battle-level-transition__countdown" id="battle-level-countdown">5</span>
                    </div>
                    <p class="battle-level-transition__hint">Your hero is moving to the next objective...</p>
                </div>
            </div>
        </div>
    </div>

    @if($quest->require_fullscreen ?? false)
    <div id="quest-fullscreen-gate" class="quest-fullscreen-gate" aria-hidden="true">
        <div class="quest-fullscreen-gate__backdrop" aria-hidden="true"></div>
        <div class="quest-fullscreen-gate__dialog" role="dialog" aria-modal="true" aria-labelledby="quest-fs-gate-title">
            <div class="quest-fullscreen-gate__icon" aria-hidden="true"><i class="fas fa-expand-arrows-alt"></i></div>
            <h2 id="quest-fs-gate-title" class="quest-fullscreen-gate__title">Before you begin this challenge</h2>
            <p class="quest-fullscreen-gate__lead">Your school asks you to use <strong>fullscreen</strong> so the battle and questions fill the screen and you can stay focused.</p>

            <div class="quest-fullscreen-gate__section quest-fullscreen-gate__section--rules">
                <h3 class="quest-fullscreen-gate__sub">Rules</h3>
                <ul class="quest-fullscreen-gate__list">
                    <li>Stay in this challenge until you finish or your teacher tells you to stop.</li>
                    <li>Keep this tab in front—no search, notes, or AI unless your teacher allows it.</li>
                    <li>Pressing <kbd>Esc</kbd> leaves fullscreen; this screen comes back until you enter fullscreen again.</li>
                </ul>
            </div>

            <p class="quest-fullscreen-gate__ack">Enter fullscreen below to continue. Breaking these rules may affect how your attempt is counted.</p>

            <div class="quest-fullscreen-gate__actions">
                <button type="button" id="quest-fs-gate-enter" class="btn-quest-fs-gate btn-quest-fs-gate--primary">
                    <i class="fas fa-expand" aria-hidden="true"></i> Enter fullscreen &amp; continue
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
