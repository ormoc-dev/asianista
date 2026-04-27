<style>
    .quest-play-page {
        display: flex;
        flex-direction: column;
        gap: 0;
        flex: 1;
        min-height: 0;
        max-height: 100%;
        height: 100%;
    }

    .btn-exit {
        color: #ef4444;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fee2e2;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .btn-exit:hover { background: #fca5a5; transform: scale(1.02); }

    .btn-exit--sidebar {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
    }

    .btn-exit--locked {
        cursor: not-allowed;
        pointer-events: none;
        opacity: 0.72;
        background: #e2e8f0;
        color: #64748b;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.45);
    }

    .btn-exit--locked:hover {
        background: #e2e8f0;
        transform: none;
    }

    .btn-exit-hint {
        margin: 0 0 10px;
        font-size: 0.68rem;
        line-height: 1.35;
        font-weight: 600;
        color: #64748b;
    }

    .quest-play-page--fs-gate-open {
        overflow: hidden;
    }

    .quest-fullscreen-gate {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10050;
        align-items: center;
        justify-content: center;
        padding: 16px;
        box-sizing: border-box;
        isolation: isolate;
    }

    /* Above student dashboard modals / overlays (often 9999–99999) so the gate stays clickable */
    body.quest-play-fullscreen .quest-fullscreen-gate.is-open {
        z-index: 200000;
    }

    .quest-fullscreen-gate.is-open {
        display: flex;
    }

    .quest-fullscreen-gate__backdrop {
        position: absolute;
        inset: 0;
        z-index: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(6px);
    }

    .quest-fullscreen-gate__dialog {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: min(92vw, 640px);
        max-height: min(90vh, 720px);
        overflow-y: auto;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 40%);
        border-radius: 20px;
        padding: 22px 22px 20px;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(148, 163, 184, 0.25);
        box-sizing: border-box;
    }

    .quest-fullscreen-gate__icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        font-size: 1.25rem;
    }

    .quest-fullscreen-gate__title {
        margin: 0 0 10px;
        font-size: 1.15rem;
        font-weight: 900;
        color: #0f172a;
        text-align: center;
        line-height: 1.25;
    }

    .quest-fullscreen-gate__lead {
        margin: 0 0 14px;
        font-size: 0.88rem;
        line-height: 1.45;
        color: #334155;
        text-align: center;
        font-weight: 600;
    }

    .quest-fullscreen-gate__section {
        margin-bottom: 12px;
        padding: 12px 14px;
        background: #f1f5f9;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.35);
    }

    .quest-fullscreen-gate__section--rules {
        background: #fff7ed;
        border-color: rgba(251, 146, 60, 0.35);
    }

    .quest-fullscreen-gate__sub {
        margin: 0 0 8px;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
    }

    .quest-fullscreen-gate__section--rules .quest-fullscreen-gate__sub {
        color: #c2410c;
    }

    .quest-fullscreen-gate__list {
        margin: 0;
        padding-left: 1.15rem;
        font-size: 0.78rem;
        line-height: 1.45;
        color: #334155;
        font-weight: 600;
    }

    .quest-fullscreen-gate__list li + li {
        margin-top: 6px;
    }

    .quest-fullscreen-gate__list kbd {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-family: ui-monospace, monospace;
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
    }

    .quest-fullscreen-gate__ack {
        margin: 0 0 16px;
        font-size: 0.72rem;
        line-height: 1.4;
        color: #64748b;
        text-align: center;
        font-weight: 600;
    }

    .quest-fullscreen-gate__actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-quest-fs-gate {
        width: 100%;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.82rem;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 2px solid transparent;
    }

    .btn-quest-fs-gate--primary {
        border-color: rgba(37, 99, 235, 0.4);
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
        color: #f8fafc;
    }

    .btn-quest-fs-gate--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
    }

    .btn-quest-fs-gate--secondary {
        border-color: rgba(148, 163, 184, 0.55);
        background: #fff;
        color: #475569;
    }

    .btn-quest-fs-gate--secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .play-sidebar-mission {
        flex-shrink: 0;
        background: #fff;
        border-radius: 16px;
        padding: 14px 14px 12px;
        
    }

    .play-sidebar-mission__title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1.3;
        margin: 10px 0 8px;
    }

    .play-sidebar-mission__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .step-counter { font-size: 0.72rem; font-weight: 700; color: var(--secondary); opacity: 0.85; }
    .lvl-badge {
        background: rgba(255, 212, 59, 0.15);
        color: var(--accent-dark);
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
        border: 1px solid rgba(255, 212, 59, 0.3);
        margin-left: 0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .lvl-badge--sidebar { flex-shrink: 0; }

    .play-sidebar-mission__actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
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

    .reward-preview--sidebar .reward-item {
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
        padding: 7px 12px;
        font-size: 0.8rem;
    }

    .play-sidebar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 0;
        max-height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-gutter: stable;
        padding-right: 4px;
    }

    .play-content-container {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(260px, 320px);
        gap: 16px;
        flex: 1;
        min-height: 0;
        max-height: 100%;
        align-items: stretch;
    }

    .battle-arena-card {
        position: relative;
        isolation: isolate;
        width: 100%;
        height: 100%;
        min-height: 0;
        align-self: stretch;
        display: flex;
        flex-direction: column;
        max-height: 100%;
        overflow: hidden;
    }

    .battle-arena {
        position: relative;
        width: 100%;
        flex: 0 0 auto;
        height: clamp(220px, 78vh, 520px);
        min-height: 220px;
        max-height: 72vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .battle-arena-bg {
        position: absolute;
        inset: 0;
        /* Fill the arena edge-to-edge; may crop top/bottom or sides to preserve aspect ratio. */
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-color: #0f172a;
        z-index: 0;
       
    }

    .battle-arena-vignette {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(180deg, rgba(15,23,42,0.55) 0%, rgba(15,23,42,0.25) 35%, rgba(15,23,42,0.75) 100%);
        pointer-events: none;
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
        gap: 10px;
        padding: 6px 12px 44px;
    }

    .battle-fighter { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px; }

    .battle-vs {
        flex-shrink: 0;
        font-size: 1.75rem;
        color: #fbbf24;
        text-shadow: 0 0 24px rgba(251, 191, 36, 0.55);
    }

    /* Characters sit directly on the battle BG — no inner card/frame */
    .fighter-stand {
        position: relative;
        width: min(200px, 22vw);
    }

    .fighter-stand .fighter-portrait-img {
        width: 100%;
        max-height: min(200px, 24vh);
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

    /* Winner closes distance, then springs back (hero left / boss right). */
    @keyframes hero-charge-toward-boss {
        0% { transform: translateX(0); }
        38% { transform: translateX(calc(-1 * min(28vw, 220px))); }
        100% { transform: translateX(0); }
    }

    @keyframes boss-charge-toward-hero {
        0% { transform: translateX(0); }
        38% { transform: translateX(min(28vw, 220px)); }
        100% { transform: translateX(0); }
    }

    .fighter-stand--hero.battle-charge-win {
        animation: hero-charge-toward-boss 0.9s cubic-bezier(0.25, 0.9, 0.32, 1) forwards;
        z-index: 6;
    }

    .fighter-stand--boss.battle-charge-win {
        animation: boss-charge-toward-hero 0.9s cubic-bezier(0.25, 0.9, 0.32, 1) forwards;
        z-index: 6;
    }

    .combat-fx__beam--smash {
        background: linear-gradient(90deg,
            transparent,
            rgba(251, 191, 36, 0.5),
            rgba(251, 146, 60, 0.95),
            rgba(220, 38, 38, 0.9));
    }

    .combat-fx__beam--arcane {
        background: linear-gradient(90deg,
            transparent,
            rgba(56, 189, 248, 0.75),
            rgba(168, 85, 247, 0.95),
            rgba(236, 72, 153, 0.85));
    }

    /* Healer: mend + impact */
    .combat-fx__beam--heal-smash {
        background: linear-gradient(90deg,
            transparent,
            rgba(52, 211, 153, 0.92),
            rgba(250, 204, 21, 0.9),
            rgba(244, 63, 94, 0.45));
    }

    .combat-fx__beam--boss-smash {
        background: linear-gradient(90deg,
            rgba(220, 38, 38, 0.98),
            rgba(251, 146, 60, 0.92),
            rgba(15, 23, 42, 0.35),
            transparent);
    }

    .combat-fx__ring--ember {
        box-shadow: 0 0 36px 16px rgba(251, 146, 60, 0.7);
    }

    .combat-fx__ring--life {
        box-shadow: 0 0 36px 16px rgba(52, 211, 153, 0.72);
    }

    .combat-fx__ring--arcane-burst {
        box-shadow:
            0 0 40px 18px rgba(167, 139, 250, 0.75),
            0 0 22px 10px rgba(34, 211, 238, 0.65);
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
        /* flex-basis: 0 so this column can shrink below content height and .q-sheet-body scrolls */
        flex: 1 1 0;
        /* Floor + flex: min 0 via clamp so MC/ID content is never squeezed to padding-only height */
        min-height: clamp(10.5rem, 22vh, 26rem);
        width: 100%;
        display: flex;
        flex-direction: column;
        background: #f1f5f9;
        border-top: 3px solid #0f172a;
        overflow: hidden;
    }

    .battle-question-inner {
        display: flex;
        flex-direction: column;
        width: 100%;
        flex: 1 1 0;
        min-height: 0;
        overflow: hidden;
    }

    .q-sheet-head {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 6px 12px;
        background: linear-gradient(125deg, #0f172a 0%, #1e293b 55%, #334155 100%);
        border-bottom: 2px solid #fbbf24;
    }

    .q-sheet-kicker {
        margin: 0 0 2px;
        font-size: 0.58rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.55);
        line-height: 1.2;
    }

    .q-sheet-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .q-progress-pill {
        font-size: 0.68rem;
        font-weight: 800;
        color: #0f172a;
        background: #fbbf24;
        padding: 3px 10px;
        border-radius: 999px;
        line-height: 1.25;
    }

    .q-damage-pill {
        font-size: 0.65rem;
        font-weight: 800;
        color: #fecaca;
        background: rgba(127, 29, 29, 0.85);
        padding: 3px 10px;
        border-radius: 999px;
        border: 1px solid rgba(248, 113, 113, 0.35);
        line-height: 1.25;
    }

    .q-damage-pill i {
        margin-right: 4px;
        opacity: 0.9;
    }

    .q-sheet-type-chip {
        flex-shrink: 0;
        align-self: center;
        font-size: 0.6rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #0f172a;
        background: #e2e8f0;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        line-height: 1.2;
    }

    .q-sheet-body {
        padding: 10px 14px 12px;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        flex: 1 1 0;
        /* Never use min-height: % here — parent can be unresolved and the sheet collapses to padding-only. */
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-gutter: stable;
        overscroll-behavior: contain;
        /* Block stacking (not a flex container): nested flex was collapsing MC/ID content to ~0 height in some cases. */
    }

    .q-sheet-body .power-active-hint {
        margin-bottom: 14px;
    }

    .q-sheet-body .power-active-hint:not(.show) {
        display: none;
        margin-bottom: 0;
    }

    .question-text--sheet {
        /* Scroll lives on .q-sheet-body so long prompts + answers share one scrollbar. */
        max-height: none;
        overflow-x: hidden;
        overflow-y: visible;
        margin: 0 0 12px;
        padding-right: 6px;
    }

    .question-text--sheet h3 {
        font-size: 1.02rem;
        line-height: 1.38;
        margin: 0;
        max-width: none;
        color: #0f172a;
        font-weight: 800;
        overflow-wrap: break-word;
    }

    .answer-options-area--sheet .options-grid {
        margin-bottom: 12px;
        gap: 8px;
    }

    .answer-options-area--sheet .option-box {
        border-radius: 12px;
        padding: 10px 12px;
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
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
    }

    .answer-options-area--sheet .option-letter {
        width: 28px;
        height: 28px;
        font-size: 0.78rem;
    }

    .answer-options-area--sheet .option-text {
        font-size: 0.88rem;
    }

    .answer-options-area--sheet .options-grid.tf {
        gap: 10px;
    }

    .answer-options-area--sheet .tf .option-box {
        padding: 12px 14px;
        font-size: 0.92rem;
    }

    .answer-options-area--sheet .tf .option-box i {
        font-size: 1.5rem;
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
        padding: 12px 14px 14px;
        background: transparent;
    }

    .hud-hp-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .hud-hp-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        grid-template-rows: auto auto;
        gap: 8px 14px;
        align-items: baseline;
        padding: 0;
        background: none;
        border: none;
        border-radius: 0;
    }
    .hud-hp-title {
        grid-column: 1;
        grid-row: 1;
        margin: 0;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.95);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.85), 0 0 12px rgba(15, 23, 42, 0.6);
        line-height: 1.25;
    }
    .hud-hp-card:first-child .hud-hp-title {
        color: #fecaca;
    }
    .hud-hp-card:last-child .hud-hp-title {
        color: #bfdbfe;
    }
    .hud-hp-track {
        grid-column: 1 / -1;
        grid-row: 2;
        height: 9px;
        background: transparent;
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.45);
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.35);
    }
    .hud-hp-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }
    .hud-hp-fill.boss { background: linear-gradient(90deg, #dc2626, #f87171); }
    .hud-hp-fill.hero { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .hud-hp-value {
        grid-column: 2;
        grid-row: 1;
        margin: 0;
        text-align: right;
        font-size: 0.72rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        color: rgba(248, 250, 252, 0.92);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.85);
        white-space: nowrap;
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

    /* Form must keep its natural height so identification (textarea) is not flex-squashed to 0; .q-sheet-body scrolls instead */
    #quest-answer-form.answer-options-area--sheet {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    #quest-answer-form.answer-options-area--sheet .text-answer-input {
        flex: 0 0 auto;
    }

    #quest-answer-form.answer-options-area--sheet .text-answer-input textarea {
        width: 100%;
        box-sizing: border-box;
        min-height: 96px;
        max-height: min(44vh, 380px);
        margin-bottom: 0;
        padding: 12px 14px;
        font-size: 0.95rem;
        line-height: 1.45;
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        font-family: inherit;
        resize: none;
        overflow-y: auto;
    }

    #quest-answer-form.answer-options-area--sheet .text-answer-input textarea:focus {
        outline: none;
        border-color: var(--accent);
    }

    #quest-answer-form.answer-options-area--sheet .form-actions--sheet {
        flex-shrink: 0;
        margin-top: 0;
    }

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

    .play-sidebar .stats-card,
    .play-sidebar .powers-card,
    .play-sidebar .music-card {
        margin-bottom: 0;
        padding: 14px 16px;
        border-radius: 16px;
    }

    .play-sidebar .stats-card h4,
    .play-sidebar .powers-card h4,
    .play-sidebar .music-card h4 {
        font-size: 0.82rem;
        margin-bottom: 10px;
    }

    .play-sidebar .music-hint {
        margin-top: 8px;
        font-size: 0.65rem;
        line-height: 1.35;
    }

    .play-sidebar .power-btn {
        padding: 10px 12px;
        gap: 10px;
    }

    .play-sidebar .hero-stats {
        gap: 8px;
        margin-bottom: 10px;
    }

    .play-sidebar .character-badge {
        padding: 8px 12px;
        font-size: 0.78rem;
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
        position: relative;
    }

    .power-btn.power-btn--loading {
        pointer-events: none;
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .power-btn__loading {
        position: absolute;
        inset: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.94), rgba(239, 246, 255, 0.96));
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 0.02em;
    }

    .power-btn__spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(37, 99, 235, 0.22);
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: questPowerBtnSpin 0.65s linear infinite;
        flex-shrink: 0;
    }

    @keyframes questPowerBtnSpin {
        to { transform: rotate(360deg); }
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

    .timer-display--sidebar {
        margin-right: 0;
        width: 100%;
        justify-content: center;
        box-sizing: border-box;
        padding: 8px 12px;
        font-size: 0.95rem;
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

    .power-hint-casting {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: #92400e;
    }

    .power-hint-casting__dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f59e0b;
        animation: powerHintCastPulse 0.55s ease-in-out infinite alternate;
    }

    @keyframes powerHintCastPulse {
        from { opacity: 0.35; transform: scale(0.85); }
        to { opacity: 1; transform: scale(1.1); }
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

    .play-sidebar .mini-map-card {
        margin-bottom: 0;
        padding: 14px;
        border-radius: 16px;
    }

    .mini-map-card h4 { font-size: 0.9rem; font-weight: 800; color: var(--primary); margin-bottom: 15px; }

    .play-sidebar .mini-map-card h4 {
        margin-bottom: 8px;
        font-size: 0.82rem;
    }
    
    .mini-map-visual {
        position: relative;
        width: 100%;
        aspect-ratio: 16/10;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .play-sidebar .mini-map-visual {
        aspect-ratio: 5/3;
        max-height: 110px;
        margin-bottom: 8px;
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

    .map-hero-marker {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 24px;
        height: 24px;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.95);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.35);
        z-index: 3;
        transition: left 1.25s cubic-bezier(0.22, 1, 0.36, 1), top 1.25s cubic-bezier(0.22, 1, 0.36, 1), transform 0.25s ease;
    }

    .map-hero-marker.map-hero-marker--walking {
        animation: mini-map-walk 0.28s linear infinite alternate;
    }

    @keyframes mini-map-walk {
        from { transform: translate(-50%, -50%) rotate(-5deg) scale(1); }
        to   { transform: translate(-50%, -50%) rotate(5deg) scale(1.05); }
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

    .play-sidebar .hint-card {
        padding: 12px;
        border-radius: 14px;
        gap: 6px;
        flex-shrink: 0;
    }

    .play-sidebar .hint-card p { font-size: 0.78rem; }

    .hint-card i { color: #0ea5e9; font-size: 1.2rem; }
    .hint-card p { font-size: 0.85rem; color: #0369a1; font-weight: 600; line-height: 1.4; }

    /* ===== BATTLE RESULT (inside .battle-arena only) ===== */
    .battle-feedback-layer {
        position: absolute;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        box-sizing: border-box;
    }

    /* Level map: escape dashboard shell / arena column; toggled in JS (fixed CB may be shell if backdrop-filter on ancestor). */
    .battle-feedback-layer.battle-feedback-layer--viewport {
        position: fixed;
        inset: 0;
        z-index: 10060;
    }

    .battle-feedback-backdrop {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 120% 80% at 50% 40%, rgba(15, 23, 42, 0.45) 0%, rgba(15, 23, 42, 0.72) 100%);
        backdrop-filter: blur(4px);
    }

    .battle-feedback-sheet {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 400px;
        animation: battle-sheet-in 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    #battle-card.battle-feedback-sheet--map-phase {
        flex-shrink: 0;
        width: min(960px, min(94vw, 100%));
        max-width: min(960px, min(94vw, 100%));
        box-sizing: border-box;
    }

    #battle-card.battle-feedback-sheet--map-phase .battle-level-transition__map {
        height: clamp(300px, min(52vh, 560px), 620px);
        max-height: min(62vh, 620px);
    }

    #battle-card.battle-feedback-sheet--map-phase .battle-level-transition__map-img {
        object-fit: contain;
        background: radial-gradient(ellipse 80% 70% at 50% 45%, #1e293b 0%, #0f172a 100%);
    }

    @keyframes battle-sheet-in {
        from { transform: translateY(12px) scale(0.96); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }

    .fighter { display: flex; flex-direction: column; align-items: center; gap: 12px; }

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

    .battle-level-transition {
        position: relative;
        z-index: 3;
        border-radius: 0;
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 8px 4px 6px;
        text-align: center;
        width: 100%;
        box-sizing: border-box;
    }

    .battle-level-transition__eyebrow {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #fde68a;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.75);
    }

    .battle-level-transition__title {
        margin: 4px 0 14px;
        font-size: 1.12rem;
        font-weight: 800;
        color: #f8fafc;
        text-shadow: 0 2px 14px rgba(0, 0, 0, 0.65);
    }

    .battle-level-transition__map {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        width: 100%;
        height: clamp(280px, 46vh, 520px);
        min-height: 260px;
        background: transparent;
    }

    .battle-level-transition__map-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 1;
    }

    .battle-level-pin-active {
        position: absolute;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #facc15;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.8);
        animation: battleLevelPulse 1.5s infinite;
        z-index: 3;
    }

    .battle-level-hero {
        position: absolute;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #fff;
        background: #fff;
        transform: translate(-50%, -50%);
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.35);
        z-index: 4;
        transition: left 1.2s cubic-bezier(0.22, 1, 0.36, 1), top 1.2s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .battle-level-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .battle-level-hero.is-walking {
        animation: battleHeroWalk 0.26s linear infinite alternate;
    }

    .battle-level-transition__hint {
        margin: 12px 0 0;
        font-size: 0.82rem;
        color: rgba(248, 250, 252, 0.92);
        font-weight: 600;
        text-shadow: 0 1px 8px rgba(0, 0, 0, 0.65);
    }

    @keyframes battleHeroWalk {
        from { transform: translate(-50%, -50%) rotate(-5deg) scale(1); }
        to { transform: translate(-50%, -50%) rotate(5deg) scale(1.05); }
    }

    @keyframes battleLevelPulse {
        0% { box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.75); }
        70% { box-shadow: 0 0 0 9px rgba(250, 204, 21, 0); }
        100% { box-shadow: 0 0 0 0 rgba(250, 204, 21, 0); }
    }

    @media (max-width: 768px) {
        .play-content-container {
            grid-template-columns: 1fr;
            max-height: none;
        }
        .play-sidebar {
            max-height: min(52vh, 420px);
            order: -1;
        }
        .battle-arena {
            height: clamp(176px, 32vh, 320px);
            max-height: 38vh;
        }
        .battle-fighters-row {
            flex-direction: column;
            padding: 10px 12px 48px;
            gap: 14px;
        }
        .hud-hp-row {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .battle-vs { font-size: 1.35rem; }
        .fighter-stand { width: min(200px, 40vw); }
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

    .btn-battle-action--result:disabled {
        cursor: wait;
        opacity: 0.9;
        transform: none;
    }

    .btn-battle-action--result.btn-battle-action--loading {
        pointer-events: none;
    }

    .battle-feedback-sheet.is-loading-next {
        opacity: 0.92;
        transition: opacity 0.2s ease;
    }

    .btn-battle-action__icon {
        font-size: 0.85rem;
        opacity: 0.9;
    }
</style>
