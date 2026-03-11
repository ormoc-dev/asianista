@extends('teacher.dashboard')

@section('content')
<div class="quest-details-container">
    <div class="quest-details-header">
        <a href="{{ route('teacher.quest') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Board
        </a>
        <div class="quest-title-area">
            <h1>{{ $quest->title }}</h1>
            <div class="quest-tags">
                <span class="tag-pill"><i class="fas fa-scroll"></i> {{ $quest->difficulty ?? 'Epic' }}</span>
                <span class="tag-pill"><i class="fas fa-users"></i> {{ $quest->grade->name ?? 'N/A' }} - {{ $quest->section->name ?? 'N/A' }}</span>
                <span class="tag-pill"><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($quest->due_date)->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <div class="map-wrapper">
        <div class="map-container">
            <!-- Map Background -->
            <img src="{{ asset('images/quest_map_bg.png') }}" alt="RPG Quest Map" class="map-bg">

            <!-- SVG Path Layer -->
            <svg class="map-paths" viewBox="0 0 1000 600">
                <path d="M 500 520 L 250 330 L 150 400 L 400 240 L 550 360 L 750 270 L 750 480 L 850 390 L 800 120" 
                      fill="none" stroke="rgba(255, 212, 59, 0.4)" stroke-width="4" stroke-dasharray="10,10" />
            </svg>

            <!-- Landmarks/Nodes -->
            <div class="map-nodes">
                <!-- Node 1: Starting Rock -->
                <div class="map-node node-start" style="left: 50%; top: 86%;">
                    <div class="node-marker"><i class="fas fa-mountain"></i></div>
                    <div class="node-label">Starting Rock</div>
                    <div class="node-tooltip">The journey begins here. Prepare your mind!</div>
                </div>

                <!-- Node 2: Whispering Waterfalls -->
                <div class="map-node" style="left: 25%; top: 55%;">
                    <div class="node-marker"><i class="fas fa-water"></i></div>
                    <div class="node-label">Whispering Waterfalls</div>
                    <div class="node-tooltip">Listen to the flow of logic. Step 2 awaits.</div>
                </div>

                <!-- Node 3: Compass Grove -->
                <div class="map-node" style="left: 15%; top: 66%;">
                    <div class="node-marker"><i class="fas fa-compass"></i></div>
                    <div class="node-label">The Compass Grove</div>
                    <div class="node-tooltip">Find your direction through difficult queries.</div>
                </div>

                <!-- Node 4: Floating Reaches -->
                <div class="map-node" style="left: 40%; top: 40%;">
                    <div class="node-marker"><i class="fas fa-cloud"></i></div>
                    <div class="node-label">The Floating Reaches</div>
                    <div class="node-tooltip">Elevate your understanding in the sky isles.</div>
                </div>

                <!-- Node 5: Sky-Isle Steps -->
                <div class="map-node" style="left: 55%; top: 60%;">
                    <div class="node-marker"><i class="fas fa-shoe-prints"></i></div>
                    <div class="node-label">Sky-Isle Steps</div>
                    <div class="node-tooltip">Climbing the ladder of wisdom.</div>
                </div>

                <!-- Node 6: Question Marks -->
                <div class="map-node" style="left: 75%; top: 45%;">
                    <div class="node-marker"><i class="fas fa-question"></i></div>
                    <div class="node-label">The Question Marks</div>
                    <div class="node-tooltip">Mysteries to solve and XP to earn.</div>
                </div>

                <!-- Node 7: Question Marks of Trivia -->
                <div class="map-node" style="left: 75%; top: 80%;">
                    <div class="node-marker"><i class="fas fa-brain"></i></div>
                    <div class="node-label">Trivia Chamber</div>
                    <div class="node-tooltip">Prove your mastery of Asianista lore.</div>
                </div>

                <!-- Node 8: Library of Wisdom -->
                <div class="map-node" style="left: 85%; top: 65%;">
                    <div class="node-marker"><i class="fas fa-book"></i></div>
                    <div class="node-label">Library of Wisdom</div>
                    <div class="node-tooltip">The archive of all recorded knowledge.</div>
                </div>

                <!-- Node 9: Observatory -->
                <div class="map-node node-end" style="left: 80%; top: 20%;">
                    <div class="node-marker"><i class="fas fa-star"></i></div>
                    <div class="node-label">The Observatory</div>
                    <div class="node-tooltip">Reach for the stars! Quest Completion point.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Sidebar/Section -->
    <div class="quest-summary-panel">
        <div class="rewards-summary">
            <h3>Quest Rewards</h3>
            <div class="reward-pills">
                <div class="reward-pill xp"><i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP</div>
                <div class="reward-pill ab"><i class="fas fa-bolt"></i> {{ $quest->ab_reward ?? 0 }} AB</div>
                <div class="reward-pill gp"><i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }} GP</div>
            </div>
        </div>

        <div class="questions-list">
            <h3>Quest Components ({{ $quest->questions->count() }} Steps)</h3>
            <div class="questions-grid">
                @foreach($quest->questions as $index => $question)
                <div class="question-mini-card">
                    <span class="step-num">Step {{ $index + 1 }}</span>
                    <p>{{ Str::limit($question->question, 60) }}</p>
                    <span class="step-pts">{{ $question->points }} PTS</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .quest-details-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
        padding: 20px;
    }

    .quest-details-header {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: color 0.2s;
    }

    .btn-back:hover { color: var(--accent-dark); }

    .quest-title-area h1 {
        font-size: 2rem;
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 10px;
    }

    .quest-tags {
        display: flex;
        gap: 10px;
    }

    .tag-pill {
        padding: 6px 14px;
        background: rgba(0, 35, 102, 0.05);
        border-radius: 999px;
        font-size: 0.8rem;
        color: var(--primary);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* MAP STYLING */
    .map-wrapper {
        width: 100%;
        background: #000;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        border: 4px solid #4a3728; /* Wood frame feel */
    }

    .map-container {
        position: relative;
        width: 100%;
        aspect-ratio: 1000 / 600;
        background-color: #f3e5ab; /* Parchment fall back */
    }

    .map-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    .map-paths {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
    }

    .map-nodes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 20;
    }

    .map-node {
        position: absolute;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .map-node:hover {
        transform: translate(-50%, -50%) scale(1.1);
        z-index: 100;
    }

    .node-marker {
        width: 45px;
        height: 45px;
        background: radial-gradient(circle at 30% 30%, #fff, var(--accent));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.2rem;
        box-shadow: 0 0 20px rgba(255, 212, 59, 0.8);
        border: 3px solid #fff;
    }

    .node-start .node-marker { background: #10b981; color: white; box-shadow: 0 0 20px rgba(16, 185, 129, 0.6); }
    .node-end .node-marker { background: #ef4444; color: white; box-shadow: 0 0 30px rgba(239, 68, 68, 0.8); animation: node-glow 2s infinite ease-in-out; }

    @keyframes node-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.5); }
        50% { box-shadow: 0 0 40px rgba(239, 68, 68, 0.8); }
    }

    .node-label {
        margin-top: 8px;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .node-tooltip {
        position: absolute;
        bottom: 120%;
        background: white;
        color: var(--text-dark);
        padding: 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 150px;
        text-align: center;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .node-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-color: white transparent transparent transparent;
        border-style: solid;
        border-width: 6px;
    }

    .map-node:hover .node-tooltip {
        opacity: 1;
        bottom: 140%;
    }

    /* SUMMARY PANEL */
    .quest-summary-panel {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 30px;
    }

    .rewards-summary {
        background: #fff;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .rewards-summary h3 { margin-bottom: 20px; font-size: 1.1rem; color: var(--primary); }

    .reward-pills {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .reward-pill {
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 1rem;
    }

    .reward-pill.xp { background: #eef2ff; color: #4f46e5; }
    .reward-pill.ab { background: #fffbeb; color: #d97706; }
    .reward-pill.gp { background: #ecfdf5; color: #059669; }

    .questions-list h3 { margin-bottom: 20px; font-size: 1.1rem; color: var(--primary); }

    .questions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .question-mini-card {
        background: #fff;
        padding: 15px;
        border-radius: 15px;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: all 0.2s;
    }

    .question-mini-card:hover {
        transform: scale(1.02);
        border-color: var(--accent);
    }

    .step-num {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--accent-dark);
        text-transform: uppercase;
    }

    .question-mini-card p { font-size: 0.85rem; color: var(--text-dark); line-height: 1.4; }

    .step-pts {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted);
        align-self: flex-end;
    }

    @media (max-width: 768px) {
        .quest-summary-panel { grid-template-columns: 1fr; }
        .quest-details-container { padding: 10px; }
        .map-node { transform: translate(-50%, -50%) scale(0.6); }
        .map-node:hover { transform: translate(-50%, -50%) scale(0.75); }
    }
</style>
@endsection
