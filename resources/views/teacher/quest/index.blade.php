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
    @if($quests->count() == 0)
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
    @else
    <div class="quest-grid">
        @foreach($quests as $quest)
        <div class="quest-card">
            <div class="quest-card-header">
                <div class="quest-type-pill">
                    <i class="fas fa-scroll"></i> {{ $quest->difficulty ?? 'Standard' }}
                </div>
                <div class="quest-actions">
                    <button class="action-btn" title="Edit Quest"><i class="fas fa-edit"></i></button>
                    <button class="action-btn delete" title="Delete Quest"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            
            <div class="quest-card-body">
                <h3>{{ $quest->title }}</h3>
                <p class="quest-desc">{{ Str::limit($quest->description, 100) }}</p>
                
                <div class="quest-meta-info">
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>{{ $quest->grade->name ?? 'N/A' }} - {{ $quest->section->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Due: {{ \Carbon\Carbon::parse($quest->due_date)->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="quest-rewards-row">
                    <div class="reward-pill xp">
                        <i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP
                    </div>
                    <div class="reward-pill ab">
                        <i class="fas fa-bolt"></i> {{ $quest->ab_reward ?? 0 }} AB
                    </div>
                    <div class="reward-pill gp">
                        <i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }} GP
                    </div>
                </div>
            </div>

            <div class="quest-card-footer">
                <div class="quest-level">Requirement: Level {{ $quest->level }}</div>
                <a href="{{ route('teacher.quest.show', $quest->id) }}" class="btn-details">View Details</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

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

    /* Quest Grid & Cards */
    .quest-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }

    .quest-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(0,35,102,0.1);
        box-shadow: 0 4px 15px rgba(0,35,102,0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .quest-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,35,102,0.1);
        border-color: var(--accent);
    }

    .quest-card-header {
        padding: 15px 20px;
        background: #fafbfc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(0,35,102,0.05);
    }

    .quest-type-pill {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--primary);
        background: rgba(0,35,102,0.05);
        padding: 4px 10px;
        border-radius: 999px;
    }

    .quest-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: none;
        background: #fff;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }

    .action-btn:hover {
        color: var(--primary);
        background: var(--accent);
    }

    .action-btn.delete:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    .quest-card-body {
        padding: 20px;
        flex-grow: 1;
    }

    .quest-card-body h3 {
        font-size: 1.15rem;
        color: var(--primary);
        margin-bottom: 10px;
        font-weight: 700;
    }

    .quest-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .quest-meta-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.8rem;
        color: var(--secondary);
        font-weight: 500;
    }

    .meta-item i {
        color: var(--accent-dark);
        width: 16px;
    }

    .quest-rewards-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .reward-pill {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .reward-pill.xp { background: #eef2ff; color: #4f46e5; }
    .reward-pill.ab { background: #fffbeb; color: #d97706; }
    .reward-pill.gp { background: #ecfdf5; color: #059669; }

    .quest-card-footer {
        padding: 15px 20px;
        background: #fafbfc;
        border-top: 1px solid rgba(0,35,102,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .quest-level {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .btn-details {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
    }

    .btn-details:hover {
        text-decoration: underline;
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
