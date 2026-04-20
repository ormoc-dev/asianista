@extends('student.dashboard')

@section('content')
@php
    $typeMeta = [
        'praise' => ['label' => 'Praise', 'icon' => 'fa-star', 'tone' => 'praise'],
        'improvement' => ['label' => 'Improvement', 'icon' => 'fa-arrow-up', 'tone' => 'improvement'],
        'concern' => ['label' => 'Concern', 'icon' => 'fa-exclamation', 'tone' => 'concern'],
    ];
@endphp

<section class="sf" aria-labelledby="sf-title">
    <header class="sf-head">
        <h1 id="sf-title" class="sf-title">Teacher feedback</h1>
        <p class="sf-sub">Praise, tips, and follow-ups from your teacher appear below.</p>
    </header>

    @if($feedbacks->isEmpty())
        <p class="sf-empty">
            <i class="fas fa-inbox" aria-hidden="true"></i>
            No messages yet. When your teacher sends feedback, it will show up here.
        </p>
    @else
        <div class="sf-feed" role="list">
            @foreach($feedbacks as $fb)
                @php
                    $meta = $typeMeta[$fb->type] ?? [
                        'label' => ucfirst($fb->type),
                        'icon' => 'fa-comment',
                        'tone' => 'default',
                    ];
                    $teacher = $fb->teacher;
                    $pic = $teacher?->profile_pic ?? 'default-pp.png';
                @endphp
                <article class="sf-item sf-item--{{ $meta['tone'] }}" role="listitem">
                    <img class="sf-avatar" src="{{ asset('images/' . $pic) }}" alt="" width="40" height="40">
                    <div class="sf-item-main">
                        <div class="sf-item-top">
                            <span class="sf-teacher">{{ $teacher->name ?? 'Teacher' }}</span>
                            <span class="sf-tag"><i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i> {{ $meta['label'] }}</span>
                        </div>
                        <time class="sf-time" datetime="{{ $fb->created_at->toIso8601String() }}">
                            {{ $fb->created_at->format('M j, Y · g:i A') }} <span class="sf-time-rel">({{ $fb->created_at->diffForHumans() }})</span>
                        </time>
                        <p class="sf-msg">{{ $fb->message }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

<style>
.sf {
    --sf-muted: #64748b;
    --sf-ink: #0b1020;
    --sf-line: rgba(15, 23, 42, 0.1);
    color: var(--sf-ink);
}

.sf-head {
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--sf-line);
}

.sf-title {
    margin: 0 0 0.35rem;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--sf-ink);
}

.sf-sub {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
    color: var(--sf-muted);
    max-width: 42rem;
}

.sf-empty {
    margin: 2rem 0 0;
    padding: 1.25rem 0;
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--sf-muted);
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.sf-empty i {
    margin-top: 0.15rem;
    color: #94a3b8;
    flex-shrink: 0;
}

.sf-feed {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.sf-item {
    display: flex;
    gap: 1rem;
    padding: 1.15rem 0 1.15rem 14px;
    border-bottom: 1px solid var(--sf-line);
    border-left: 3px solid transparent;
    align-items: flex-start;
}

.sf-item:last-child {
    border-bottom: none;
    padding-bottom: 0.25rem;
}

.sf-item--praise { border-left-color: #f59e0b; }
.sf-item--improvement { border-left-color: #3b82f6; }
.sf-item--concern { border-left-color: #ef4444; }
.sf-item--default { border-left-color: #94a3b8; }

.sf-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid var(--sf-line);
}

.sf-item-main {
    min-width: 0;
    flex: 1;
}

.sf-item-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 0.75rem;
    margin-bottom: 0.2rem;
}

.sf-teacher {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--sf-ink);
}

.sf-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    background: rgba(15, 23, 42, 0.06);
    color: #475569;
}

.sf-item--praise .sf-tag { background: rgba(245, 158, 11, 0.15); color: #b45309; }
.sf-item--improvement .sf-tag { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
.sf-item--concern .sf-tag { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }

.sf-time {
    display: block;
    font-size: 0.78rem;
    color: var(--sf-muted);
    margin-bottom: 0.65rem;
}

.sf-time-rel {
    font-weight: 500;
}

.sf-msg {
    margin: 0;
    font-size: 0.94rem;
    line-height: 1.65;
    color: #1e293b;
    white-space: pre-wrap;
    word-break: break-word;
}

@media (max-width: 520px) {
    .sf-item-top {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection
