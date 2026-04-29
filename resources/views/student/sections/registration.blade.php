@extends('student.dashboard')

@section('content')
@php
    $student = auth()->user();
    $userStatus = $student->status;
    $isRejected = $userStatus === 'rejected';
    $isCleared = in_array($userStatus, ['approved', 'active'], true);
@endphp

<div class="reg-page">
    <div class="reg-layout">
        <header class="reg-hero">
            <div class="reg-hero-visual" aria-hidden="true">
                <div class="reg-hero-orbit"></div>
                <i class="fas fa-id-card-alt"></i>
            </div>
            <div class="reg-hero-copy">
                <p class="reg-eyebrow">Your enrollment</p>
                <h1 class="reg-title">Registration</h1>
                <p class="reg-lead">What your teacher set up for you on ASIANISTA—codes, class, and approval status in one place.</p>
            </div>
        </header>

        @if($registrationRecord)
            @php
                $approved = !$isRejected && $isCleared;
                $teacher = $registrationRecord->teacher;
                $teacherPic = $teacher?->profile_pic ?? 'default-pp.png';
                $statusTone = $isRejected ? 'denied' : ($approved ? 'ok' : 'wait');
            @endphp

            <article class="reg-card">
                <div class="reg-card__status reg-card__status--{{ $statusTone }}">
                    <div class="reg-card__status-bar">
                        <span class="reg-pill">
                            @if($isRejected)
                                <i class="fas fa-times-circle"></i> Not approved
                            @elseif($approved)
                                <i class="fas fa-shield-check"></i> Approved
                            @else
                                <i class="fas fa-hourglass-half"></i> Pending approval
                            @endif
                        </span>
                        <p class="reg-card__status-hint">
                            @if($isRejected)
                                Your teacher declined this registration. Contact them if you think this is a mistake.
                            @elseif($approved)
                                You are cleared to use the platform with this class enrollment.
                            @else
                                Your teacher still needs to approve your account. You can keep exploring meanwhile.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="reg-card__body">
                    <div class="reg-card__main-grid">
                        <div class="reg-col reg-col--profile">
                            <div class="reg-section-label">Student</div>
                            <div class="reg-identity-name">{{ $registrationRecord->full_name }}</div>
                            @if($teacher)
                                <div class="reg-teacher reg-teacher--card">
                                    <img src="{{ asset('images/' . $teacherPic) }}" alt="" class="reg-teacher-avatar" width="56" height="56" loading="lazy">
                                    <div>
                                        <span class="reg-teacher-label">Teacher</span>
                                        <span class="reg-teacher-name">{{ $teacher->name }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="reg-col reg-col--details">
                            <div class="reg-panel">
                                <div class="reg-section-label">Class</div>
                                <div class="reg-tiles">
                                    @if($registrationRecord->grade)
                                        <div class="reg-tile">
                                            <span class="reg-tile-icon"><i class="fas fa-layer-group"></i></span>
                                            <span class="reg-tile-label">Grade</span>
                                            <span class="reg-tile-value">{{ $registrationRecord->grade->name }}</span>
                                        </div>
                                    @endif
                                    @if($registrationRecord->section)
                                        <div class="reg-tile">
                                            <span class="reg-tile-icon"><i class="fas fa-users"></i></span>
                                            <span class="reg-tile-label">Section</span>
                                            <span class="reg-tile-value">{{ $registrationRecord->section->name }}</span>
                                        </div>
                                    @endif
                                    @if(!$registrationRecord->grade && !$registrationRecord->section)
                                        <p class="reg-muted-inline">Grade and section will appear here once linked.</p>
                                    @endif
                                </div>
                            </div>

                            @if($registrationRecord->student_code || $registrationRecord->username)
                                <div class="reg-panel reg-panel--credentials">
                                    <div class="reg-section-label">Sign-in identifiers</div>
                                    <div class="reg-credentials">
                                        @if($registrationRecord->student_code)
                                            <div class="reg-credential">
                                                <span class="reg-credential-label">Student code</span>
                                                <code class="reg-credential-value">{{ $registrationRecord->student_code }}</code>
                                            </div>
                                        @endif
                                        @if($registrationRecord->username)
                                            <div class="reg-credential">
                                                <span class="reg-credential-label">Username</span>
                                                <code class="reg-credential-value">{{ $registrationRecord->username }}</code>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="reg-col reg-col--full">
                            <div class="reg-tip">
                                <i class="fas fa-lock"></i>
                                <span>Do not share your password. If you cannot sign in, ask your teacher to reset your access.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @else
            <div class="reg-card reg-card--empty">
                <div class="reg-empty-inner">
                    <div class="reg-empty-icon"><i class="fas fa-folder-open"></i></div>
                    <h2 class="reg-empty-title">No registration record</h2>
                    <p class="reg-empty-text">This account is not linked to a teacher registration. If that surprises you, talk to your teacher.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.reg-page {
    --reg-ink: var(--text-dark, #0b1020);
    --reg-muted: var(--text-muted, #64748b);
    --reg-line: rgba(15, 23, 42, 0.1);
    --reg-card-bg: var(--card-bg, #f1f1e0);
    --reg-accent: var(--accent, #ffd43b);
    --reg-accent-dark: var(--accent-dark, #f5c400);
    --reg-primary: rgba(48, 6, 117, 0.69);
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 4px 0 40px;
    box-sizing: border-box;
}

.reg-layout {
    display: flex;
    flex-direction: column;
    gap: 24px;
    width: 100%;
}

.reg-hero {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 22px 32px;
    align-items: center;
    padding: 4px 0 8px;
    width: 100%;
}

.reg-hero-visual {
    position: relative;
    width: 72px;
    height: 72px;
    border-radius: 22px;
    background: linear-gradient(145deg, rgba(255, 212, 59, 0.35), rgba(48, 6, 117, 0.2));
    border: 1px solid rgba(255, 212, 59, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--reg-ink);
    font-size: 1.5rem;
    box-shadow:
        0 12px 32px rgba(48, 6, 117, 0.12),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
    flex-shrink: 0;
}

.reg-hero-orbit {
    position: absolute;
    inset: -6px;
    border-radius: 26px;
    border: 1px dashed rgba(48, 6, 117, 0.2);
    pointer-events: none;
    animation: reg-orbit-pulse 4s ease-in-out infinite;
}

@keyframes reg-orbit-pulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.02); }
}

.reg-hero-copy {
    min-width: 0;
}

.reg-eyebrow {
    margin: 0 0 6px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--reg-muted);
}

.reg-title {
    margin: 0 0 10px;
    font-size: clamp(1.5rem, 3vw, 1.85rem);
    font-weight: 700;
    color: var(--reg-ink);
    letter-spacing: -0.03em;
    line-height: 1.15;
}

.reg-lead {
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--reg-muted);
    max-width: min(72ch, 100%);
}

.reg-card {
    background: var(--reg-card-bg);
    border-radius: 24px;
    border: 1px solid var(--reg-line);
    box-shadow:
        0 4px 6px rgba(15, 23, 42, 0.04),
        0 20px 50px rgba(48, 6, 117, 0.08);
    overflow: hidden;
}

.reg-card--empty {
    padding: 0;
}

.reg-card__status {
    padding: 20px clamp(18px, 3vw, 36px);
    border-bottom: 1px solid var(--reg-line);
}

.reg-card__status-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 14px 28px;
    justify-content: flex-start;
}

