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
                <span class="value">{{ $quests->count() }}</span>
            </div>
            <div class="quest-pill">
                <span class="label">Missions Available</span>
                <span class="value">{{ $quests->where('status', '!=', 'completed')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Quests List or Empty State -->
    @if($quests->count() == 0)
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
    </div>
    @else
    <div class="quest-list-grid">
        @foreach($quests as $quest)
        <div class="quest-item-card">
            <div class="quest-item-banner">
                <span class="diff-badge {{ strtolower($quest->difficulty ?? 'medium') }}">
                    {{ $quest->difficulty ?? 'Medium' }}
                </span>
            </div>
            <div class="quest-item-content">
                <h3>{{ $quest->title }}</h3>
                <p>{{ Str::limit($quest->description, 80) }}</p>
                
                <div class="quest-item-meta">
                    <div class="meta-row">
                        <i class="fas fa-medal"></i>
                        <span>Requirement: Level {{ $quest->level }}</span>
                    </div>
                    <div class="meta-row">
                        <i class="fas fa-calendar-day"></i> 
                        <span>Assign: {{ \Carbon\Carbon::parse($quest->assign_date)->format('M d') }}</span>
                    </div>
                    <div class="meta-row">
                        <i class="fas fa-hourglass-half"></i> 
                        <span>Due: {{ \Carbon\Carbon::parse($quest->due_date)->format('M d') }}</span>
                    </div>
                </div>

                <div class="quest-item-rewards">
                    <div class="rew-pill xp">+{{ $quest->xp_reward ?? 0 }} XP</div>
                    <div class="rew-pill gp">+{{ $quest->gp_reward ?? 0 }} GP</div>
                </div>

                @php
                    $attempt = $quest->attempts->first();
                    $isCompleted = $attempt && $attempt->status === 'completed';
                    $isExpired = !$isCompleted && $quest->due_date && \Carbon\Carbon::parse($quest->due_date)->isPast();
                @endphp

                <a href="{{ route('student.quest.show', $quest->id) }}" class="btn-start-quest {{ $isExpired ? 'expired' : ($isCompleted ? 'completed' : '') }}">
                    @if($isCompleted)
                        Quest Conquered <i class="fas fa-check-circle"></i>
                    @elseif($isExpired)
                        Mission Overdue <i class="fas fa-lock"></i>
                    @else
                        Start Adventure <i class="fas fa-chevron-right"></i>
                    @endif
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
    .btn-start-quest.expired {
        background: #94a3b8;
        cursor: not-allowed;
        opacity: 0.8;
    }
    .btn-start-quest.expired:hover {
        transform: none;
        background: #94a3b8;
    }
    .btn-start-quest.completed:hover {
        background: #059669;
    }

    .lvl-badge {
        background: rgba(255, 212, 59, 0.2);
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid rgba(255, 212, 59, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>

<style>
    /* Header */
    .quest-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 30px;
    }

    .quest-header-text h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--primary);
    }

    .quest-header-text p {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    .quest-header-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .quest-pill {
        background: white;
        border-radius: 12px;
        padding: 8px 16px;
        display: flex;
        flex-direction: column;
        min-width: 140px;
        border: 1px solid rgba(0,35,102,0.08);
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }

    .quest-pill .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 800;
    }

    .quest-pill .value {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--primary);
    }

    /* Quest Grid */
    .quest-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }

    .quest-item-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(0,35,102,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 15px rgba(0,35,102,0.05);
    }

    .quest-item-card:hover {
        transform: translateY(-8px);
        border-color: var(--accent);
        box-shadow: 0 15px 30px rgba(0,35,102,0.1);
    }

    .quest-item-banner {
        height: 10px;
        background: linear-gradient(90deg, var(--accent), var(--accent-dark));
        position: relative;
    }

    .diff-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: white;
        background: #94a3b8;
    }

    .diff-badge.easy { background: #10b981; }
    .diff-badge.medium { background: #f59e0b; }
    .diff-badge.hard { background: #ef4444; }
    .diff-badge.epic { background: #8b5cf6; }

    .quest-item-content {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .quest-item-content h3 {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .quest-item-content p {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 20px;
        flex: 1;
    }

    .quest-item-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .meta-row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--secondary);
    }

    .meta-row i {
        color: var(--accent-dark);
        width: 16px;
    }

    .quest-item-rewards {
        display: flex;
        gap: 10px;
        margin-bottom: 24px;
    }

    .rew-pill {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .rew-pill.xp { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
    .rew-pill.gp { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

    .btn-start-quest {
        background: var(--primary);
        color: white;
        text-align: center;
        padding: 12px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s;
    }

    .btn-start-quest:hover {
        background: var(--accent-dark);
        color: var(--primary);
        transform: scale(1.02);
    }

    @media (max-width: 768px) {
        .quest-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection
