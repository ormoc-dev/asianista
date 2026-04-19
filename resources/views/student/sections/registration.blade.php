@extends('student.dashboard')

@section('content')
<div class="reg-page">
    <header class="reg-hero">
        <div class="reg-hero-badge" aria-hidden="true">
            <i class="fas fa-id-card-alt"></i>
        </div>
        <div class="reg-hero-text">
            <h1 class="reg-title">Registration</h1>
            <p class="reg-lead">What your teacher set up for you on ASIANISTA—keep these details handy.</p>
        </div>
    </header>

    @if($registrationRecord)
        @php
            $approved = $registrationRecord->is_approved;
            $teacher = $registrationRecord->teacher;
            $teacherPic = $teacher?->profile_pic ?? 'default-pp.png';
        @endphp

        <article class="reg-card">
            <div class="reg-card__status reg-card__status--{{ $approved ? 'ok' : 'wait' }}">
                <div class="reg-card__status-inner">
                    <span class="reg-pill">
                        @if($approved)
                            <i class="fas fa-shield-check"></i> Approved
                        @else
                            <i class="fas fa-hourglass-half"></i> Pending approval
                        @endif
                    </span>
                    <p class="reg-card__status-hint">
                        @if($approved)
                            You are cleared to use the platform with this class enrollment.
                        @else
                            Your teacher still needs to approve your account. You can keep exploring meanwhile.
                        @endif
                    </p>
                </div>
            </div>

            <div class="reg-card__body">
                <div class="reg-identity">
                    <div class="reg-identity-name">{{ $registrationRecord->full_name }}</div>
                    @if($teacher)
                        <div class="reg-teacher">
                            <img src="{{ asset('images/' . $teacherPic) }}" alt="" class="reg-teacher-avatar" width="44" height="44">
                            <div>
                                <span class="reg-teacher-label">Teacher</span>
                                <span class="reg-teacher-name">{{ $teacher->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>

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
                </div>

                @if($registrationRecord->student_code || $registrationRecord->username)
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
                @endif

                <div class="reg-tip">
                    <i class="fas fa-lock"></i>
                    <span>Do not share your password. If you cannot sign in, ask your teacher to reset your access.</span>
                </div>
            </div>
        </article>
    @else
        <div class="reg-empty">
            <div class="reg-empty-icon"><i class="fas fa-folder-open"></i></div>
            <h2 class="reg-empty-title">No registration record</h2>
            <p class="reg-empty-text">This account is not linked to a teacher registration. If that surprises you, talk to your teacher.</p>
        </div>
    @endif
</div>

<style>
.reg-page {
    --reg-accent: #d97706;
    --reg-accent-soft: rgba(217, 119, 6, 0.12);
    --reg-ink: #0b1020;
    --reg-muted: #64748b;
    --reg-line: rgba(15, 23, 42, 0.08);
    max-width: 720px;
    margin: 0 auto;
    padding: 8px 8px 40px;
}

.reg-hero {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 28px;
}

.reg-hero-badge {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(145deg, #fff6e5, #fde68a);
    border: 1px solid rgba(217, 119, 6, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #b45309;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(217, 119, 6, 0.15);
}

.reg-title {
    margin: 0 0 8px;
    font-size: clamp(1.45rem, 2.5vw, 1.75rem);
    font-weight: 700;
    color: var(--reg-ink);
    letter-spacing: -0.02em;
}

.reg-lead {
    margin: 0;
    font-size: 0.98rem;
    line-height: 1.55;
    color: var(--reg-muted);
    max-width: 42ch;
}

.reg-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--reg-line);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.reg-card__status {
    padding: 20px 24px;
    border-bottom: 1px solid var(--reg-line);
}

.reg-card__status--ok {
    background: linear-gradient(105deg, #ecfdf5 0%, #f0fdf4 45%, #fff 100%);
}

.reg-card__status--wait {
    background: linear-gradient(105deg, #fffbeb 0%, #fff7ed 50%, #fff 100%);
}

.reg-card__status-inner {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.reg-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    align-self: flex-start;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    padding: 8px 14px;
    border-radius: 999px;
}

.reg-card__status--ok .reg-pill {
    background: rgba(16, 185, 129, 0.18);
    color: #047857;
}

.reg-card__status--wait .reg-pill {
    background: rgba(245, 158, 11, 0.22);
    color: #b45309;
}

.reg-card__status-hint {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
    color: #475569;
    max-width: 52ch;
}

.reg-card__body {
    padding: 24px 24px 28px;
}

.reg-identity {
    margin-bottom: 24px;
    padding-bottom: 22px;
    border-bottom: 1px solid var(--reg-line);
}

.reg-identity-name {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--reg-ink);
    letter-spacing: -0.02em;
    line-height: 1.25;
    margin-bottom: 16px;
}

.reg-teacher {
    display: flex;
    align-items: center;
    gap: 14px;
}

.reg-teacher-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px var(--reg-line);
}

.reg-teacher-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--reg-muted);
    margin-bottom: 2px;
}

.reg-teacher-name {
    font-size: 0.98rem;
    font-weight: 600;
    color: var(--reg-ink);
}

.reg-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--reg-muted);
    margin-bottom: 12px;
}

.reg-tiles {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}

.reg-tile {
    background: #f8fafc;
    border: 1px solid var(--reg-line);
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 88px;
}

.reg-tile-icon {
    color: var(--reg-accent);
    font-size: 0.95rem;
    opacity: 0.9;
}

.reg-tile-label {
    font-size: 0.75rem;
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
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 22px;
}

.reg-credential {
    background: linear-gradient(180deg, #fafafa 0%, #f4f4f5 100%);
    border: 1px dashed rgba(15, 23, 42, 0.12);
    border-radius: 14px;
    padding: 14px 18px;
}

.reg-credential-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--reg-muted);
    margin-bottom: 8px;
}

.reg-credential-value {
    display: block;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    word-break: break-all;
    background: #fff;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid var(--reg-line);
}

.reg-tip {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    font-size: 0.88rem;
    line-height: 1.55;
    color: #475569;
    background: var(--reg-accent-soft);
    border-radius: 14px;
    padding: 14px 16px;
    border: 1px solid rgba(217, 119, 6, 0.15);
}

.reg-tip i {
    color: var(--reg-accent);
    margin-top: 2px;
    flex-shrink: 0;
}

.reg-empty {
    text-align: center;
    padding: 56px 28px;
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--reg-line);
    box-shadow: 0 12px 36px rgba(15, 23, 42, 0.06);
}

.reg-empty-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 20px;
    border-radius: 20px;
    background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 1.75rem;
}

.reg-empty-title {
    margin: 0 0 10px;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--reg-ink);
}

.reg-empty-text {
    margin: 0 auto;
    max-width: 400px;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--reg-muted);
}

@media (max-width: 520px) {
    .reg-hero {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .reg-hero-text {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .reg-lead {
        max-width: none;
    }
    .reg-card__body {
        padding: 20px 18px 24px;
    }
}
</style>
@endsection