@media (min-width: 900px) {
    .reg-card__status-bar {
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .reg-card__status-bar .reg-card__status-hint {
        text-align: right;
        flex: 1;
        min-width: 220px;
    }
}

.reg-card__status--ok {
    background: linear-gradient(115deg, rgba(16, 185, 129, 0.14) 0%, rgba(240, 253, 244, 0.9) 42%, var(--reg-card-bg) 100%);
}

.reg-card__status--wait {
    background: linear-gradient(115deg, rgba(245, 158, 11, 0.12) 0%, rgba(255, 251, 235, 0.95) 45%, var(--reg-card-bg) 100%);
}

.reg-card__status--denied {
    background: linear-gradient(115deg, rgba(239, 68, 68, 0.12) 0%, rgba(254, 242, 242, 0.95) 45%, var(--reg-card-bg) 100%);
}

.reg-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 8px 14px;
    border-radius: 999px;
}

.reg-card__status--ok .reg-pill {
    background: rgba(16, 185, 129, 0.22);
    color: #047857;
}

.reg-card__status--wait .reg-pill {
    background: rgba(245, 158, 11, 0.22);
    color: #b45309;
}

.reg-card__status--denied .reg-pill {
    background: rgba(239, 68, 68, 0.2);
    color: #b91c1c;
}

.reg-card__status-hint {
    margin: 0;
    font-size: 0.91rem;
    line-height: 1.55;
    color: #475569;
    flex: 1;
}

.reg-card__body {
    padding: clamp(20px, 2.5vw, 32px) clamp(18px, 3vw, 40px) clamp(24px, 3vw, 36px);
}

.reg-card__main-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 28px;
}

