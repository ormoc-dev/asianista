@extends('teacher.layouts.app')

@section('title', 'Random Events')
@section('page-title', 'Random Events')

@section('content')
@if(session('success'))
    <div class="alert alert-success teacher-flash-auto" data-teacher-flash role="status" style="margin-bottom: 20px; padding: 12px 16px; border-radius: var(--radius-sm); background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="event-draw-container">
    <div class="event-draw-layout">
        <div class="draw-layout__col draw-layout__col--left">
            <div class="draw-card draw-card--audience">
                <header class="draw-card__head">
                    <h2 class="draw-card__title">Draw a random event</h2>
                    <p class="draw-card__lede"><strong>Step 1.</strong> Choose who can see this draw. Then use the wheel on the right to pick from the pool.</p>
                </header>

                <div class="recipient-panel" role="group" aria-labelledby="recipient-panel-title">
                    <header class="recipient-panel__header">
                    <span class="recipient-panel__kicker">Audience</span>
                    <h3 id="recipient-panel-title" class="recipient-panel__title">Who should see this event?</h3>
                    <p class="recipient-panel__lead">Only students you registered will get the notification on their dashboard.</p>
                    </header>

                    <div class="recipient-panel__options">
                        <div class="recipient-option-wrap">
                        <label class="recipient-choice">
                            <input type="radio" name="recipient_mode" value="all" class="recipient-choice__input" checked>
                            <span class="recipient-choice__card">
                                <span class="recipient-choice__icon recipient-choice__icon--all" aria-hidden="true">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="recipient-choice__body">
                                    <span class="recipient-choice__label">All your registered students</span>
                                    <span class="recipient-choice__hint">Approved students you added through your registration list</span>
                                </span>
                                <span class="recipient-choice__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                            </span>
                        </label>
                    </div>

                    <div class="recipient-option-wrap">
                        <label class="recipient-choice">
                            <input type="radio" name="recipient_mode" value="random" class="recipient-choice__input">
                            <span class="recipient-choice__card">
                                <span class="recipient-choice__icon recipient-choice__icon--random" aria-hidden="true">
                                    <i class="fas fa-random"></i>
                                </span>
                                <span class="recipient-choice__body">
                                    <span class="recipient-choice__label">Random subset</span>
                                    <span class="recipient-choice__hint">Pick a number; the system chooses students at random</span>
                                </span>
                                <span class="recipient-choice__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                            </span>
                        </label>
                        <div id="randomRecipientFields" class="recipient-detail" hidden>
                            <div class="recipient-detail__inner">
                                <label class="recipient-field" for="randomCount">
                                    <span class="recipient-field__label">Number of students</span>
                                    <span class="recipient-field__control">
                                        <input type="number" id="randomCount" class="recipient-field__input" min="1" max="500" value="1" inputmode="numeric">
                                    </span>
                                </label>
                                <p class="recipient-detail__note">If you ask for more than your pool size, every approved student you registered may be included.</p>
                            </div>
                        </div>
                    </div>

                    <div class="recipient-option-wrap">
                        <label class="recipient-choice">
                            <input type="radio" name="recipient_mode" value="selected" class="recipient-choice__input">
                            <span class="recipient-choice__card">
                                <span class="recipient-choice__icon recipient-choice__icon--selected" aria-hidden="true">
                                    <i class="fas fa-user-check"></i>
                                </span>
                                <span class="recipient-choice__body">
                                    <span class="recipient-choice__label">Specific students</span>
                                    <span class="recipient-choice__hint">Choose exactly who receives this event</span>
                                </span>
                                <span class="recipient-choice__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                            </span>
                        </label>
                        <div id="selectedRecipientFields" class="recipient-detail" hidden>
                            <div class="recipient-detail__inner">
                                <label class="recipient-field recipient-field--block" for="studentSelect">
                                    <span class="recipient-field__label">Students</span>
                                    <select id="studentSelect" class="recipient-multiselect" multiple size="7" title="Hold Ctrl or Cmd to select multiple">
                                        @foreach($students as $student)
                                            @php
                                                $label = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                                                if ($label === '') {
                                                    $label = $student->name;
                                                }
                                            @endphp
                                            <option value="{{ $student->id }}">{{ $label }}@if(!empty($student->email)) — {{ $student->email }}@endif</option>
                                        @endforeach
                                    </select>
                                </label>
                                <p class="recipient-hint"><kbd>Ctrl</kbd> / <kbd>Cmd</kbd> + click for multiple selection.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="draw-layout__col draw-layout__col--right">
            <div class="draw-card draw-card--wheel">
                <header class="draw-card__head draw-card__head--wheel">
                    <span class="draw-card__kicker">Step 2</span>
                    <h3 class="draw-card__title draw-card__title--wheel">Spin the wheel</h3>
                    <p class="draw-card__lede draw-card__lede--wheel">The pointer picks a slot while the server chooses one active event from the pool.</p>
                </header>

                <div class="wheel-stage" aria-hidden="true">
                    <div class="wheel-pointer"></div>
                    <div class="wheel-wrap">
                        <div class="wheel-fireworks" id="wheelFireworks" aria-hidden="true"></div>
                        <div class="wheel" id="drawWheel" role="presentation"></div>
                        <div class="wheel-hub">
                            <i class="fas fa-star wheel-hub__icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="draw-card__cta draw-card__cta--wheel">
                    <button type="button" class="btn btn-success btn-lg draw-btn" id="drawBtn">
                        <i class="fas fa-sync-alt"></i> Spin &amp; draw
                    </button>
                    <p class="draw-card__hint">Events are defined by admin. Students see the popup for a few minutes after you draw.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Result Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-scroll"></i> Event Drawn!</h3>
            <button onclick="closeEventModal()" class="btn-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="eventModalBody">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<!-- Draw History Section -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-history"></i> Draw History</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Target</th>
                        <th>Recipients</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drawHistory as $draw)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $draw->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $draw->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $draw->event_title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($draw->event_description, 50) }}</div>
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'positive' => 'success',
                                    'negative' => 'danger',
                                    'neutral' => 'info',
                                    'challenge' => 'warning'
                                ];
                                $typeLabels = [
                                    'positive' => 'Positive',
                                    'negative' => 'Negative',
                                    'neutral' => 'Neutral',
                                    'challenge' => 'Challenge'
                                ];
                            @endphp
                            <span class="badge badge-{{ $typeColors[$draw->event_type] ?? 'info' }}">
                                {{ $typeLabels[$draw->event_type] ?? $draw->event_type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $targetLabels = [
                                    'single' => 'Single',
                                    'all' => 'All Players',
                                    'pair' => 'Pair',
                                    'random' => 'Random'
                                ];
                            @endphp
                            <span class="badge badge-purple">{{ $targetLabels[$draw->target_type] ?? $draw->target_type }}</span>
                        </td>
                        <td>
                            @php
                                $rMode = $draw->recipient_mode ?? 'all';
                                $rIds = $draw->recipient_student_ids ?? [];
                                $rCount = is_array($rIds) ? count($rIds) : 0;
                            @endphp
                            @if($rMode === 'all')
                                <span class="badge badge-info">All students</span>
                            @elseif($rMode === 'random')
                                <span class="badge badge-warning">Random ({{ $rCount }})</span>
                            @else
                                <span class="badge badge-secondary">Selected ({{ $rCount }})</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <div style="color: var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                No events drawn yet. Click the dice to draw your first event!
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($drawHistory->hasMorePages())
        <div style="padding: 16px; border-top: 1px solid var(--border);">
            {{ $drawHistory->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.event-draw-container {
    width: 100%;
    margin-bottom: 24px;
}

.event-draw-layout {
    display: grid;
    grid-template-columns: minmax(300px, 1fr) minmax(260px, 380px);
    gap: 24px;
    width: 100%;
    align-items: start;
}

.draw-layout__col {
    min-width: 0;
}

.draw-layout__col--right {
    position: sticky;
    top: 88px;
}

.draw-card {
    text-align: left;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 16px;
    padding: 22px 22px 20px;
    box-shadow:
        0 1px 2px rgba(15, 23, 42, 0.04),
        0 12px 32px rgba(15, 23, 42, 0.07);
}

.draw-card--wheel {
    display: flex;
    flex-direction: column;
    align-items: stretch;
}

.draw-card__head {
    margin-bottom: 18px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
}

.draw-card__head--wheel {
    text-align: center;
    border-bottom: none;
    padding-bottom: 8px;
    margin-bottom: 8px;
}

.draw-card__kicker {
    display: inline-block;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #059669;
    margin-bottom: 6px;
}

.draw-card__title {
    color: #0f172a;
    font-size: 1.35rem;
    font-weight: 700;
    margin: 0 0 8px;
    line-height: 1.25;
    letter-spacing: -0.02em;
}

.draw-card__title--wheel {
    font-size: 1.2rem;
    margin-bottom: 6px;
}

.draw-card__lede {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.55;
    max-width: 40rem;
}

.draw-card__lede--wheel {
    font-size: 0.82rem;
    max-width: none;
    margin: 0 auto;
    max-width: 18rem;
}

.draw-card__cta--wheel {
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 10px;
}

.draw-card__hint {
    font-size: 0.78rem;
    color: #64748b;
    margin: 0;
    line-height: 1.45;
    max-width: 19rem;
}

/* Spin wheel */
.wheel-stage {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 0 20px;
}

.wheel-pointer {
    position: relative;
    z-index: 4;
    width: 0;
    height: 0;
    margin-bottom: -2px;
    border-left: 16px solid transparent;
    border-right: 16px solid transparent;
    border-bottom: 26px solid #0f172a;
    filter: drop-shadow(0 3px 6px rgba(15, 23, 42, 0.25));
}

.wheel-wrap {
    position: relative;
    width: min(220px, 72vw);
    height: min(220px, 72vw);
    flex-shrink: 0;
    border-radius: 50%;
    overflow: visible;
}

.wheel-wrap--pulse {
    animation: wheel-celebrate-pulse 0.75s ease-out;
}

@keyframes wheel-celebrate-pulse {
    0% { filter: brightness(1); box-shadow: none; }
    35% {
        filter: brightness(1.08);
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.45), 0 0 32px rgba(251, 191, 36, 0.5);
    }
    100% { filter: brightness(1); box-shadow: none; }
}

.wheel-fireworks {
    position: absolute;
    inset: -14%;
    z-index: 3;
    pointer-events: none;
    overflow: visible;
}

.wheel-fw-particle {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 7px;
    height: 7px;
    margin: -3.5px 0 0 -3.5px;
    border-radius: 50%;
    opacity: 0;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.9), 0 0 4px currentColor;
    animation: wheel-fw-burst 0.95s cubic-bezier(0.15, 0.85, 0.2, 1) forwards;
}

@keyframes wheel-fw-burst {
    0% {
        opacity: 1;
        transform: translate(0, 0) scale(1);
    }
    25% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translate(var(--tx), var(--ty)) scale(0.15);
    }
}

.wheel-fw-spark {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 2px;
    height: 28px;
    margin: -14px 0 0 -1px;
    border-radius: 1px;
    opacity: 0;
    transform-origin: 50% 100%;
    animation: wheel-fw-spark 0.8s ease-out forwards;
    background: linear-gradient(180deg, #fff 0%, transparent 100%);
}

@keyframes wheel-fw-spark {
    0% {
        opacity: 1;
        transform: rotate(var(--spark-rot)) translateY(-18px) scaleY(0.3);
    }
    40% {
        opacity: 0.95;
        transform: rotate(var(--spark-rot)) translateY(-52px) scaleY(1.2);
    }
    100% {
        opacity: 0;
        transform: rotate(var(--spark-rot)) translateY(-95px) scaleY(0.4);
    }
}

@media (prefers-reduced-motion: reduce) {
    .wheel {
        transition-duration: 0.01ms !important;
    }

    .wheel-fw-particle,
    .wheel-fw-spark,
    .wheel-wrap--pulse {
        animation: none !important;
    }
}

.wheel {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: conic-gradient(
        #6366f1 0deg 45deg,
        #8b5cf6 45deg 90deg,
        #a855f7 90deg 135deg,
        #d946ef 135deg 180deg,
        #ec4899 180deg 225deg,
        #f43f5e 225deg 270deg,
        #f97316 270deg 315deg,
        #22c55e 315deg 360deg
    );
    border: 8px solid #fff;
    box-shadow:
        0 4px 6px rgba(15, 23, 42, 0.06),
        0 16px 40px rgba(15, 23, 42, 0.12),
        inset 0 0 0 1px rgba(15, 23, 42, 0.06);
    transform: rotate(0deg);
    transform-origin: 50% 50%;
    transition: transform 2.6s cubic-bezier(0.2, 0.82, 0.15, 1);
    z-index: 1;
}

.wheel-hub {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: linear-gradient(165deg, #ffffff 0%, #f1f5f9 100%);
    box-shadow:
        0 4px 14px rgba(15, 23, 42, 0.12),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    z-index: 4;
    border: 1px solid rgba(15, 23, 42, 0.06);
}

.wheel-hub__icon {
    color: #d97706;
    font-size: 1.15rem;
    filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.12));
}

@media (max-width: 900px) {
    .event-draw-layout {
        grid-template-columns: 1fr;
    }

    .draw-layout__col--right {
        position: static;
    }

    .draw-card__head--wheel {
        text-align: left;
    }

    .draw-card__lede--wheel {
        margin: 0;
        max-width: none;
    }

    .draw-card__cta--wheel {
        align-items: flex-start;
        text-align: left;
    }
}

.recipient-panel {
    text-align: left;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 18px 16px;
    margin: 0;
    max-width: 100%;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.recipient-panel__header {
    margin-bottom: 18px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
}

.recipient-panel__kicker {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6366f1;
    margin-bottom: 6px;
}

.recipient-panel__title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 6px;
    line-height: 1.3;
}

.recipient-panel__lead {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

.recipient-panel__options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.recipient-option-wrap {
    border-radius: 12px;
    transition: background 0.15s ease;
}

.recipient-option-wrap--on {
    background: rgba(99, 102, 241, 0.04);
}

.recipient-choice {
    display: block;
    margin: 0;
    cursor: pointer;
}

.recipient-choice__input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.recipient-choice__card {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    transition:
        border-color 0.15s ease,
        background 0.15s ease,
        box-shadow 0.15s ease;
}

.recipient-choice:hover .recipient-choice__card {
    border-color: #cbd5e1;
    background: #fff;
}

.recipient-choice__input:focus-visible + .recipient-choice__card {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

.recipient-option-wrap--on .recipient-choice__card {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.25);
}

.recipient-choice__icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #fff;
}

.recipient-choice__icon--all {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.recipient-choice__icon--random {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.recipient-choice__icon--selected {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.recipient-choice__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding-top: 1px;
}

.recipient-choice__label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.25;
}

.recipient-choice__hint {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
}

.recipient-choice__check {
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    color: transparent;
    margin-top: 9px;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
}

.recipient-option-wrap--on .recipient-choice__check {
    background: #6366f1;
    border-color: #6366f1;
    color: #fff;
}

.recipient-detail {
    margin: 8px 0 4px 12px;
    padding: 0 4px 0 8px;
    border-left: 3px solid #e0e7ff;
}

.recipient-detail__inner {
    padding: 12px 12px 14px;
    background: #f8fafc;
    border-radius: 0 10px 10px 10px;
    border: 1px solid #e2e8f0;
}

.recipient-detail__note {
    font-size: 0.75rem;
    color: #64748b;
    margin: 10px 0 0;
    line-height: 1.45;
}

.recipient-field {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.recipient-field--block {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
}

.recipient-field__label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #334155;
}

.recipient-field__control {
    display: flex;
    align-items: center;
}

.recipient-field__input {
    width: 88px;
    padding: 8px 12px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #fff;
}

.recipient-field__input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.recipient-multiselect {
    width: 100%;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    padding: 8px;
    font-size: 0.875rem;
    line-height: 1.4;
    color: #0f172a;
    background: #fff;
    min-height: 160px;
}

.recipient-multiselect:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.recipient-hint {
    font-size: 0.75rem;
    color: #64748b;
    margin: 10px 0 0;
    line-height: 1.5;
}

.recipient-hint kbd {
    display: inline-block;
    padding: 2px 6px;
    font-size: 0.68rem;
    font-family: ui-monospace, monospace;
    background: #e2e8f0;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.draw-btn {
    padding: 14px 40px;
    font-size: 1.1rem;
    border-radius: 30px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
    transition: all 0.3s ease;
}

.draw-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.draw-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Modal Styles */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.modal.show {
    display: flex;
}

.modal-content {
    background: transparent;
    max-width: 500px;
    width: 90%;
    animation: modalPop 0.3s ease-out;
}

@keyframes modalPop {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.modal-header {
    display: none;
}

.modal-body {
    padding: 0;
}

.btn-close {
    background: rgba(255,255,255,0.2);
    border: none;
    font-size: 1rem;
    cursor: pointer;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Scroll Paper Styles */
.event-card-drawn {
    position: relative;
    max-width: 450px;
    margin: 0 auto;
}

.scroll-draw-top,
.scroll-draw-bottom {
    height: 40px;
    background: linear-gradient(180deg, #d4a574 0%, #c49a6c 50%, #b08d5f 100%);
    border-radius: 20px;
    position: relative;
    box-shadow: 
        inset 0 2px 4px rgba(255,255,255,0.3),
        inset 0 -2px 4px rgba(0,0,0,0.2),
        0 4px 8px rgba(0,0,0,0.3);
}

.scroll-draw-top::before,
.scroll-draw-top::after,
.scroll-draw-bottom::before,
.scroll-draw-bottom::after {
    content: '';
    position: absolute;
    width: 30px;
    height: 50px;
    background: linear-gradient(90deg, #8b6914 0%, #a67c2e 50%, #8b6914 100%);
    border-radius: 0 0 15px 15px;
    top: 0;
    box-shadow: inset 0 -3px 6px rgba(0,0,0,0.3);
}

.scroll-draw-top::before,
.scroll-draw-bottom::before {
    left: -10px;
}

.scroll-draw-top::after,
.scroll-draw-bottom::after {
    right: -10px;
}

.scroll-draw-bottom::before,
.scroll-draw-bottom::after {
    border-radius: 15px 15px 0 0;
    top: auto;
    bottom: 0;
}

.scroll-draw-paper {
    background: linear-gradient(180deg, #f5e6d3 0%, #f0dcc0 50%, #ebd5b3 100%);
    padding: 40px 45px;
    margin: -5px 10px;
    position: relative;
    box-shadow: 
        inset 0 0 60px rgba(139, 105, 20, 0.1),
        0 4px 20px rgba(0,0,0,0.15);
}

.scroll-draw-paper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        repeating-linear-gradient(
            0deg,
            transparent,
            transparent 28px,
            rgba(139, 105, 20, 0.03) 28px,
            rgba(139, 105, 20, 0.03) 29px
        );
    pointer-events: none;
}

.scroll-draw-title {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 1.8rem;
    color: #4a3728;
    text-align: center;
    margin-bottom: 15px;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
}

.scroll-draw-description {
    font-style: italic;
    color: #6b5344;
    text-align: center;
    margin-bottom: 25px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.scroll-draw-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent, #c4a77d, transparent);
    margin: 25px 0;
}

.scroll-draw-effect {
    text-align: center;
    color: #4a3728;
    font-size: 1.1rem;
    line-height: 1.6;
    font-weight: 500;
}

.scroll-draw-xp {
    text-align: center;
    margin-top: 25px;
    font-size: 1.4rem;
    font-weight: 700;
}

.scroll-draw-xp .xp-reward {
    color: #16a34a;
}

.scroll-draw-xp .xp-penalty {
    color: #dc2626;
}

.scroll-draw-footer {
    text-align: center;
    margin-top: 20px;
}

.scroll-draw-footer .btn {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #fff;
    padding: 12px 40px;
    border-radius: 25px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
}

.scroll-draw-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.close-btn-container {
    position: absolute;
    top: -50px;
    right: 0;
}
</style>

<script>
var drawWheelRotation = 0;
var drawWheelSpinMs = 2600;
var drawSpinTimerId = null;
var pendingDrawEvent = null;
var spinAnimationDone = false;

if (typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    drawWheelSpinMs = 120;
}

function spinDrawWheel() {
    var el = document.getElementById('drawWheel');
    if (!el) return;
    var spins = 4 + Math.random() * 4;
    var jitter = Math.random() * 360;
    drawWheelRotation += spins * 360 + jitter;
    el.style.transform = 'rotate(' + drawWheelRotation + 'deg)';
}

function clearDrawSpinTimer() {
    if (drawSpinTimerId !== null) {
        clearTimeout(drawSpinTimerId);
        drawSpinTimerId = null;
    }
}

function resetDrawFlowState() {
    clearDrawSpinTimer();
    pendingDrawEvent = null;
    spinAnimationDone = false;
}

function launchWheelFireworks() {
    if (typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }
    var host = document.getElementById('wheelFireworks');
    if (!host) return;
    host.innerHTML = '';
    var colors = ['#fbbf24', '#f472b6', '#60a5fa', '#34d399', '#a78bfa', '#fb7185', '#facc15', '#ffffff'];

    function addBurst(scale, delayOffset) {
        var n = 40;
        for (var i = 0; i < n; i++) {
            var p = document.createElement('span');
            p.className = 'wheel-fw-particle';
            var a = (Math.PI * 2 * i) / n + Math.random() * 0.35;
            var d = (72 + Math.random() * 88) * scale;
            p.style.setProperty('--tx', (Math.cos(a) * d).toFixed(1) + 'px');
            p.style.setProperty('--ty', (Math.sin(a) * d).toFixed(1) + 'px');
            p.style.animationDelay = (delayOffset + Math.random() * 0.1) + 's';
            p.style.background = colors[Math.floor(Math.random() * colors.length)];
            host.appendChild(p);
        }
        var sparks = 14;
        for (var s = 0; s < sparks; s++) {
            var sp = document.createElement('span');
            sp.className = 'wheel-fw-spark';
            var rot = (360 / sparks) * s + Math.random() * 12;
            sp.style.setProperty('--spark-rot', rot + 'deg');
            sp.style.animationDelay = (delayOffset + Math.random() * 0.08) + 's';
            host.appendChild(sp);
        }
    }

    addBurst(1, 0);
    setTimeout(function () { addBurst(0.62, 0.05); }, 120);
    setTimeout(function () { addBurst(0.38, 0.08); }, 260);

    var wrap = document.querySelector('.wheel-wrap');
    if (wrap) {
        wrap.classList.remove('wheel-wrap--pulse');
        void wrap.offsetWidth;
        wrap.classList.add('wheel-wrap--pulse');
    }

    setTimeout(function () {
        host.innerHTML = '';
    }, 1500);
}

function tryShowDrawModal(drawBtn) {
    if (!pendingDrawEvent || !spinAnimationDone) return;
    var ev = pendingDrawEvent;
    pendingDrawEvent = null;
    spinAnimationDone = false;
    showEventModal(ev);
    if (drawBtn) drawBtn.disabled = false;
}

function syncRecipientPanels() {
    const mode = document.querySelector('input[name="recipient_mode"]:checked')?.value || 'all';
    const randomFields = document.getElementById('randomRecipientFields');
    const selectedFields = document.getElementById('selectedRecipientFields');
    if (randomFields) randomFields.hidden = mode !== 'random';
    if (selectedFields) selectedFields.hidden = mode !== 'selected';

    document.querySelectorAll('.recipient-option-wrap').forEach(function (wrap) {
        var input = wrap.querySelector('.recipient-choice__input');
        wrap.classList.toggle('recipient-option-wrap--on', !!(input && input.checked));
    });
}

function drawRandomEvent() {
    var drawBtn = document.getElementById('drawBtn');
    var mode = document.querySelector('input[name="recipient_mode"]:checked')?.value || 'all';

    var payload = { recipient_mode: mode };

    if (mode === 'random') {
        var n = parseInt(document.getElementById('randomCount')?.value, 10);
        payload.random_count = Number.isFinite(n) && n > 0 ? n : 1;
    }

    if (mode === 'selected') {
        var sel = document.getElementById('studentSelect');
        payload.student_ids = sel ? Array.from(sel.selectedOptions).map(function (o) { return parseInt(o.value, 10); }) : [];
        if (payload.student_ids.length === 0) {
            alert('Select at least one student, or choose another recipient option.');
            return;
        }
    }

    drawBtn.disabled = true;
    resetDrawFlowState();
    spinDrawWheel();

    drawSpinTimerId = setTimeout(function () {
        drawSpinTimerId = null;
        spinAnimationDone = true;
        launchWheelFireworks();
        tryShowDrawModal(drawBtn);
    }, drawWheelSpinMs);

    fetch('{{ route("teacher.random-events.draw") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(function (response) {
        return response.json().then(function (data) {
            if (!response.ok) {
                const msg = (data && data.message) ? data.message : (data && data.error ? data.error : 'Request failed');
                throw new Error(msg);
            }
            return data;
        });
    })
    .then(function (event) {
        pendingDrawEvent = event;
        tryShowDrawModal(drawBtn);
    })
    .catch(function (error) {
        console.error('Error:', error);
        resetDrawFlowState();
        drawBtn.disabled = false;
        alert(error.message || 'Failed to draw random event. Please try again.');
    });
}

function showEventModal(event) {
    const modalBody = document.getElementById('eventModalBody');

    modalBody.innerHTML = `
        <div class="event-card-drawn">
            <div class="close-btn-container">
                <button onclick="closeEventModal()" class="btn-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="scroll-draw-top"></div>
            <div class="scroll-draw-paper">
                <h2 class="scroll-draw-title">${event.title}</h2>
                <div class="scroll-draw-description">${event.description}</div>
                <div class="scroll-draw-divider"></div>
                <div class="scroll-draw-effect">${event.effect}</div>
                <div class="scroll-draw-footer">
                    <button onclick="closeEventModal()" class="btn">
                        <i class="fas fa-check"></i> Done
                    </button>
                </div>
            </div>
            <div class="scroll-draw-bottom"></div>
        </div>
    `;
    
    const modal = document.getElementById('eventModal');
    modal.classList.add('show');
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('show');
    
    // Reload page to show new history
    window.location.reload();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const drawBtn = document.getElementById('drawBtn');
    const eventModal = document.getElementById('eventModal');

    document.querySelectorAll('input[name="recipient_mode"]').forEach(function (radio) {
        radio.addEventListener('change', syncRecipientPanels);
    });
    syncRecipientPanels();
    
    if (drawBtn) {
        drawBtn.addEventListener('click', function(e) {
            e.preventDefault();
            drawRandomEvent();
        });
    }
    
    if (eventModal) {
        eventModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEventModal();
            }
        });
    }
});
</script>
@endsection
