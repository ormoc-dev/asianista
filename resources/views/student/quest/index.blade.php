@extends('student.dashboard')

@section('content')
<div class="quest-page">

    <!-- Page Header -->
    <div class="quest-header">
        <div class="quest-header-text">
            <h2>🗺️ Quest Board</h2>
            <p>View your missions, track your progress, and earn more XP.</p>
        </div>

        <div class="quest-header-stats">
            <div class="quest-pill">
                <span class="label">Total Quests</span>
                <span class="value">0</span>
            </div>
            <div class="quest-pill">
                <span class="label">Completed</span>
                <span class="value">0</span>
            </div>
        </div>
    </div>

    <!-- No Quest Yet Placeholder -->
    <div class="quest-empty card">
        <div class="quest-empty-icon">
            <i class="fas fa-scroll"></i>
        </div>

        <h3>No Available Quests… Yet!</h3>
        <p>
            Your teacher hasn’t assigned any quests at the moment.
            Check back soon to start your next adventure and earn more XP.
        </p>

        <div class="quest-tips">
            <span class="tip-pill">
                <i class="fas fa-bolt"></i> Check in daily to keep your streak.
            </span>
            <span class="tip-pill">
                <i class="fas fa-star"></i> Completing quests boosts your rank.
            </span>
        </div>

        {{-- Example button (hidden for now, ready when you have real quests) --}}
        <a href="{{ route('student.quest') }}" class="btn-primary quest-btn" style="visibility: hidden;">
            View Available Quests
        </a>
    </div>

</div>

<style>
    .quest-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Header */
    .quest-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
    }

    .quest-header-text h2 {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--primary);
    }

    .quest-header-text p {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .quest-header-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .quest-pill {
        background: rgba(0,35,102,0.06);
        border-radius: 999px;
        padding: 6px 14px;
        display: flex;
        flex-direction: column;
        min-width: 110px;
    }

    .quest-pill .label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
    }

    .quest-pill .value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--primary);
    }

    /* Empty state card */
    .quest-empty {
        text-align: center;
        padding: 32px 24px 26px;
    }

    .quest-empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: radial-gradient(circle, #fff7c2, #ffd43b);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 18px rgba(255, 212, 59, 0.7);
        position: relative;
        z-index: 1;
    }

    .quest-empty-icon i {
        font-size: 2.1rem;
        color: #b45309;
    }

    .quest-empty h3 {
        font-size: 1.35rem;
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .quest-empty p {
        color: var(--text-muted);
        font-size: 0.95rem;
        max-width: 520px;
        margin: 0 auto 18px;
        position: relative;
        z-index: 1;
    }

    .quest-tips {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .tip-pill {
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0,35,102,0.06);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .tip-pill i {
        color: var(--accent-dark);
        font-size: 0.85rem;
    }

    .quest-btn {
        margin-top: 8px;
    }

    @media (max-width: 768px) {
        .quest-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection
