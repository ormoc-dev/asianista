<div class="quests-index-container" style="padding: 20px;">
    <style>
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: var(--primary);
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-forge {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
        }
        .btn-forge:hover {
            transform: translateY(-2px);
        }
        .search-bar {
            width: 100%;
            max-width: 400px;
            padding: 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        .quest-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .quest-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border: 1px solid #e2e8f0;
        }
        .quest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .quest-banner {
            height: 120px;
            background: linear-gradient(45deg, #1e3a8a, #312e81);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        .quest-content {
            padding: 20px;
        }
        .quest-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .meta-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .reward-info {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }
        .reward-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .xp-text { color: #4f46e5; }
        .gp-text { color: #d97706; }
        .btn-view {
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #f1f5f9;
            color: #1e3a8a;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 15px;
            transition: background 0.2s;
        }
        .btn-view:hover {
            background: #e2e8f0;
        }
    </style>

    <div class="header-section">
        <div>
            <h1>🗺️ Quest Board</h1>
            <p style="color: #64748b;">Manage adventure quests and assign them to your students.</p>
        </div>
        <a href="{{ route('teacher.quest.create') }}" class="btn-forge">
            <i class="fas fa-hammer"></i> Forge New Quest
        </a>
    </div>

    <input type="text" wire:model.debounce.300ms="search" placeholder="Search quests..." class="search-bar">

    <div class="quest-grid">
        @forelse($quests as $quest)
            <div class="quest-card">
                <div class="quest-banner">
                    <i class="fas fa-scroll"></i>
                </div>
                <div class="quest-content">
                    <div class="quest-title">{{ $quest->title }}</div>
                    <div class="meta-section">
                        <span class="meta-tag"><i class="fas fa-graduation-cap"></i> {{ $quest->grade->name ?? 'N/A' }}</span>
                        <span class="meta-tag"><i class="fas fa-users"></i> {{ $quest->section->name ?? 'N/A' }}</span>
                        <span class="meta-tag"><i class="fas fa-signal"></i> {{ ucfirst($quest->difficulty ?? 'NORMAL') }}</span>
                    </div>
                    
                    <div class="reward-info">
                        <div class="reward-item xp-text">
                            <i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP
                        </div>
                        <div class="reward-item gp-text">
                            <i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }} GP
                        </div>
                    </div>

                    <p style="margin-top: 15px; font-size: 0.9rem; color: #64748b; line-height: 1.4; height: 3.8em; overflow: hidden;">
                        {{ $quest->description }}
                    </p>

                    <a href="{{ route('teacher.quest.show', $quest->id) }}" class="btn-view">View Details</a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 20px;">
                <p style="color: #64748b; font-size: 1.2rem;">No quests found. Click "Forge New Quest" to start!</p>
            </div>
        @endforelse
    </div>
</div>
