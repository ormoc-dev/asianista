@extends('student.dashboard')

@section('content')
<div class="quest-view-container">
    <div class="quest-view-header">
        <a href="{{ route('student.quest') }}" class="btn-back">
            <i class="fas fa-chevron-left"></i> Quest Board
        </a>
        <div class="quest-title-shield">
            <h2>{{ $quest->title }}</h2>
            <div class="quest-meta-row">
                <span class="meta-badge"><i class="fas fa-scroll"></i> {{ $quest->difficulty ?? 'Epic' }}</span>
                <span class="meta-badge"><i class="fas fa-medal"></i> Level {{ $quest->level }}</span>
                <span class="meta-badge"><i class="fas fa-star"></i> {{ $quest->xp_reward ?? 0 }} XP</span>
                <span class="meta-badge"><i class="fas fa-coins"></i> {{ $quest->gp_reward ?? 0 }} GP</span>
            </div>
        </div>
    </div>

    @php
        // Order questions by level, then by id (same order as gameplay)
        $orderedQuestions = $quest->questions->sortBy('level')->sortBy('id')->values();
        $totalQuestions = $orderedQuestions->count();
        $completedCount = 0;
        $currentQuestionIndex = 0;
        $status = $attempt->status ?? 'not_started';

        if ($attempt) {
            // Find current question position in ordered list
            $questionIds = $orderedQuestions->pluck('id')->toArray();
            $currentQuestionIndex = array_search($attempt->current_question_id, $questionIds);
            
            // If found, completed = all questions before current one
            if ($currentQuestionIndex !== false) {
                $completedCount = $currentQuestionIndex;
            } else {
                // If current_question_id not found, student might be at start
                $completedCount = 0;
            }
            
            if ($status === 'completed') {
                $completedCount = $totalQuestions;
            }
        }
        
        $progressPercent = $totalQuestions > 0 ? ($completedCount / $totalQuestions) * 100 : 0;
        
        // Map image
        $mapImage = $quest->map_image ?? 'quest_map_bg.png';
        $mapImageUrl = str_starts_with($mapImage, 'quest_maps/') 
            ? asset('storage/' . $mapImage) 
            : asset('images/' . $mapImage);
    @endphp

    <div class="map-exploration-area">
        <div class="map-frame" style="background-image: url('{{ $mapImageUrl }}'); background-size: cover; background-position: center;">
            
            <!-- Path System -->
            <svg class="map-svg-layer" viewBox="0 0 1000 600">
                <path d="M 500 520 L 250 330 L 150 400 L 400 240 L 550 360 L 750 270 L 750 480 L 850 390 L 800 120" 
                      fill="none" stroke="rgba(255, 212, 59, 0.4)" stroke-width="5" stroke-dasharray="12,12" />
                <path d="M 500 520 L 250 330 L 150 400 L 400 240 L 550 360 L 750 270 L 750 480 L 850 390 L 800 120" 
                      fill="none" stroke="var(--accent)" stroke-width="5" 
                      stroke-dasharray="1000" stroke-dashoffset="{{ 1000 - (1000 * ($progressPercent / 100)) }}"
                      style="transition: stroke-dashoffset 1s ease-in-out;" />
            </svg>

            <!-- Landmarks (Visual representation of progress) -->
            <div class="interactive-landmarks">
                @php
                    $positions = [
                        ['left' => 50, 'top' => 86, 'icon' => 'fa-mountain', 'name' => 'Gate of Entry'],
                        ['left' => 25, 'top' => 55, 'icon' => 'fa-water', 'name' => 'Whispering Falls'],
                        ['left' => 15, 'top' => 66, 'icon' => 'fa-compass', 'name' => 'Compass Grove'],
                        ['left' => 40, 'top' => 40, 'icon' => 'fa-cloud', 'name' => 'Floating Reaches'],
                        ['left' => 55, 'top' => 60, 'icon' => 'fa-shoe-prints', 'name' => 'Sky-Isle Steps'],
                        ['left' => 75, 'top' => 45, 'icon' => 'fa-question', 'name' => 'Mystery Landmark'],
                        ['left' => 75, 'top' => 80, 'icon' => 'fa-brain', 'name' => 'Trivia Chamber'],
                        ['left' => 85, 'top' => 65, 'icon' => 'fa-book', 'name' => 'Library of Wisdom'],
                        ['left' => 80, 'top' => 20, 'icon' => 'fa-crown', 'name' => 'The Observatory'],
                    ];
                @endphp

                @for($lvl = 1; $lvl <= $quest->level; $lvl++)
                @php
                    $levelQuestions = $orderedQuestions->where('level', $lvl);
                    $totalLvlQuestions = $levelQuestions->count();
                    
                    // Get current question object if exists
                    $currentQuestion = $attempt ? $orderedQuestions->where('id', $attempt->current_question_id)->first() : null;
                    
                    // Status calculation for landmarks
                    $isLvlCompleted = false;
                    $isLvlCurrent = false;
                    $isLvlLocked = true;
                    $completedInLevel = 0;

                    if ($status === 'completed') {
                        $isLvlCompleted = true;
                        $isLvlLocked = false;
                        $completedInLevel = $totalLvlQuestions;
                    } else if ($attempt && $currentQuestion) {
                        $currLvl = $currentQuestion->level;
                        
                        if ($lvl < $currLvl) {
                            $isLvlCompleted = true;
                            $isLvlLocked = false;
                            $completedInLevel = $totalLvlQuestions;
                        } else if ($lvl == $currLvl) {
                            $isLvlCurrent = true;
                            $isLvlLocked = false;
                            
                            // For the tooltip: count questions in this level before the current one
                            $qIdsInLvl = $levelQuestions->pluck('id')->toArray();
                            $currIdxInLvl = array_search($attempt->current_question_id, $qIdsInLvl);
                            $completedInLevel = $currIdxInLvl !== false ? $currIdxInLvl : 0;
                        }
                    } else if ($lvl == 1) {
                        // Not started, level 1 is active
                        $isLvlCurrent = true;
                        $isLvlLocked = false;
                    }

                    $pos = $positions[($lvl - 1) % count($positions)];
                @endphp
                <div class="landmark-node" style="left: {{ $pos['left'] }}%; top: {{ $pos['top'] }}%;" 
                     @if(!$isLvlLocked) onclick="showLevelDetails({{ $lvl }}, {{ json_encode($levelQuestions->values()) }})" @endif>
                    <div class="node-icon {{ $isLvlCompleted ? 'finish' : ($isLvlCurrent ? 'active' : 'locked') }}">
                        <i class="fas {{ $isLvlCompleted ? 'fa-check' : ($isLvlCurrent ? 'fa-play' : 'fa-lock') }}"></i>
                    </div>
                    <div class="node-tag">Level {{ $lvl }}</div>
                    <div class="node-tooltip">
                        <strong>{{ $pos['name'] }}</strong><br>
                        @if($isLvlLocked)
                            <i class="fas fa-lock"></i> Locked Stage
                        @else
                            {{ $totalLvlQuestions }} Challenges ({{ $completedInLevel }}/{{ $totalLvlQuestions }})
                        @endif
                    </div>
                </div>
                @endfor
            </div>

            <!-- Action Floating Menu -->
            <div class="map-action-card">
                <div class="action-card-header">
                    <h4>Quest Progress</h4>
                    <span class="progress-percent">{{ round($progressPercent) }}%</span>
                </div>
                <div class="action-card-progress">
                    <div class="progress-track"><div class="progress-fill" style="width: {{ $progressPercent }}%;"></div></div>
                </div>
                <div class="action-card-footer">
                    @php
                        $isExpired = $quest->due_date && \Carbon\Carbon::parse($quest->due_date)->isPast();
                    @endphp

                    @if($status === 'completed')
                        <p>Quest Conquered! You are a true Hero of Neural Realm.</p>
                        <button class="btn-primary-action completed" disabled>Completed <i class="fas fa-check-circle"></i></button>
                    @elseif($isExpired)
                        <p class="text-secondary" style="font-size: 0.8rem; margin-bottom: 10px;"><strong>Deadline Passed:</strong> This mission is no longer available.</p>
                        <button class="btn-primary-action expired" disabled style="background: #94a3b8; opacity: 0.7;">Expired <i class="fas fa-clock"></i></button>
                    @elseif($status === 'started')
                        <p>Current Step: {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}</p>
                        <a href="{{ route('student.quest.play', $quest->id) }}" class="btn-primary-action">Continue Quest <i class="fas fa-play"></i></a>
                    @else
                        <p>Your journey awaits! Begin the mission to earn rewards.</p>
                        <form action="{{ route('student.quest.start', $quest->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary-action">Begin Mission <i class="fas fa-swords"></i></button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- LEVEL DETAILS MODAL -->
        <div id="levelDetailsModal" class="modal-overlay" style="display: none;">
            <div class="modal-box level-details-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-scroll"></i> Level <span id="modalLevel">1</span> Details</h3>
                    <button onclick="closeLevelModal()" class="btn-close-modal"><i class="fas fa-times"></i></button>
                </div>
                <div id="modalQuestionsList" class="modal-questions-list">
                    {{-- Questions will be injected here --}}
                </div>
                <div class="modal-footer">
                    <button onclick="closeLevelModal()" class="btn-ok">Back to Map</button>
                </div>
            </div>
        </div>

        <script>
        function showLevelDetails(level, questions) {
            document.getElementById('modalLevel').textContent = level;
            const list = document.getElementById('modalQuestionsList');
            if (list) {
                list.innerHTML = '';
                questions.forEach((q, i) => {
                    const card = document.createElement('div');
                    card.className = 'modal-question-card';
                    card.innerHTML = `
                        <div class="modal-q-header">
                            <span class="q-type-badge">${q.type.replace('-', ' ')}</span>
                            <span class="q-points-badge">${q.points} PTS</span>
                        </div>
                        <p class="q-text">${q.question}</p>
                    `;
                    list.appendChild(card);
                });
            }
            document.getElementById('levelDetailsModal').style.display = 'flex';
        }

        function closeLevelModal() {
            const modal = document.getElementById('levelDetailsModal');
            if (modal) modal.style.display = 'none';
        }
        </script>
    </div>

    <!-- Side Content (Questions/Steps) -->
    <div class="quest-steps-panel">
        <div class="panel-header">
            <h3>Quest Objectives</h3>
            <p>Complete all tasks to claim your rewards.</p>
        </div>
        <div class="steps-scroll">
            @foreach($orderedQuestions as $index => $step)
            @php
                // Step is completed if: quest is completed OR current question index > this step's index
                $isStepCompleted = ($status === 'completed') || ($currentQuestionIndex !== false && $currentQuestionIndex > $index);
                $isStepCurrent = $attempt && $status === 'started' && $attempt->current_question_id == $step->id;
                $isStepLocked = !$isStepCompleted && !$isStepCurrent;
            @endphp
            <div class="step-card {{ $isStepCompleted ? 'completed' : ($isStepCurrent ? 'current' : 'locked') }}">
                <div class="step-status">
                    @if($isStepCompleted) <i class="fas fa-check"></i>
                    @elseif($isStepCurrent) <i class="fas fa-play"></i>
                    @else <i class="fas fa-lock"></i> @endif
                </div>
                <div class="step-details">
                    <span class="step-label">Step {{ $index + 1 }} (LVL {{ $step->level }})</span>
                    <h4>Objective {{ $index + 1 }}</h4>
                    <p>{{ Str::limit($step->question, 50) }}</p>
                </div>
                <div class="step-pts">{{ $step->points }} XP</div>
            </div>
            @endforeach
        </div>
    </div>
