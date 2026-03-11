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
                </div>
            </div>
        </div>

        <div class="play-header-right">
            <div class="reward-preview">
                <span class="reward-item"><i class="fas fa-star"></i> {{ $question->points }} XP</span>
            </div>
        </div>
    </div>

    <div class="play-content-container">
        <!-- Question Area -->
        <div class="question-card">
            <div class="question-type-badge">{{ str_replace('_', ' ', ucfirst($question->type)) }}</div>
            
            <div class="question-text">
                <h3>{!! nl2br(e($question->question)) !!}</h3>
            </div>

            <form id="quest-answer-form" class="answer-options-area">
                @csrf
                @if($question->type === 'multiple_choice' && $question->options)
                    <div class="options-grid">
                        @foreach($question->options as $index => $option)
                        <label class="option-item">
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
            <!-- Mini Map showing progress -->
            <div class="mini-map-card">
                <h4>World Progress</h4>
                <div class="mini-map-visual">
                    <img src="{{ asset('images/quest_map_bg.png') }}" class="mini-map-bg">
                    <div class="current-node-pulse" style="left: 50%; top: 50%;"></div>
                </div>
                <div class="progress-footer">
                    <div class="mini-progress-bar">
                        <div class="mini-fill" style="width: 25%;"></div>
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

    /* SIDEBAR */
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
    
    .mini-map-bg { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.6); }

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
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
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
        width: 350px;
        pointer-events: none;
        opacity: 0;
        z-index: 10;
    }

    .dragon-fire { left: 160px; top: 20px; }
    .hero-fire   { right: 160px; top: 20px; }

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
            showBattle('victory', result.message, () => window.location.href = result.next_url);
        } else {
            showBattle('defeat', result.message, () => {
                modal.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                // reset fires
                document.getElementById('dragon-fire').classList.remove('active');
                document.getElementById('hero-fire').classList.remove('active');
                document.getElementById('hero-sprite').classList.remove('hit');
                document.getElementById('dragon-sprite').classList.remove('hit');
            });
        }
    } catch (error) {
        console.error('Submission error:', error);
    }
});
</script>
@endsection
