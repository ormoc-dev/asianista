<div class="quest-details-container" style="padding: 20px;">
    <style>
        .quest-details-header {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .quest-title-area h1 {
            font-size: 2.2rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 12px;
        }
        .tag-pill {
            padding: 6px 14px;
            background: #f1f5f9;
            border-radius: 999px;
            font-size: 0.8rem;
            color: #475569;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 8px;
        }
        
        .map-section {
            width: 100%;
            background: #1e293b;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 1000 / 600;
            border: 4px solid #4a3728;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }
        .map-bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
        }
        .map-node {
            position: absolute;
            transform: translate(-50%, -50%);
            cursor: pointer;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 20;
        }
        .map-node:hover { transform: translate(-50%, -50%) scale(1.15); z-index: 100; }
        .node-marker {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            font-size: 1.2rem;
            border: 3px solid #4f46e5;
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
        }
        .node-label {
            margin-top: 8px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-top: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .reward-pill {
            padding: 12px;
            border-radius: 12px;
            font-weight: 800;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .reward-xp { background: #eef2ff; color: #4f46e5; }
        .reward-ab { background: #fffbeb; color: #d97706; }
        .reward-gp { background: #ecfdf5; color: #059669; }

        /* Modal Styles */
        .modal-blur {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-box {
            background: #1e293b;
            width: 100%;
            max-width: 600px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.1);
            overflow: hidden;
            color: white;
        }
        .question-card {
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #4f46e5;
        }
    </style>

    <div class="quest-details-header">
        <a href="{{ route('teacher.quest') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Board
        </a>
        <div class="quest-title-area">
            <h1>{{ $quest->title }}</h1>
            <div class="quest-tags">
                <span class="tag-pill"><i class="fas fa-scroll"></i> {{ ucfirst($quest->difficulty ?? 'NORMAL') }}</span>
                <span class="tag-pill"><i class="fas fa-medal"></i> Level {{ $quest->level }}</span>
                <span class="tag-pill"><i class="fas fa-users"></i> {{ $quest->grade->name ?? 'N/A' }} - {{ $quest->section->name ?? 'N/A' }}</span>
                <span class="tag-pill"><i class="fas fa-calendar-alt"></i> Due: {{ \Carbon\Carbon::parse($quest->due_date)->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <img src="{{ asset('images/quest_map_bg.png') }}" alt="RPG Quest Map" class="map-bg" onerror="this.src='https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=1000&auto=format&fit=crop'">
        
        @php
            $positions = [
                ['left' => 50, 'top' => 86], ['left' => 25, 'top' => 55], ['left' => 15, 'top' => 66],
                ['left' => 40, 'top' => 40], ['left' => 55, 'top' => 60], ['left' => 75, 'top' => 45],
                ['left' => 75, 'top' => 80], ['left' => 85, 'top' => 65], ['left' => 80, 'top' => 20],
            ];
        @endphp

        @for($lvl = 1; $lvl <= $quest->level; $lvl++)
            @php $pos = $positions[($lvl - 1) % count($positions)]; @endphp
            <div class="map-node" style="left: {{ $pos['left'] }}%; top: {{ $pos['top'] }}%;" wire:click="showLevelDetails({{ $lvl }})">
                <div class="node-marker">
                    <i class="fas {{ $lvl == $quest->level ? 'fa-flag-checkered' : 'fa-fort-awesome' }}"></i>
                </div>
                <div class="node-label">Level {{ $lvl }}</div>
            </div>
        @endfor
    </div>

    <div class="summary-grid">
        <div class="card">
            <h3 style="margin-bottom: 20px; color: var(--primary); font-weight: 700;">💰 Grand Rewards</h3>
            <div class="reward-pill reward-xp"><i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP</div>
            <div class="reward-pill reward-ab"><i class="fas fa-bolt"></i> {{ $quest->ab_reward ?? 0 }} AB</div>
            <div class="reward-pill reward-gp"><i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }} GP</div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px; color: var(--primary); font-weight: 700;">🧩 Challenges ({{ $quest->questions->count() }})</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                @foreach($quest->questions as $index => $q)
                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 0.9rem;">
                        <div style="font-weight: 800; color: #4f46e5; margin-bottom: 5px;">Step {{ $index + 1 }} (Lvl {{ $q->level }})</div>
                        <p style="color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $q->question }}</p>
                        <div style="font-weight: 700; color: #64748b; font-size: 0.8rem; margin-top: 5px;">{{ $q->points }} PTS</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Level Details Modal -->
    @if($showModal)
        <div class="modal-blur" wire:click.self="closeModal">
            <div class="modal-box">
                <div style="padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="margin: 0; color: #fbbf24; font-size: 1.5rem;">📜 Level {{ $selectedLevel }} Challenges</h2>
                    <button wire:click="closeModal" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
                </div>
                <div style="padding: 25px; max-height: 60vh; overflow-y: auto;">
                    @forelse($levelQuestions as $q)
                        <div class="question-card">
                            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">{{ str_replace('_', ' ', $q->type) }}</span>
                                <span style="background: rgba(251, 191, 36, 0.2); color: #fbbf24; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">{{ $q->points }} PTS</span>
                            </div>
                            <p style="font-size: 1.1rem; line-height: 1.5; margin-bottom: 15px;">{{ $q->question }}</p>
                            @if($q->type == 'multiple_choice')
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    @foreach($q->options as $opt)
                                        <div style="padding: 8px 12px; border-radius: 8px; font-size: 0.9rem; {{ $opt == $q->answer ? 'background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80;' : 'background: rgba(255,255,255,0.05); color: #cbd5e1;' }}">
                                            {{ $opt }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="padding: 10px; background: rgba(255,255,255,0.05); border-radius: 8px; color: #cbd5e1; font-size: 0.95rem;">
                                    Answer: <strong style="color: white;">{{ $q->answer }}</strong>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="text-align: center; color: #94a3b8;">No challenges found for this level.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
