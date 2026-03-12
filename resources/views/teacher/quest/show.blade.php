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
                <span class="tag-pill"><i class="fas fa-medal"></i> Level {{ $quest->level }}</span>
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
                @php
                    $positions = [
                        ['left' => 50, 'top' => 86, 'icon' => 'fa-mountain', 'label' => 'Starting Rock'],
                        ['left' => 25, 'top' => 55, 'icon' => 'fa-water', 'label' => 'Whispering Waterfalls'],
                        ['left' => 15, 'top' => 66, 'icon' => 'fa-compass', 'label' => 'Compass Grove'],
                        ['left' => 40, 'top' => 40, 'icon' => 'fa-cloud', 'label' => 'Floating Reaches'],
                        ['left' => 55, 'top' => 60, 'icon' => 'fa-shoe-prints', 'label' => 'Sky-Isle Steps'],
                        ['left' => 75, 'top' => 45, 'icon' => 'fa-question', 'label' => 'The Question Marks'],
                        ['left' => 75, 'top' => 80, 'icon' => 'fa-brain', 'label' => 'Trivia Chamber'],
                        ['left' => 85, 'top' => 65, 'icon' => 'fa-book', 'label' => 'Library of Wisdom'],
                        ['left' => 80, 'top' => 20, 'icon' => 'fa-star', 'label' => 'The Observatory'],
                    ];
                @endphp

                @for($lvl = 1; $lvl <= $quest->level; $lvl++)
                    @php
                        $levelQuestions = $quest->questions->where('level', $lvl);
                        $pos = $positions[($lvl - 1) % count($positions)];
                        $isLast = ($lvl == $quest->level);
                        $isFirst = ($lvl == 1);
                    @endphp
                    <div class="map-node {{ $isFirst ? 'node-start' : ($isLast ? 'node-end' : '') }}" 
                         style="left: {{ $pos['left'] }}%; top: {{ $pos['top'] }}%;"
                         onclick="showLevelDetails({{ $lvl }}, {{ json_encode($levelQuestions->values()) }})">
                        <div class="node-marker">
                            <i class="fas {{ $isLast ? 'fa-flag-checkered' : 'fa-fort-awesome' }}"></i>
                        </div>
                        <div class="node-label">Level {{ $lvl }}</div>
                        <div class="node-tooltip">
                            <strong>{{ $pos['label'] }}</strong><br>
                            {{ $levelQuestions->count() }} Challenges Found<br>
                            <span style="color: var(--accent-dark)">Worth {{ $levelQuestions->sum('points') }} PTS</span>
                        </div>
                    </div>
                @endfor
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
                    <span class="step-num">Step {{ $index + 1 }} (LVL {{ $question->level }})</span>
                    <p>{{ Str::limit($question->question, 60) }}</p>
                    <span class="step-pts">{{ $question->points }} PTS</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- LEVEL DETAILS MODAL -->
<div id="levelDetailsModal" class="modal-overlay" style="display: none;">
    <div class="modal-box level-details-modal">
        <div class="modal-header">
            <h3><i class="fas fa-scroll"></i> Level <span id="modalLevel">1</span> Challenges</h3>
            <button onclick="closeLevelModal()" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <div id="modalQuestionsList" class="modal-questions-list">
            {{-- Questions will be injected here --}}
        </div>
        <div class="modal-footer">
            <button onclick="closeLevelModal()" class="btn-ok">Close Journey</button>
        </div>
    </div>
</div>

<script>
function showLevelDetails(level, questions) {
    document.getElementById('modalLevel').textContent = level;
    const list = document.getElementById('modalQuestionsList');
    list.innerHTML = '';

    if (questions.length === 0) {
        list.innerHTML = '<p class="no-questions">No questions assigned to this level yet.</p>';
    } else {
        questions.forEach((q, i) => {
            const card = document.createElement('div');
            card.className = 'modal-question-card';
            card.innerHTML = `
                <div class="modal-q-header">
                    <span class="q-type-badge">${q.type.replace('-', ' ')}</span>
                    <span class="q-points-badge">${q.points} PTS</span>
                </div>
                <p class="q-text">${q.question}</p>
                ${q.type === 'multiple_choice' ? `
                    <div class="modal-q-options">
                        ${q.options ? q.options.map(opt => `<div class="modal-opt ${opt === q.correct_answer ? 'correct' : ''}">${opt}</div>`).join('') : ''}
                    </div>
                ` : `
                    <div class="modal-q-answer">Correct Answer: <strong>${q.correct_answer}</strong></div>
                `}
            `;
            list.appendChild(card);
        });
    }
    document.getElementById('levelDetailsModal').style.display = 'flex';
}

function closeLevelModal() {
    document.getElementById('levelDetailsModal').style.display = 'none';
}
</script>

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

    /* MODAL STYLES */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease-out;
    }

    .level-details-modal {
        background: radial-gradient(circle at top right, #0f172a, #0b1121);
        width: 100%;
        max-width: 650px;
        max-height: 85vh;
        overflow-y: auto;
        border: 1px solid rgba(255, 212, 59, 0.2);
        box-shadow: 0 0 50px rgba(0,0,0,0.8);
        border-radius: 20px;
        padding: 30px;
        text-align: left !important;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 15px;
    }

    .modal-header h3 {
        color: var(--accent);
        margin: 0;
        font-size: 1.5rem;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-close-modal:hover { color: #fff; }

    .modal-questions-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .modal-question-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 15px;
        padding: 20px;
    }

    .modal-q-header {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .q-type-badge {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .q-points-badge {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        background: rgba(255, 212, 59, 0.2);
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .q-text {
        font-size: 1.05rem;
        color: #e2e8f0;
        line-height: 1.5;
        margin-bottom: 15px;
    }

    .modal-q-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .modal-opt {
        background: rgba(255,255,255,0.05);
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #cbd5e1;
        border: 1px solid transparent;
    }

    .modal-opt.correct {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.1);
        color: #4ade80;
    }

    .modal-q-answer {
        font-size: 0.95rem;
        color: #94a3b8;
        padding: 10px;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
    }

    .modal-footer {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-ok {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 12px 25px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
        transition: 0.2s;
    }

    .btn-ok:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 212, 59, 0.3);
    }

    @media (max-width: 768px) {
        .quest-summary-panel { grid-template-columns: 1fr; }
        .quest-details-container { padding: 10px; }
        .map-node { transform: translate(-50%, -50%) scale(0.6); }
        .map-node:hover { transform: translate(-50%, -50%) scale(0.75); }
        .modal-q-options { grid-template-columns: 1fr; }
    }
</style>
@endsection
