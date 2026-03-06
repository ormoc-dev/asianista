@extends('teacher.dashboard')

@section('content')
<div class="quest-board">

    <!-- Page Header -->
    <div class="quest-board-header">
        <div>
            <div class="quest-pill">
                <i class="fas fa-scroll"></i>
                Quest Board
            </div>
            <h2>
                <i class="fas fa-dragon"></i>
                Manage Quests
            </h2>
            <p>Forge new adventures, update existing quests, and guide your class through epic learning journeys.</p>
        </div>

        <a href="{{ route('teacher.quest.create') }}" class="btn-quest-primary">
            <i class="fas fa-plus-circle"></i>
            Create New Quest
        </a>
    </div>

    <!-- Empty State – if no quests yet -->
    <div class="quest-empty-card">
        <div class="quest-empty-icon">
            <i class="fas fa-treasure-chest"></i>
        </div>
        <h3>No Quests Yet</h3>
        <p>
            Your quest board is still empty. Start by creating your first quest and let your students earn XP, AB, and GP!
        </p>

        <div class="quest-empty-tags">
            <span><i class="fas fa-star"></i> XP Rewards</span>
            <span><i class="fas fa-swords"></i> Difficulty Levels</span>
            <span><i class="fas fa-users"></i> Grade & Section Targeting</span>
        </div>

        <button class="btn-ghost-primary" onclick="window.location.href='{{ route('teacher.quest.create') }}'">
            Begin Your First Quest
        </button>
    </div>

</div>

<style>
    .quest-board {
        display: flex;
        flex-direction: column;
        gap: 24px;
        /* dashboard-shell already gives a nice background; this sits inside it */
    }

    .quest-board-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 10px;
    }

    .quest-board-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quest-board-header p {
        margin-top: 6px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .quest-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0,35,102,0.08);
        color: var(--primary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .quest-pill i {
        color: var(--accent-dark);
    }

    .btn-quest-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 10px 20px;
        border-radius: 999px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(0,0,0,0.25);
        border: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }

    .btn-quest-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.32);
    }

    /* Empty quest state card */
    .quest-empty-card {
        margin-top: 10px;
        padding: 32px 26px;
        border-radius: 18px;
        border: 1px dashed rgba(148,163,184,0.7);
        background: radial-gradient(circle at top, rgba(191,197,219,0.6), rgba(241,241,224,0.9));
        box-shadow: 0 10px 25px rgba(15,23,42,0.25);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .quest-empty-card::after {
        content: "";
        position: absolute;
        inset: -40%;
        background: radial-gradient(circle at 10% 0%, rgba(255,212,59,0.2), transparent 60%),
                    radial-gradient(circle at 90% 100%, rgba(0,35,102,0.18), transparent 60%);
        opacity: 0.9;
        z-index: 0;
    }

    .quest-empty-card > * {
        position: relative;
        z-index: 1;
    }

    .quest-empty-icon {
        width: 78px;
        height: 78px;
        margin: 0 auto 14px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 20%, #fff, #e5ecff);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 18px rgba(255,212,59,0.65);
        color: var(--primary);
        font-size: 2rem;
    }

    .quest-empty-card h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 6px;
    }

    .quest-empty-card p {
        font-size: 0.95rem;
        color: var(--text-muted);
        max-width: 520px;
        margin: 0 auto 18px;
    }

    .quest-empty-tags {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 18px;
    }

    .quest-empty-tags span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        background: rgba(15,23,42,0.75);
        color: #e5edff;
        box-shadow: 0 4px 12px rgba(15,23,42,0.45);
    }

    .quest-empty-tags i {
        color: var(--accent);
    }

    .btn-ghost-primary {
        border-radius: 999px;
        border: 1px solid rgba(15,23,42,0.3);
        background: rgba(15,23,42,0.05);
        padding: 10px 22px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--primary);
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .btn-ghost-primary:hover {
        background: rgba(15,23,42,0.12);
        border-color: rgba(15,23,42,0.45);
    }

    @media (max-width: 768px) {
        .quest-board-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-quest-primary {
            align-self: stretch;
            justify-content: center;
        }
    }
</style>
@endsection
