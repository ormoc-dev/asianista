@extends('student.dashboard')

@section('content')
@php
    $typeMeta = [
        'praise' => ['label' => 'Praise', 'icon' => 'fa-star', 'class' => 'fb-type-praise', 'accent' => 'praise'],
        'improvement' => ['label' => 'Improvement', 'icon' => 'fa-arrow-up', 'class' => 'fb-type-improvement', 'accent' => 'improvement'],
        'concern' => ['label' => 'Concern', 'icon' => 'fa-exclamation', 'class' => 'fb-type-concern', 'accent' => 'concern'],
    ];
@endphp

<div class="fb-page">
    <header class="fb-hero">
        <div class="fb-hero-badge" aria-hidden="true">
            <i class="fas fa-comment-dots"></i>
        </div>
        <div class="fb-hero-text">
            <h1 class="fb-title">Feedback from your teacher</h1>
            <p class="fb-lead">Notes sent from <strong>Student Feedback</strong> show up here—praise, tips, and things to work on.</p>
        </div>
    </header>

    @if($feedbacks->isEmpty())
        <div class="fb-empty">
            <div class="fb-empty-visual">
                <span class="fb-empty-ring"></span>
                <i class="fas fa-inbox"></i>
            </div>
            <h2 class="fb-empty-title">No feedback yet</h2>
            <p class="fb-empty-text">When your teacher sends a message, it will appear in a card on this page so you can read it anytime.</p>
        </div>
    @else
        <ul class="fb-list">
            @foreach($feedbacks as $fb)
                @php
                    $meta = $typeMeta[$fb->type] ?? [
                        'label' => ucfirst($fb->type),
                        'icon' => 'fa-comment',
                        'class' => 'fb-type-default',
                        'accent' => 'default',
                    ];
                    $teacher = $fb->teacher;
                    $pic = $teacher?->profile_pic ?? 'default-pp.png';
                @endphp
                <li class="fb-card fb-card--{{ $meta['accent'] }}">
                    <div class="fb-card__head">
                        <div class="fb-card__teacher">
                            <img src="{{ asset('images/' . $pic) }}" alt="" class="fb-card__avatar" width="48" height="48">
                            <div class="fb-card__teacher-meta">
                                <span class="fb-card__name">{{ $teacher->name ?? 'Teacher' }}</span>
                                <span class="fb-card__date">
                                    <i class="far fa-clock"></i>
                                    {{ $fb->created_at->format('M j, Y \a\t g:i A') }} · {{ $fb->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <span class="fb-pill {{ $meta['class'] }}">
                            <i class="fas {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                        </span>
                    </div>
                    <div class="fb-card__body">
                        <p class="fb-card__message">{{ $fb->message }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<style>
.fb-page {
    --fb-ink: #0b1020;
    --fb-muted: #64748b;
    --fb-line: rgba(15, 23, 42, 0.08);
    --fb-gold: #d97706;
    max-width: 800px;
    margin: 0 auto;
    padding: 8px 8px 40px;
}

.fb-hero {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 28px;
}

.fb-hero-badge {
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

.fb-title {
    margin: 0 0 8px;
    font-size: clamp(1.45rem, 2.5vw, 1.75rem);
    font-weight: 700;
    color: var(--fb-ink);
    letter-spacing: -0.02em;
}

.fb-lead {
    margin: 0;
    font-size: 0.98rem;
    line-height: 1.55;
    color: var(--fb-muted);
    max-width: 48ch;
}

.fb-lead strong {
    color: #475569;
    font-weight: 600;
}

/* Empty state */
.fb-empty {
    text-align: center;
    padding: 52px 28px 56px;
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--fb-line);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
}

.fb-empty-visual {
    position: relative;
    width: 88px;
    height: 88px;
    margin: 0 auto 22px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fb-empty-ring {
    position: absolute;
    inset: 0;
    border-radius: 24px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border: 1px solid var(--fb-line);
}

.fb-empty-visual > i {
    position: relative;
    font-size: 2rem;
    color: #94a3b8;
}

.fb-empty-title {
    margin: 0 0 10px;
    font-size: 1.28rem;
    font-weight: 700;
    color: var(--fb-ink);
}

.fb-empty-text {
    margin: 0 auto;
    max-width: 420px;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--fb-muted);
}

/* List & cards */
.fb-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.fb-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid var(--fb-line);
    box-shadow: 0 10px 36px rgba(15, 23, 42, 0.07);
    overflow: hidden;
    position: relative;
}

.fb-card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 18px 0 0 18px;
}

.fb-card--praise::before {
    background: linear-gradient(180deg, #fbbf24, #f59e0b);
}

.fb-card--improvement::before {
    background: linear-gradient(180deg, #60a5fa, #3b82f6);
}

.fb-card--concern::before {
    background: linear-gradient(180deg, #f87171, #ef4444);
}

.fb-card--default::before {
    background: linear-gradient(180deg, #94a3b8, #64748b);
}

.fb-card__head {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    padding: 20px 22px 16px;
    padding-left: 22px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
}

.fb-card__teacher {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.fb-card__avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px var(--fb-line);
    flex-shrink: 0;
}

.fb-card__teacher-meta {
    min-width: 0;
}

.fb-card__name {
    display: block;
    font-weight: 700;
    font-size: 1.02rem;
    color: var(--fb-ink);
    line-height: 1.25;
}

.fb-card__date {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: var(--fb-muted);
    margin-top: 4px;
    line-height: 1.45;
}

.fb-card__date > i {
    font-size: 0.72rem;
    opacity: 0.85;
    flex-shrink: 0;
}

.fb-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    flex-shrink: 0;
}

.fb-pill.fb-type-praise {
    background: rgba(234, 179, 8, 0.2);
    color: #a16207;
}

.fb-pill.fb-type-improvement {
    background: rgba(59, 130, 246, 0.18);
    color: #1d4ed8;
}

.fb-pill.fb-type-concern {
    background: rgba(239, 68, 68, 0.14);
    color: #b91c1c;
}

.fb-pill.fb-type-default {
    background: rgba(100, 116, 139, 0.14);
    color: #475569;
}

.fb-card__body {
    padding: 18px 22px 22px;
}

.fb-card__message {
    margin: 0;
    font-size: 0.96rem;
    line-height: 1.7;
    color: #1e293b;
    white-space: pre-wrap;
    word-break: break-word;
}

@media (max-width: 520px) {
    .fb-hero {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .fb-hero-text {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .fb-lead {
        max-width: none;
    }
    .fb-card__head {
        flex-direction: column;
        align-items: stretch;
    }
    .fb-pill {
        align-self: flex-start;
    }
}
</style>
@endsection