</div>


<style>
    .btn-primary-action.completed {
        background: #10b981;
        cursor: default;
        opacity: 0.9;
    }
    .step-card.completed { background: #ecfdf5; border-color: #10b981; }
    .step-card.completed .step-status { color: #10b981; }
</style>

<style>
    .quest-view-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
        padding: 20px;
    }

    .quest-view-header {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn-back {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.2s;
    }

    .btn-back:hover { color: var(--primary); }

    .quest-title-shield h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .quest-meta-row {
        display: flex;
        gap: 12px;
    }

    .meta-badge {
        background: white;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--secondary);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* MAP Exploration AREA */
    .map-exploration-area {
        position: relative;
    }

    .map-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 1000 / 600;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        border: 2px solid rgba(255,255,255,0.1);
    }

    .map-background {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .map-svg-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 5;
    }

    .interactive-landmarks {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
    }

    .landmark-node {
        position: absolute;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .landmark-node:hover { transform: translate(-50%, -50%) scale(1.1); }

    .node-icon {
        width: 50px;
        height: 50px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: var(--text-muted);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        border: 3px solid #eee;
    }

    .node-icon.active {
        background: var(--accent);
        color: var(--primary);
        border-color: #fff;
        box-shadow: 0 0 25px rgba(255,212,59,0.6);
        animation: pulse-ring 2s infinite;
    }

    .node-icon.locked { background: #cbd5e1; color: #94a3b8; border-color: #f1f5f9; box-shadow: none; }
    .node-icon.finish { background: #1e293b; color: #fbbf24; border-color: #334155; }

    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(255, 212, 59, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0); }
    }

    .node-tag {
        margin-top: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ACTION CARD */
    .map-action-card {
        position: absolute;
        bottom: 30px;
        right: 30px;
        width: 300px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        z-index: 100;
        border: 1px solid rgba(255,255,255,0.5);
    }

    .action-card-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .action-card-header h4 { font-size: 1rem; color: var(--primary); font-weight: 800; }
    .progress-percent { font-size: 0.9rem; font-weight: 800; color: var(--accent-dark); }

    .action-card-progress { height: 8px; background: #eee; border-radius: 4px; overflow: hidden; margin-bottom: 15px; }
    .progress-fill { height: 100%; background: var(--accent); border-radius: 4px; }

    .action-card-footer p { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 20px; font-weight: 600; }

    .btn-primary-action {
        width: 100%;
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .btn-primary-action:hover { transform: translateY(-2px); background: #1e293b; }

    /* STEPS PANEL */
    .quest-steps-panel {
        background: #fff;
        border-radius: 30px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }

    .panel-header { margin-bottom: 25px; }
    .panel-header h3 { font-size: 1.2rem; color: var(--primary); font-weight: 800; }
    .panel-header p { font-size: 0.85rem; color: var(--text-muted); }

    .steps-scroll {
        display: flex;
        flex-direction: column;
        gap: 15px;
        overflow-y: auto;
        max-height: 500px;
        padding-right: 5px;
    }

    .step-card {
        padding: 15px;
        background: #f8fafc;
        border-radius: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .step-card.current { background: #fffbeb; border-color: var(--accent); }
    .step-card.locked { opacity: 0.6; }

    .step-status {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .current .step-status { color: var(--accent-dark); background: white; }

    .step-details { flex: 1; }
    .step-label { font-size: 0.7rem; font-weight: 800; color: var(--accent-dark); text-transform: uppercase; }
    .step-details h4 { font-size: 0.95rem; color: var(--primary); font-weight: 700; margin: 2px 0; }
    .step-details p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.3; }

    .step-pts { font-size: 0.75rem; font-weight: 800; color: #4f46e5; }

    /* MODAL STYLES */
    .modal-overlay {
        position: fixed;
        inset: 0;
       
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease-out;
    }

    .level-details-modal {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
        border: 1px solid rgba(255, 212, 59, 0.2);
        box-shadow: 0 0 50px rgba(0,0,0,0.8);
        border-radius: 20px;
        padding: 30px;
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
        gap: 15px;
    }

    .modal-question-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 15px;
    }

    .modal-q-header {
        display: flex;
        gap: 10px;
        margin-bottom: 8px;
    }

    .q-type-badge {
        font-size: 0.65rem;
        font-weight: 800;
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        padding: 3px 8px;
        border-radius: 5px;
        text-transform: uppercase;
    }

    .q-points-badge {
        font-size: 0.65rem;
        font-weight: 800;
        background: rgba(255, 212, 59, 0.2);
        color: var(--accent);
        padding: 3px 8px;
        border-radius: 5px;
        text-transform: uppercase;
    }

    .q-text {
        font-size: 1rem;
        color: #e2e8f0;
        line-height: 1.5;
    }

    .modal-footer {
        margin-top: 25px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-ok {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 10px 22px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
        transition: 0.2s;
    }

    @media (max-width: 768px) {
        .quest-view-header { flex-direction: column; align-items: flex-start; }
        .interactive-landmarks { transform: scale(0.6); transform-origin: top left; width: 166%; }
        .map-action-card { width: 100%; position: static; margin-top: 20px; }
        .level-details-modal { padding: 20px; }
    }

    @media (max-width: 1200px) {
        .quest-view-container { grid-template-columns: 1fr; }
    }
</style>
@endsection
