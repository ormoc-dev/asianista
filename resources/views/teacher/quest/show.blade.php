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
        pointer-events: none;
        user-select: none;
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
        z-index: 30;
        pointer-events: auto;
    }

    .map-node {
        position: absolute;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        /* Larger hit area so each level is easy to tap (markers alone are tiny) */
        padding: 14px 16px;
        box-sizing: content-box;
        /* Higher levels stack above so overlapping nodes stay clickable */
        z-index: var(--map-node-z, 20);
    }

    .map-node:hover {
        transform: translate(-50%, -50%) scale(1.1);
        z-index: calc(var(--map-node-z, 20) + 50);
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
        max-width: min(120px, 28vw);
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: center;
        user-select: none;
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
    .reward-pill.ap { background: #fffbeb; color: #d97706; }
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
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 25px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 15px;
    }

    .modal-header-main {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .modal-header h3 {
        color: var(--primary);
        margin: 0;
        font-size: 1.5rem;
        line-height: 1.25;
    }

    .btn-modal-back {
        flex-shrink: 0;
        display: none;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--bg-main);
        color: var(--text-primary);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
    }

    .btn-modal-back:hover {
        border-color: var(--primary);
        background: #eef2ff;
    }

    .modal-picker-hint {
        margin: 0 0 16px;
        font-size: 0.9rem;
        color: var(--text-secondary);
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

    .modal-question-pick {
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        text-align: left;
        width: 100%;
        box-sizing: border-box;
    }

    .modal-question-pick:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.12);
        transform: translateY(-1px);
    }

    .modal-question-pick .pick-cue {
        margin-top: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary);
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

    .student-progress-section {
        margin-top: 24px;
        border-top: 1px solid var(--border);
        padding-top: 16px;
    }

    .student-progress-section h4 {
        margin: 0 0 12px;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .student-progress-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .student-progress-col {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
    }

    .student-progress-col h5 {
        margin: 0 0 8px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .student-progress-col ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .student-progress-col li {
        font-size: 0.88rem;
        color: var(--text-secondary);
    }

    .student-progress-col .empty {
        color: var(--text-muted);
        font-style: italic;
    }

    .student-progress-col.passed h5 { color: #059669; }
    .student-progress-col.in-progress h5 { color: #2563eb; }
    .student-progress-col.failed h5 { color: #dc2626; }
    .student-progress-col.not-started h5 { color: #6b7280; }

    @media (max-width: 768px) {
        .quest-summary-panel { grid-template-columns: 1fr; }
        .map-node { transform: translate(-50%, -50%) scale(0.6); }
        .map-node:hover { transform: translate(-50%, -50%) scale(0.75); }
        .modal-q-options { grid-template-columns: 1fr; }
        .student-progress-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="quest-details-container">
    <div class="quest-details-header">
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <a href="{{ route('teacher.quest') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Board
            </a>
            <a href="{{ route('teacher.quest.edit', $quest) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Quest
            </a>
        </div>
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

            <!-- Landmarks/Nodes (positions from quest map_pins / admin layout, same as student map) -->
            @php
                $levelPins = \App\Models\QuestMapLayout::pinsForQuest($quest);
                $pathD = \App\Models\QuestMapLayout::svgPathD($levelPins);

                $questionsByLevel = [];
                for ($lvl = 1; $lvl <= $quest->level; $lvl++) {
                    $questionsByLevel[$lvl] = $quest->questions->where('level', $lvl)->values()->toArray();
                }
            @endphp

            @if($pathD !== '')
            <svg class="map-paths" viewBox="0 0 1000 600" aria-hidden="true">
                <path d="{{ $pathD }}" fill="none" stroke="rgba(255, 212, 59, 0.45)" stroke-width="5" stroke-dasharray="12,12" />
            </svg>
            @endif

            <div class="map-nodes">
                @for($lvl = 1; $lvl <= $quest->level; $lvl++)
                    @php
                        $levelQuestions = $quest->questions->where('level', $lvl);
                        $pin = $levelPins[$lvl - 1] ?? ['left' => 50, 'top' => 50, 'name' => 'Level '.$lvl, 'icon' => 'fa-map-marker-alt'];
                        $leftPos = (float) ($pin['left'] ?? 50);
                        $topPos = (float) ($pin['top'] ?? 50);
                        $landmarkName = (string) ($pin['name'] ?? 'Level '.$lvl);
                        $pinIcon = (string) ($pin['icon'] ?? 'fa-map-marker-alt');
                        $isLast = ($lvl == $quest->level);
                        $isFirst = ($lvl == 1);
                        $nodeIcon = $isLast ? 'fa-flag-checkered' : ($isFirst ? 'fa-play' : $pinIcon);
                    @endphp
                    <div class="map-node {{ $isFirst ? 'node-start' : ($isLast ? 'node-end' : '') }}"
                         data-level="{{ $lvl }}"
                         data-pos-left="{{ sprintf('%.3f', $leftPos) }}"
                         data-pos-top="{{ sprintf('%.3f', $topPos) }}"
                         data-node-z="{{ 20 + $lvl }}"
                         role="button"
                         tabindex="0">
                        <div class="node-marker">
                            <i class="fas {{ $nodeIcon }}"></i>
                        </div>
                        <div class="node-label">Level {{ $lvl }}</div>
                        <div class="node-tooltip">
                            <strong>{{ $landmarkName }}</strong><br>
                            {{ $levelQuestions->count() }} Challenges Found<br>
                            <span style="color: var(--accent)">Worth {{ $levelQuestions->sum('points') }} PTS</span>
                        </div>
                    </div>
                @endfor
            </div>
            <script>
                (function () {
                    document.querySelectorAll('.map-nodes .map-node').forEach(function (n) {
                        var l = n.getAttribute('data-pos-left');
                        var t = n.getAttribute('data-pos-top');
                        var z = n.getAttribute('data-node-z');
                        if (l !== null && l !== '') n.style.left = l + '%';
                        if (t !== null && t !== '') n.style.top = t + '%';
                        if (z !== null && z !== '') n.style.setProperty('--map-node-z', z);
                    });
                })();
            </script>
        </div>
    </div>

    <!-- Questions Sidebar/Section -->
    <div class="quest-summary-panel">
        <div class="rewards-summary">
            <h3>Quest Rewards</h3>
            <div class="reward-pills">
                <div class="reward-pill xp"><i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP</div>
                <div class="reward-pill ap"><i class="fas fa-bolt"></i> {{ $quest->ab_reward ?? 0 }} AP</div>
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
            <div class="modal-header-main">
                <button type="button" id="modalQuestionBack" class="btn-modal-back" aria-label="Back to challenge list">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <h3><i class="fas fa-scroll"></i> <span id="modalTitleText">Level <span id="modalLevel">1</span> Challenges</span></h3>
            </div>
            <button type="button" onclick="closeLevelModal()" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <p id="modalPickerHint" class="modal-picker-hint" style="display: none;">
            This level has more than one challenge. Click a card to view the full question, answer, and who passed or is still on it.
        </p>
        <div id="modalQuestionsList" class="modal-questions-list">
            {{-- Question picker or detail view --}}
        </div>
        <div id="modalStudentProgressSection" class="student-progress-section" style="display: none;">
            <h4><i class="fas fa-users"></i> Student Progress <span style="font-weight: 500; color: var(--text-muted);">(this challenge)</span></h4>
            <div id="modalStudentProgress" class="student-progress-grid">
                {{-- Per-question student status --}}
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeLevelModal()" class="btn btn-primary">Close Journey</button>
        </div>
    </div>
</div>

@push('scripts')
<script type="application/json" id="quest-questions-by-level">
{!! json_encode($questionsByLevel, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/json" id="quest-students-by-question">
{!! json_encode($studentsByQuestion ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

let currentModalLevel = null;

const questionsByLevel = JSON.parse(document.getElementById('quest-questions-by-level').textContent);
const studentsByQuestion = JSON.parse(document.getElementById('quest-students-by-question').textContent);

function questionsForLevel(level) {
    const k = String(level);
    const raw = questionsByLevel[k] ?? questionsByLevel[level];
    const arr = Array.isArray(raw) ? raw.slice() : [];
    arr.sort((a, b) => (Number(a.id) || 0) - (Number(b.id) || 0));
    return arr;
}

function progressForQuestion(questionId) {
    const k = String(questionId);
    const raw = studentsByQuestion[k] ?? studentsByQuestion[questionId];
    if (raw && typeof raw === 'object') {
        return {
            passed: Array.isArray(raw.passed) ? raw.passed : [],
            in_progress: Array.isArray(raw.in_progress) ? raw.in_progress : [],
            failed: Array.isArray(raw.failed) ? raw.failed : [],
            not_started: Array.isArray(raw.not_started) ? raw.not_started : [],
        };
    }
    return { passed: [], in_progress: [], failed: [], not_started: [] };
}

function renderStudentColumn(title, className, students) {
    const safeStudents = Array.isArray(students) ? students : [];
    const items = safeStudents.length
        ? safeStudents.map(name => `<li>${escapeHtml(name)}</li>`).join('')
        : '<li class="empty">None</li>';

    return `
        <div class="student-progress-col ${className}">
            <h5>${title} (${safeStudents.length})</h5>
            <ul>${items}</ul>
        </div>
    `;
}

function renderProgressGrid(progress) {
    return (
        renderStudentColumn('Passed', 'passed', progress.passed) +
        renderStudentColumn('In Progress', 'in-progress', progress.in_progress) +
        renderStudentColumn('Failed', 'failed', progress.failed) +
        renderStudentColumn('Not Started', 'not-started', progress.not_started)
    );
}

function buildQuestionDetailHtml(q) {
    const typeLabel = escapeHtml(String(q.type || '').replace(/-/g, ' ') || 'Question');
    const pts = escapeHtml(q.points != null ? String(q.points) : '0');
    const body = q.type === 'multiple_choice'
        ? `<div class="modal-q-options">${Array.isArray(q.options) ? q.options.map(opt => {
            const o = escapeHtml(opt);
            const isCorrect = opt === q.answer;
            return `<div class="modal-opt ${isCorrect ? 'correct' : ''}">${o}</div>`;
        }).join('') : ''}</div>`
        : `<div class="modal-q-answer">Correct Answer: <strong>${escapeHtml(q.answer)}</strong></div>`;
    return `
        <div class="modal-question-card">
            <div class="modal-q-header">
                <span class="q-type-badge">${typeLabel}</span>
                <span class="q-points-badge">${pts} PTS</span>
            </div>
            <p class="q-text">${escapeHtml(q.question)}</p>
            ${body}
        </div>
    `;
}

function setModalListMode(level, questions) {
    currentModalLevel = String(level);
    document.getElementById('modalTitleText').innerHTML =
        'Level <span id="modalLevel">' + escapeHtml(currentModalLevel) + '</span> Challenges';

    const backBtn = document.getElementById('modalQuestionBack');
    const hint = document.getElementById('modalPickerHint');
    const progSection = document.getElementById('modalStudentProgressSection');

    backBtn.style.display = 'none';
    progSection.style.display = 'none';
    hint.style.display = questions.length > 1 ? 'block' : 'none';

    const list = document.getElementById('modalQuestionsList');
    list.innerHTML = '';

    if (questions.length === 0) {
        list.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 20px;">No questions assigned to this level yet.</p>';
        return;
    }

    questions.forEach(function (q, i) {
        const card = document.createElement('button');
        card.type = 'button';
        card.className = 'modal-question-card modal-question-pick';
        card.setAttribute('data-question-id', String(q.id));
        const previewRaw = String(q.question || '');
        const preview = previewRaw.length > 160 ? previewRaw.slice(0, 160) + '…' : previewRaw;
        const typeLabel = escapeHtml(String(q.type || '').replace(/-/g, ' ') || 'Question');
        const pts = escapeHtml(q.points != null ? String(q.points) : '0');
        card.innerHTML = `
            <div class="modal-q-header">
                <span class="q-type-badge">${typeLabel}</span>
                <span class="q-points-badge">${pts} PTS</span>
            </div>
            <p class="q-text" style="margin-bottom: 0;">${escapeHtml(preview)}</p>
            <div class="pick-cue">View full challenge &amp; student progress <i class="fas fa-chevron-right"></i></div>
        `;
        list.appendChild(card);
    });
}

function showQuestionDetail(q) {
    const backBtn = document.getElementById('modalQuestionBack');
    const hint = document.getElementById('modalPickerHint');
    const progSection = document.getElementById('modalStudentProgressSection');
    const list = document.getElementById('modalQuestionsList');
    const progress = document.getElementById('modalStudentProgress');

    backBtn.style.display = 'inline-flex';
    hint.style.display = 'none';
    progSection.style.display = 'block';

    document.getElementById('modalTitleText').textContent =
        'Challenge — Level ' + (currentModalLevel != null ? currentModalLevel : '');

    list.innerHTML = buildQuestionDetailHtml(q);

    const pq = progressForQuestion(q.id);
    progress.innerHTML = renderProgressGrid(pq);

    document.getElementById('levelDetailsModal').style.display = 'flex';
}

function showLevelQuestionPicker(level) {
    const questions = questionsForLevel(level);
    setModalListMode(level, questions);
    document.getElementById('levelDetailsModal').style.display = 'flex';
}

function closeLevelModal() {
    document.getElementById('levelDetailsModal').style.display = 'none';
    currentModalLevel = null;
    document.getElementById('modalQuestionBack').style.display = 'none';
    document.getElementById('modalPickerHint').style.display = 'none';
    document.getElementById('modalStudentProgressSection').style.display = 'none';
    document.getElementById('modalQuestionsList').innerHTML = '';
    document.getElementById('modalTitleText').innerHTML =
        'Level <span id="modalLevel">1</span> Challenges';
}

function openLevelFromNode(node) {
    if (!node) return;
    const level = node.getAttribute('data-level');
    if (!level) return;
    showLevelQuestionPicker(level);
}

document.addEventListener('DOMContentLoaded', function() {
    const mapNodesRoot = document.querySelector('.map-nodes');
    if (mapNodesRoot) {
        mapNodesRoot.addEventListener('click', function (e) {
            const node = e.target.closest('.map-node');
            if (!node || !mapNodesRoot.contains(node)) return;
            openLevelFromNode(node);
        });
        mapNodesRoot.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const node = e.target.closest('.map-node');
            if (!node || !mapNodesRoot.contains(node)) return;
            e.preventDefault();
            openLevelFromNode(node);
        });
    }

    const backBtn = document.getElementById('modalQuestionBack');
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            if (currentModalLevel == null) return;
            const questions = questionsForLevel(currentModalLevel);
            setModalListMode(currentModalLevel, questions);
        });
    }

    const qList = document.getElementById('modalQuestionsList');
    if (qList) {
        qList.addEventListener('click', function (e) {
            const pick = e.target.closest('.modal-question-pick');
            if (!pick || !qList.contains(pick)) return;
            const id = pick.getAttribute('data-question-id');
            if (!id || currentModalLevel == null) return;
            const questions = questionsForLevel(currentModalLevel);
            const q = questions.find(function (qq) { return String(qq.id) === String(id); });
            if (q) showQuestionDetail(q);
        });
    }
});
</script>
@endpush
@endsection
