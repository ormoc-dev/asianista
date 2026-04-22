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
        align-items: start;
    }

    .battle-arena-card {
        position: relative;
        isolation: isolate;
        width: 100%;
        align-self: start;
        display: flex;
        flex-direction: column;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        border: 2px solid rgba(255,255,255,0.12);
        background: #0f172a;
    }

    .battle-arena {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        max-height: min(72vh, 720px);
        min-height: 380px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .battle-arena-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        z-index: 0;
    }

    .battle-arena-vignette {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(180deg, rgba(15,23,42,0.55) 0%, rgba(15,23,42,0.25) 35%, rgba(15,23,42,0.75) 100%);
        pointer-events: none;
    }

    .battle-arena-header {
        position: relative;
        z-index: 2;
        padding: 16px 22px 0;
    }

    .battle-arena-title {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .battle-arena-title strong {
        color: #f8fafc;
        font-size: 1.05rem;
        font-weight: 800;
        text-shadow: 0 2px 12px rgba(0,0,0,0.6);
    }

    .battle-arena-title span {
        color: rgba(248, 250, 252, 0.75);
        font-size: 0.78rem;
        font-weight: 600;
    }

    .battle-fighters-row {
        position: relative;
        z-index: 2;
        flex: 1;
        flex-shrink: 1;
        min-height: 0;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 16px 96px;
    }

    .battle-fighter { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px; }

    .battle-fighter-label {
        text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        text-align: center;
        max-width: 180px;
    }

    .battle-fighter-label--boss {
        color: #fecaca;
        background: linear-gradient(180deg, transparent, rgba(127, 29, 29, 0.55));
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid rgba(248, 113, 113, 0.35);
    }

    .battle-fighter-label--hero {
        color: #e0f2fe;
        background: linear-gradient(180deg, transparent, rgba(30, 58, 138, 0.5));
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid rgba(125, 211, 252, 0.4);
    }

    .battle-vs {
        flex-shrink: 0;
        font-size: 1.75rem;
        color: #fbbf24;
        text-shadow: 0 0 24px rgba(251, 191, 36, 0.55);
    }

    /* Characters sit directly on the battle BG — no inner card/frame */
    .fighter-stand {
        position: relative;
        width: min(240px, 26vw);
    }

    .fighter-stand .fighter-portrait-img {
        width: 100%;
        max-height: min(220px, 28vh);
        object-fit: contain;
        object-position: bottom center;
        display: block;
    }

    .fighter-portrait-boss {
        filter: drop-shadow(0 16px 28px rgba(0, 0, 0, 0.75)) saturate(1.05);
        animation: boss-idle 3.2s ease-in-out infinite;
    }

    .fighter-portrait-hero {
        transform: scaleX(-1);
        border-radius: 0;
        filter: drop-shadow(0 14px 26px rgba(0, 0, 0, 0.55));
        animation: hero-idle 3.2s ease-in-out infinite;
    }

    /* Combat VFX: shadow surge (boss) vs mind strike (student) — no dragon fire */
    .combat-fx {
        position: absolute;
        pointer-events: none;
        opacity: 0;
        z-index: 12;
        transition: opacity 0.1s ease;
    }

    .combat-fx.active {
        opacity: 1;
    }

    .combat-fx--boss {
        left: 52%;
        top: 45%;
        width: min(380px, 48vw);
        height: 76px;
        transform: translateY(-50%);
    }

    .combat-fx--hero {
        right: 52%;
        top: 45%;
        width: min(380px, 48vw);
        height: 76px;
        transform: translateY(-50%);
    }

    .combat-fx__beam {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        filter: blur(4px);
        transform: scaleX(0.12);
    }

    .combat-fx--boss .combat-fx__beam {
        transform-origin: left center;
    }

    .combat-fx--hero .combat-fx__beam {
        transform-origin: right center;
    }

    .combat-fx.active .combat-fx__beam {
        animation: combat-beam 0.48s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .combat-fx__beam--shadow {
        background: linear-gradient(90deg,
            rgba(192, 38, 211, 0.95),
            rgba(91, 33, 182, 0.85),
            rgba(15, 23, 42, 0.25),
            transparent);
    }

    .combat-fx__beam--mind {
        background: linear-gradient(90deg,
            transparent,
            rgba(45, 212, 191, 0.75),
            rgba(34, 211, 238, 0.95),
            rgba(224, 242, 254, 0.9));
    }

    .combat-fx__ring {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 48px;
        height: 48px;
        margin: -24px 0 0 -24px;
        border-radius: 50%;
        transform: scale(0);
        opacity: 0;
    }

    .combat-fx.active .combat-fx__ring {
        animation: combat-ring 0.55s ease-out forwards;
    }

    .combat-fx__ring--violet {
        box-shadow: 0 0 32px 14px rgba(192, 38, 211, 0.65);
    }

    .combat-fx__ring--cyan {
        box-shadow: 0 0 32px 14px rgba(34, 211, 238, 0.7);
    }

    @keyframes combat-beam {
        0% { transform: scaleX(0.08); opacity: 0.35; }
        35% { opacity: 1; }
        100% { transform: scaleX(1); opacity: 1; }
    }

    @keyframes combat-ring {
        0% { transform: scale(0.2); opacity: 1; }
        100% { transform: scale(2.8); opacity: 0; }
    }

    @keyframes boss-idle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    @keyframes hero-idle {
        0%, 100% { transform: scaleX(-1) translateY(0); }
        50% { transform: scaleX(-1) translateY(-8px); }
    }

    /* Question docked under the battle scene (not a modal overlay) */
    .battle-question-panel {
        flex-shrink: 0;
        width: 100%;
        display: block;
        background: #f1f5f9;
        border-top: 4px solid #0f172a;
    }

    .battle-question-inner {
        display: flex;
        flex-direction: column;
        width: 100%;
        overflow: hidden;
    }

    .q-sheet-head {
        flex-shrink: 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 22px;
        background: linear-gradient(125deg, #0f172a 0%, #1e293b 55%, #334155 100%);
        border-bottom: 3px solid #fbbf24;
    }

    .q-sheet-kicker {
        margin: 0 0 6px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.55);
    }

    .q-sheet-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .q-progress-pill {
        font-size: 0.72rem;
        font-weight: 800;
        color: #0f172a;
        background: #fbbf24;
        padding: 5px 12px;
        border-radius: 999px;
    }

    .q-damage-pill {
        font-size: 0.7rem;
        font-weight: 800;
        color: #fecaca;
        background: rgba(127, 29, 29, 0.85);
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid rgba(248, 113, 113, 0.35);
    }

    .q-damage-pill i {
        margin-right: 4px;
        opacity: 0.9;
    }

    .q-sheet-type-chip {
        flex-shrink: 0;
        align-self: center;
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #0f172a;
        background: #e2e8f0;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.35);
    }

    .q-sheet-body {
        padding: 20px 24px 22px;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        max-height: min(420px, 48vh);
        overflow-y: auto;
    }

    .q-sheet-body .power-active-hint {
        margin-bottom: 14px;
    }

    .question-text--sheet h3 {
        font-size: 1.28rem;
        line-height: 1.45;
        margin: 0 0 20px;
        max-width: none;
        color: #0f172a;
        font-weight: 800;
    }

    .answer-options-area--sheet .options-grid {
        margin-bottom: 18px;
        gap: 12px;
    }

    .answer-options-area--sheet .option-box {
        border-radius: 14px;
        padding: 14px 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left-width: 4px;
        border-left-color: #cbd5e1;
    }

    .answer-options-area--sheet .option-item:hover .option-box {
        border-left-color: #3b82f6;
        background: #f8fafc;
    }

    .answer-options-area--sheet .option-item input:checked + .option-box {
        border-left-color: #1d4ed8;
        background: #eff6ff;
    }

    .form-actions--sheet {
        justify-content: stretch;
        margin-top: 4px;
    }

    .btn-submit-answer--sheet {
        width: 100%;
        justify-content: center;
        border-radius: 14px;
        padding: 16px 22px;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.35);
    }

    .btn-submit-answer--sheet:hover {
        background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
    }

    .quest-battle-hud {
        border: none;
        border-radius: 0;
        padding: 0;
        margin: 0;
        background: transparent;
    }

    .battle-hud-bottom {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 8;
        padding: 14px 18px 18px;
        background: linear-gradient(180deg, transparent, rgba(15, 23, 42, 0.92));
    }

    .hud-hp-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .hud-hp-card {
        background: rgba(255, 255, 255, 0.94);
        border-radius: 12px;
        padding: 10px;
        border: 1px solid rgba(226, 232, 240, 0.9);
    }
    .hud-hp-title {
        font-size: 0.7rem;
        font-weight: 800;
        color: #334155;
        margin-bottom: 6px;
    }
    .hud-hp-track {
        height: 10px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }
    .hud-hp-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }
    .hud-hp-fill.boss { background: linear-gradient(90deg, #dc2626, #f87171); }
    .hud-hp-fill.hero { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .hud-hp-value {
        text-align: right;
        margin-top: 6px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #0f172a;
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

    .stats-card, .powers-card, .music-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .stats-card h4, .powers-card h4, .music-card h4 {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .music-card-controls {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .music-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 16px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        box-shadow: 0 4px 12px rgba(0, 35, 102, 0.2);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .music-toggle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 35, 102, 0.28);
    }

    .music-toggle-btn i {
        font-size: 1rem;
    }

    .music-volume-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .music-volume-icon {
        color: var(--secondary);
        font-size: 0.85rem;
        width: 1.25rem;
        text-align: center;
        flex-shrink: 0;
    }

    .music-volume-slider {
        flex: 1;
        height: 6px;
        border-radius: 3px;
        appearance: none;
        background: linear-gradient(to right, var(--primary) 0%, var(--primary) var(--vol-pct, 45%), #e2e8f0 var(--vol-pct, 45%), #e2e8f0 100%);
        cursor: pointer;
    }

    .music-volume-slider::-webkit-slider-thumb {
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--accent);
        border: 2px solid #0b1020;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .music-volume-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--accent);
        border: 2px solid #0b1020;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .music-volume-slider::-moz-range-track {
        height: 6px;
        border-radius: 3px;
        background: #e2e8f0;
    }

    .music-volume-slider::-moz-range-progress {
        height: 6px;
        border-radius: 3px;
        background: var(--primary);
    }

    .music-hint {
        margin-top: 12px;
        font-size: 0.72rem;
        line-height: 1.4;
        color: var(--text-muted, #94a3b8);
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

    .stat-bar-svg {
        display: block;
        width: 100%;
        height: 100%;
    }

    rect.hp-fill,
    rect.ap-fill {
        transition: width 0.3s ease;
    }

    .mini-map-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .mini-progress-svg {
        display: block;
        width: 100%;
        height: 6px;
    }

    rect.mini-fill {
        fill: var(--accent);
    }

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
        flex: 1;
        min-width: 0;
    }

    .power-ap-tag {
        font-size: 0.68rem;
        font-weight: 700;
        color: #b45309;
        background: #fef3c7;
        padding: 3px 8px;
        border-radius: 999px;
        border: 1px solid #fcd34d;
        flex-shrink: 0;
    }

    .power-btn.used .power-ap-tag {
        display: none;
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

    /* ===== BATTLE RESULT (inside .battle-arena only) ===== */
    .battle-feedback-layer {
        position: absolute;
        inset: 0;
        z-index: 25;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        box-sizing: border-box;
    }

    .battle-feedback-backdrop {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 120% 80% at 50% 40%, rgba(15, 23, 42, 0.45) 0%, rgba(15, 23, 42, 0.72) 100%);
        backdrop-filter: blur(4px);
    }

    .battle-feedback-sheet {
        position: relative;
        width: 100%;
        max-width: 400px;
        animation: battle-sheet-in 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    @keyframes battle-sheet-in {
        from { transform: translateY(12px) scale(0.96); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
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

    /* Hit reactions (boss frame vs student portrait) */
    .fighter-stand--boss.hit {
        animation: frame-shake-boss 0.5s ease-out forwards;
    }

    .fighter-stand--boss.hit .fighter-portrait-boss {
        animation: none;
    }

    .fighter-stand--hero.hit .fighter-portrait-hero {
        animation: frame-shake-hero 0.5s ease-out forwards;
    }

    @keyframes frame-shake-hero {
        0%   { transform: scaleX(-1) translateX(0); filter: brightness(1.6) saturate(0.5); }
        25%  { transform: scaleX(-1) translateX(14px); }
        50%  { transform: scaleX(-1) translateX(-12px); }
        75%  { transform: scaleX(-1) translateX(6px); }
        100% { transform: scaleX(-1) translateX(0); filter: brightness(1) saturate(1); }
    }

    @keyframes frame-shake-boss {
        0%   { transform: translateX(0); filter: brightness(1.5); }
        25%  { transform: translateX(-14px); }
        50%  { transform: translateX(12px); }
        75%  { transform: translateX(-6px); }
        100% { transform: translateX(0); filter: brightness(1); }
    }

    .battle-result {
        position: relative;
        text-align: center;
        padding: 28px 24px 24px;
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(165deg, rgba(30, 41, 59, 0.97) 0%, rgba(15, 23, 42, 0.98) 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.4),
            0 24px 50px rgba(0, 0, 0, 0.45);
    }

    .battle-result::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.5), transparent);
        opacity: 0.9;
    }

    .battle-result.victory::before {
        background: linear-gradient(90deg, #b45309, #fbbf24, #fde68a, #fbbf24, #b45309);
    }

    .battle-result.defeat::before {
        background: linear-gradient(90deg, #7f1d1d, #f87171, #fecaca, #f87171, #7f1d1d);
    }

    .battle-result__badge {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border: 2px solid rgba(255, 255, 255, 0.15);
    }

    .battle-result.victory .battle-result__badge {
        background: linear-gradient(145deg, rgba(251, 191, 36, 0.25), rgba(245, 158, 11, 0.15));
        color: #fbbf24;
        box-shadow: 0 0 28px rgba(251, 191, 36, 0.35);
    }

    .battle-result.defeat .battle-result__badge {
        background: linear-gradient(145deg, rgba(248, 113, 113, 0.2), rgba(185, 28, 28, 0.2));
        color: #fecaca;
        box-shadow: 0 0 24px rgba(248, 113, 113, 0.25);
    }

    .battle-result__eyebrow {
        margin: 0 0 6px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.45);
    }

    .battle-result__title {
        margin: 0 0 12px;
        font-size: 1.65rem;
        font-weight: 900;
        line-height: 1.2;
        color: #f8fafc;
    }

    .battle-result.victory .battle-result__title {
        color: #fef3c7;
        text-shadow: 0 0 24px rgba(251, 191, 36, 0.35);
    }

    .battle-result.defeat .battle-result__title {
        color: #fecaca;
        text-shadow: 0 0 20px rgba(248, 113, 113, 0.3);
    }

    .battle-result__message {
        margin: 0 0 22px;
        font-size: 0.95rem;
        line-height: 1.55;
        font-weight: 600;
        color: rgba(226, 232, 240, 0.88);
    }

    @media (max-width: 768px) {
        .battle-fighters-row {
            flex-direction: column;
            padding: 12px 12px 108px;
            gap: 20px;
        }
        .battle-vs { font-size: 1.35rem; }
        .fighter-stand { width: min(200px, 40vw); }
        .q-sheet-body {
            max-height: min(360px, 45vh);
        }
        .battle-feedback-sheet {
            max-width: 100%;
        }
    }

    .btn-battle-action--result {
        width: 100%;
        padding: 14px 20px;
        border: none;
        border-radius: 14px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .battle-result.victory .btn-battle-action--result {
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        color: #0f172a;
        box-shadow: 0 8px 24px rgba(251, 191, 36, 0.35);
    }

    .battle-result.defeat .btn-battle-action--result {
        background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%);
        color: #fff;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);
    }

    .btn-battle-action--result:hover {
        transform: translateY(-2px);
    }

    .btn-battle-action__icon {
        font-size: 0.85rem;
        opacity: 0.9;
    }
</style>