@media (min-width: 880px) {
    .reg-card__main-grid {
        grid-template-columns: minmax(260px, 340px) minmax(0, 1fr);
        gap: 32px 40px;
        align-items: start;
    }

    .reg-col--profile {
        padding-right: 28px;
        border-right: 1px solid var(--reg-line);
    }

    .reg-col--full {
        grid-column: 1 / -1;
    }
}

@media (min-width: 1200px) {
    .reg-card__main-grid {
        grid-template-columns: minmax(280px, 380px) minmax(0, 1fr);
        gap: 36px 48px;
    }
}

.reg-col--details {
    display: flex;
    flex-direction: column;
    gap: 24px;
    min-width: 0;
}

.reg-panel {
    margin: 0;
}

.reg-panel--credentials {
    margin-top: 0;
}

.reg-teacher--card {
    margin-top: 8px;
    padding: 16px 18px;
    background: rgba(255, 255, 255, 0.55);
    border: 1px solid var(--reg-line);
    border-radius: 16px;
}

.reg-col--profile .reg-section-label {
    margin-bottom: 8px;
}

.reg-identity-name {
    font-size: clamp(1.2rem, 2.2vw, 1.45rem);
    font-weight: 700;
    color: var(--reg-ink);
    letter-spacing: -0.02em;
    line-height: 1.25;
    margin-bottom: 0;
}

.reg-teacher {
    display: flex;
    align-items: center;
    gap: 14px;
}

.reg-teacher-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px var(--reg-line);
}

.reg-teacher-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--reg-muted);
    margin-bottom: 2px;
}

.reg-teacher-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--reg-ink);
}

.reg-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: var(--reg-muted);
    margin-bottom: 12px;
}

.reg-tiles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
}

@media (min-width: 880px) {
    .reg-tiles {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.reg-muted-inline {
    margin: 0;
    font-size: 0.9rem;
    color: var(--reg-muted);
}

.reg-tile {
    background: rgba(255, 255, 255, 0.55);
    border: 1px solid var(--reg-line);
    border-radius: 16px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 92px;
}

.reg-tile-icon {
    color: var(--reg-primary);
    font-size: 1rem;
    opacity: 0.9;
}

.reg-tile-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--reg-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.reg-tile-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--reg-ink);
}

.reg-credentials {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
}

@media (min-width: 640px) {
    .reg-credentials:has(.reg-credential:nth-child(2)) {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.reg-credential {
    background: rgba(255, 255, 255, 0.65);
    border: 1px dashed rgba(48, 6, 117, 0.18);
    border-radius: 16px;
    padding: 16px 18px;
}

.reg-credential-label {
    display: block;
    font-size: 0.74rem;
    font-weight: 600;
    color: var(--reg-muted);
    margin-bottom: 8px;
}

.reg-credential-value {
    display: block;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.94rem;
    font-weight: 600;
    color: #1e293b;
    word-break: break-all;
    background: #fff;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid var(--reg-line);
}

.reg-tip {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    font-size: 0.88rem;
    line-height: 1.55;
    color: #475569;
    background: linear-gradient(135deg, rgba(255, 212, 59, 0.15), rgba(48, 6, 117, 0.06));
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid rgba(255, 212, 59, 0.28);
}

.reg-tip i {
    color: #b45309;
    margin-top: 2px;
    flex-shrink: 0;
}

.reg-empty-inner {
    text-align: center;
    padding: 56px 28px 60px;
}

.reg-empty-icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 22px;
    border-radius: 22px;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(191, 197, 219, 0.35));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--reg-muted);
    font-size: 1.85rem;
    border: 1px solid var(--reg-line);
}

.reg-empty-title {
    margin: 0 0 10px;
    font-size: 1.28rem;
    font-weight: 700;
    color: var(--reg-ink);
}

.reg-empty-text {
    margin: 0 auto;
    max-width: 420px;
    font-size: 0.95rem;
    line-height: 1.65;
    color: var(--reg-muted);
}

@media (max-width: 879px) {
    .reg-col--profile {
        padding-bottom: 24px;
        border-bottom: 1px solid var(--reg-line);
    }
}

@media (max-width: 600px) {
    .reg-hero {
        grid-template-columns: 1fr;
        text-align: center;
        justify-items: center;
    }

    .reg-hero-copy {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .reg-lead {
        max-width: none;
    }

    .reg-card__body {
        padding: 18px 16px 22px;
    }

    .reg-card__status {
        padding: 18px 16px;
    }

    .reg-card__status-bar .reg-card__status-hint {
        text-align: left;
        flex: none;
    }
}
</style>
@endsection
