@extends('teacher.layouts.app')

@section('title', 'Quest Details')
@section('page-title', 'Quest Details')

@push('styles')
<style>
    .quest-details-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
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

    .btn-back:hover { color: var(--accent); }

    .quest-title-area h1 {
        font-size: 2rem;
        color: var(--text-primary);
        font-weight: 800;
        margin-bottom: 10px;
    }

    .quest-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tag-pill {
        padding: 6px 14px;
        background: var(--bg-main);
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
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 4px solid #4a3728;
    }

    .map-container {
        position: relative;
        width: 100%;
        aspect-ratio: 1000 / 600;
        background-color: #f3e5ab;
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

    .node-start .node-marker { background: var(--success); color: white; box-shadow: 0 0 20px rgba(16, 185, 129, 0.6); }
    .node-end .node-marker { background: var(--danger); color: white; box-shadow: 0 0 30px rgba(239, 68, 68, 0.8); animation: node-glow 2s infinite ease-in-out; }

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
    }

    .node-tooltip {
        position: absolute;
        bottom: 120%;
        background: white;
        color: var(--text-primary);
        padding: 10px;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        width: 150px;
        text-align: center;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s;
        box-shadow: var(--shadow-lg);
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
        background: var(--bg-card);
        padding: 25px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .rewards-summary h3 { margin-bottom: 20px; font-size: 1.1rem; color: var(--text-primary); }

    .reward-pills {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .reward-pill {
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 1rem;
    }

    .reward-pill.xp { background: #eef2ff; color: #4f46e5; }
    .reward-pill.ab { background: #fffbeb; color: #d97706; }
    .reward-pill.gp { background: #ecfdf5; color: #059669; }

    .questions-list h3 { margin-bottom: 20px; font-size: 1.1rem; color: var(--text-primary); }

    .questions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .question-mini-card {
        background: var(--bg-card);
        padding: 15px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
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
        color: var(--accent);
        text-transform: uppercase;
    }

    .question-mini-card p { font-size: 0.85rem; color: var(--text-primary); line-height: 1.4; }

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
    }

    .level-details-modal {
        background: var(--bg-card);
        width: 100%;
        max-width: 650px;
        max-height: 85vh;
        overflow-y: auto;
        border-radius: var(--radius);
        padding: 30px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 15px;
    }

    .modal-header h3 {
        color: var(--primary);
        margin: 0;
        font-size: 1.5rem;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-close-modal:hover { color: var(--text-primary); }

    .modal-questions-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .modal-question-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
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
        background: #dbeafe;
        color: #2563eb;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .q-points-badge {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        background: #fef3c7;
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .q-text {
        font-size: 1.05rem;
        color: var(--text-primary);
        line-height: 1.5;
        margin-bottom: 15px;
    }

    .modal-q-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .modal-opt {
        background: var(--bg-card);
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        color: var(--text-secondary);
        border: 1px solid transparent;
    }

    .modal-opt.correct {
        border-color: var(--success);
        background: #d1fae5;
        color: #059669;
    }

    .modal-q-answer {
        font-size: 0.95rem;
        color: var(--text-secondary);
        padding: 10px;
        background: var(--bg-card);
        border-radius: var(--radius-sm);
    }

    .modal-footer {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .quest-summary-panel { grid-template-columns: 1fr; }
        .map-node { transform: translate(-50%, -50%) scale(0.6); }
        .map-node:hover { transform: translate(-50%, -50%) scale(0.75); }
        .modal-q-options { grid-template-columns: 1fr; }
    }
</style>
@endpush

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
            @if($quest->map_image && $quest->map_image !== 'quest_map_bg.png')
                <img src="{{ asset('storage/' . $quest->map_image) }}" alt="RPG Quest Map" class="map-bg">
            @else
                <img src="{{ asset('images/quest_map_bg.png') }}" alt="RPG Quest Map" class="map-bg">
            @endif

            <!-- Landmarks/Nodes -->
            @php
                $positions = [
                    ['left' => 10, 'top' => 85, 'icon' => 'fa-mountain', 'label' => 'Starting Rock'],
                    ['left' => 20, 'top' => 65, 'icon' => 'fa-water', 'label' => 'Whispering Waterfalls'],
                    ['left' => 30, 'top' => 40, 'icon' => 'fa-compass', 'label' => 'Compass Grove'],
                    ['left' => 42, 'top' => 55, 'icon' => 'fa-cloud', 'label' => 'Floating Reaches'],
                    ['left' => 55, 'top' => 35, 'icon' => 'fa-shoe-prints', 'label' => 'Sky-Isle Steps'],
                    ['left' => 68, 'top' => 50, 'icon' => 'fa-question', 'label' => 'The Question Marks'],
                    ['left' => 75, 'top' => 70, 'icon' => 'fa-brain', 'label' => 'Trivia Chamber'],
                    ['left' => 85, 'top' => 45, 'icon' => 'fa-book', 'label' => 'Library of Wisdom'],
                    ['left' => 92, 'top' => 25, 'icon' => 'fa-star', 'label' => 'The Observatory'],
                    ['left' => 88, 'top' => 15, 'icon' => 'fa-crown', 'label' => 'Victory Summit'],
                ];
                
                // Prepare questions grouped by level for JavaScript
                $questionsByLevel = [];
                for ($lvl = 1; $lvl <= $quest->level; $lvl++) {
                    $questionsByLevel[$lvl] = $quest->questions->where('level', $lvl)->values()->toArray();
                }
            @endphp

            <div class="map-nodes">
                @for($lvl = 1; $lvl <= $quest->level; $lvl++)
                    @php
                        $levelQuestions = $quest->questions->where('level', $lvl);
                        $pos = $positions[($lvl - 1) % count($positions)];
                        $isLast = ($lvl == $quest->level);
                        $isFirst = ($lvl == 1);
                        $nodeIcon = $isLast ? 'fa-flag-checkered' : ($isFirst ? 'fa-play' : $pos['icon']);
                    @endphp
                    <div class="map-node {{ $isFirst ? 'node-start' : ($isLast ? 'node-end' : '') }}" 
                         style="left: {{ $pos['left'] }}%; top: {{ $pos['top'] }}%;"
                         data-level="{{ $lvl }}">
                        <div class="node-marker">
                            <i class="fas {{ $nodeIcon }}"></i>
                        </div>
                        <div class="node-label">Level {{ $lvl }}</div>
                        <div class="node-tooltip">
                            <strong>{{ $pos['label'] }}</strong><br>
                            {{ $levelQuestions->count() }} Challenges Found<br>
                            <span style="color: var(--accent)">Worth {{ $levelQuestions->sum('points') }} PTS</span>
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
    <div class="level-details-modal">
        <div class="modal-header">
            <h3><i class="fas fa-scroll"></i> Level <span id="modalLevel">1</span> Challenges</h3>
            <button onclick="closeLevelModal()" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <div id="modalQuestionsList" class="modal-questions-list">
            {{-- Questions will be injected here --}}
        </div>
        <div class="modal-footer">
            <button onclick="closeLevelModal()" class="btn btn-primary">Close Journey</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLevelDetails(level, questions) {
    document.getElementById('modalLevel').textContent = level;
    const list = document.getElementById('modalQuestionsList');
    list.innerHTML = '';

    if (questions.length === 0) {
        list.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 20px;">No questions assigned to this level yet.</p>';
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
                        ${q.options ? q.options.map(opt => `<div class="modal-opt ${opt === q.answer ? 'correct' : ''}">${opt}</div>`).join('') : ''}
                    </div>
                ` : `
                    <div class="modal-q-answer">Correct Answer: <strong>${q.answer}</strong></div>
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

// Questions data grouped by level (from PHP)
const questionsByLevel = {{ json_encode($questionsByLevel) }};

// Add click event listeners to all map nodes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.map-node').forEach(node => {
        node.addEventListener('click', function() {
            const level = this.getAttribute('data-level');
            const questions = questionsByLevel[level] || [];
            showLevelDetails(level, questions);
        });
    });
});
</script>
@endpush
@endsection
