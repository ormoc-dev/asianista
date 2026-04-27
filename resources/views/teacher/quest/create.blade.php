@extends('teacher.layouts.app')

@section('title', isset($quest) ? 'Edit Quest' : 'Create Quest')
@section('page-title', isset($quest) ? 'Edit Quest' : 'Create Quest')

@push('styles')
<style>
    .step-indicator {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }
    .step {
        flex: 1;
        padding: 12px 16px;
        background: var(--bg-main);
        border-radius: var(--radius-sm);
        text-align: center;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-secondary);
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .step.active {
        background: #eef2ff;
        color: var(--primary);
        border-color: var(--primary);
    }
    .step-number {
        display: inline-flex;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--border);
        color: var(--text-secondary);
        font-size: 0.8rem;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
    }
    .step.active .step-number {
        background: var(--primary);
        color: #fff;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .reward-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .reward-card {
        background: var(--bg-main);
        padding: 20px;
        border-radius: var(--radius-sm);
        text-align: center;
    }
    .reward-card i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    .question-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .question-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: var(--bg-main);
        border-radius: var(--radius-sm);
        border-left: 4px solid var(--primary);
    }
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
    }
    .map-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
        margin-top: 8px;
    }
    .map-option {
        position: relative;
        aspect-ratio: 16/10;
        border-radius: var(--radius-sm);
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.2s;
        background: var(--bg-main);
    }
    .map-option:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-lg);
    }
    .map-option.selected {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .map-option img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .map-option .map-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 8px 10px;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
    }
    .map-option .check-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: var(--primary);
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
    }
    .map-option.selected .check-icon {
        display: flex;
    }
    .map-upload-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--bg-main);
        border: 2px dashed var(--border);
        color: var(--text-secondary);
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .map-upload-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .map-upload-btn i {
        font-size: 1.5rem;
    }
    #mapUploadInput {
        display: none;
    }

    #questSubmitBtn.is-loading {
        pointer-events: none;
        opacity: 0.9;
        cursor: wait;
    }
    #questSubmitBtn.is-loading .submit-idle {
        display: none;
    }
    #questSubmitBtn.is-loading .submit-loading {
        display: inline-flex !important;
        align-items: center;
        gap: 10px;
    }
    #questSubmitBtn .submit-loading {
        display: none;
    }

    /* Custom level positions editor */
    .teacher-map-pins-editor {
        margin-top: 16px;
        padding: 20px 22px;
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        max-width: 1100px;
    }
    .teacher-map-pins-hint {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin: 0 0 14px;
        padding: 12px 14px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--border);
    }
    .teacher-map-pins-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
    }
    .teacher-map-pins-edit-toggle.is-active {
        background: #eef2ff;
        border-color: var(--primary);
        color: var(--primary);
        font-weight: 600;
    }
    .teacher-map-pins-map-help {
        font-size: 0.82rem;
        line-height: 1.55;
        color: var(--text-secondary);
        margin: 0 0 12px;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 10px;
        border-left: 4px solid var(--primary);
    }
    .teacher-map-pins-map-help strong {
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 0.88rem;
    }
    .teacher-map-pins-map-help ul {
        margin: 0;
        padding-left: 1.15rem;
    }
    .teacher-map-pins-map-help li {
        margin-bottom: 4px;
    }
    .teacher-map-pins-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
    }
    .teacher-map-pins-grid.teacher-map-pins-grid--with-sidebar {
        grid-template-columns: minmax(0, 1fr) minmax(260px, 360px);
    }
    .teacher-map-pins-grid:not(.teacher-map-pins-grid--with-sidebar) .teacher-map-pins-list-col {
        display: none;
    }
    .teacher-map-pins-grid:not(.teacher-map-pins-grid--with-sidebar) .teacher-map-pins-map-frame {
        max-width: none;
    }
    .teacher-map-pins-grid > * {
        min-width: 0;
    }
    .teacher-map-pins-map-caption {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin: 0 0 8px;
    }
    .teacher-map-pins-map-frame {
        position: relative;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--border);
        aspect-ratio: 1000 / 600;
        max-width: 640px;
        background: #e2e8f0;
    }
    .teacher-map-pins-map-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        cursor: crosshair;
        vertical-align: top;
    }
    .teacher-map-pin-markers-layer {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }
    .teacher-map-pin-marker {
        position: absolute;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #4f46e5;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translate(-50%, -50%);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        cursor: grab;
        touch-action: none;
        pointer-events: auto;
        user-select: none;
        -webkit-user-select: none;
        z-index: 2;
    }
    .teacher-map-pin-marker.is-dragging {
        cursor: grabbing;
        z-index: 25;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        transform: translate(-50%, -50%) scale(1.08);
    }
    .teacher-map-pins-list-title {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        margin: 0 0 10px;
    }
    .teacher-map-pins-scroll {
        max-height: min(52vh, 520px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 10px 8px 10px 6px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
    }
    .teacher-pin-rows {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .teacher-pin-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .teacher-pin-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .teacher-pin-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        border-radius: 8px;
        background: #eef2ff;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.9rem;
    }
    .teacher-pin-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-end;
    }
    .teacher-pin-actions .btn {
        min-width: 36px;
    }
    .teacher-pin-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .teacher-pin-fields .teacher-pin-field-wide {
        grid-column: 1 / -1;
    }
    .teacher-pin-field-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .teacher-pin-field-input {
        width: 100%;
        box-sizing: border-box;
        min-height: 42px;
        padding: 10px 12px;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.3;
        color: var(--text-primary) !important;
        background: #fff !important;
        border: 1px solid var(--border);
        border-radius: 8px;
        -webkit-appearance: none;
        appearance: none;
    }
    .teacher-pin-field-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }
    .teacher-pin-empty {
        padding: 22px 16px;
        text-align: center;
        font-size: 0.88rem;
        line-height: 1.55;
        color: var(--text-secondary);
        margin: 0;
        border: 1px dashed var(--border);
        border-radius: 10px;
        background: #fafafa;
    }
    @media (max-width: 900px) {
        .teacher-map-pins-grid.teacher-map-pins-grid--with-sidebar {
            grid-template-columns: 1fr;
        }
        .teacher-map-pins-map-frame {
            max-width: none;
        }
        .teacher-map-pins-scroll {
            max-height: none;
        }
    }

    @include('teacher.partials._quest_ai_model_picker_css')
</style>
@endpush

@section('content')
<form id="questForm" method="POST" action="{{ isset($quest) ? route('teacher.quest.update', $quest) : route('teacher.quest.store') }}" novalidate>
    @csrf
    @if(isset($quest))
        @method('PUT')
    @endif
    
    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" data-step="1">
            <span class="step-number">1</span>Details
        </div>
        <div class="step" data-step="2">
            <span class="step-number">2</span>Challenges
        </div>
        <div class="step" data-step="3">
            <span class="step-number">3</span>Target
        </div>
    </div>

    <!-- Step 1: Quest Details -->
    <div class="step-content active" id="step1">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quest Information</h2>
                <button type="button" class="btn btn-secondary" onclick="openAiModal()">
                    <i class="fas fa-magic"></i> Generate with AI
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Quest Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., The Algebra Adventure" value="{{ old('title', $quest->title ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the quest adventure..." required>{{ old('description', $quest->description ?? '') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-control">
                            @php $diff = old('difficulty', $quest->difficulty ?? 'medium'); @endphp
                            <option value="easy" @selected($diff === 'easy')>Easy</option>
                            <option value="medium" @selected($diff === 'medium')>Medium</option>
                            <option value="hard" @selected($diff === 'hard')>Hard</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Levels</label>
                        <input type="number" name="level" id="questTotalLevelsInput" class="form-control" value="{{ old('level', $quest->level ?? 3) }}" min="1" max="30">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-map"></i> Quest Map</label>
                    @php
                        $mapHidden = old('map_image');
                        if ($mapHidden === null) {
                            $mapHidden = (isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png')
                                ? 'existing:' . $quest->map_image
                                : 'default';
                        }
                    @endphp
                    <input type="hidden" name="map_image" id="mapImage" value="{{ $mapHidden }}">
                    <div class="map-selector" id="mapSelector">
                        <!-- Default Maps -->
                        <div class="map-option {{ (isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png') ? '' : 'selected' }}" data-map="default" onclick="selectMap(this, 'default')">
                            <img src="{{ asset('images/quest_map_bg.png') }}" alt="Default Map">
                            <span class="map-label">Default Map</span>
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                        </div>
                        @if(isset($quest) && $quest->map_image && $quest->map_image !== 'quest_map_bg.png')
                        <div class="map-option selected" data-map="existing" data-existing-path="{{ $quest->map_image }}" onclick="selectMapExisting(this)">
                            <img src="{{ asset('storage/' . $quest->map_image) }}" alt="Current Map">
                            <span class="map-label">Current Map</span>
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                        </div>
                        @endif
                        
                        <!-- Upload Button -->
                        <div class="map-option map-upload-btn" onclick="document.getElementById('mapUploadInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Upload Map</span>
                        </div>
                    </div>
                    <input type="file" id="mapUploadInput" accept="image/*" onchange="handleMapUpload(this)">
                    <small class="text-muted" style="display: block; margin-top: 8px; color: var(--text-muted);">
                        <i class="fas fa-info-circle"></i> Select a default map or upload your own (JPG, PNG, WebP, GIF — max 5&nbsp;MB)
                    </small>
                </div>

                @php
                    $hasCustomMapPins = isset($quest) && is_array($quest->map_pins ?? null) && count($quest->map_pins) > 0;
                @endphp
                <div class="form-group" id="teacherMapPinsSection" style="margin-top: 20px;">
                    <label class="form-label"><i class="fas fa-location-dot"></i> Level positions on map (optional)</label>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">
                        Leave off to use the <strong>school default path</strong> for this map (set in Admin → Quest Maps).
                        Turn on to place your own dots for 3, 5, 10, or more levels — click the map in order (Level 1, 2, 3…).
                    </p>
                    <input type="hidden" name="use_custom_map_pins" value="0">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; margin-bottom: 12px;">
                        <input type="checkbox" name="use_custom_map_pins" id="useCustomMapPins" value="1"
                            {{ (string) old('use_custom_map_pins', $hasCustomMapPins ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <span>Use custom positions for this quest</span>
                    </label>
                    <input type="hidden" name="map_pins_json" id="mapPinsJson" value="">
                    <div id="teacherMapPinsEditor" class="teacher-map-pins-editor" style="display: none;">
                        <p id="mapPinLevelHint" class="teacher-map-pins-hint"></p>
                        <div class="teacher-map-pins-toolbar">
                            <button type="button" class="btn btn-sm btn-secondary" id="btnTeacherPinsSchoolDefault"><i class="fas fa-school"></i> Load school default path</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btnTeacherPinsClear"><i class="fas fa-eraser"></i> Clear points</button>
                            <button type="button" class="btn btn-sm btn-secondary teacher-map-pins-edit-toggle" id="btnTeacherPinsToggleEdit" aria-expanded="false" aria-controls="teacherMapPinsListCol">
                                <i class="fas fa-sliders-h"></i> Edit points
                            </button>
                        </div>
                        <div class="teacher-map-pins-grid" id="teacherMapPinsGrid">
                            <div class="teacher-map-pins-map-col">
                                <div class="teacher-map-pins-map-help" role="note">
                                    <strong><i class="fas fa-map-location-dot"></i> How to place levels</strong>
                                    <ul>
                                        <li><strong>Click</strong> the map in order — first click = Level 1, next = Level 2, and so on (it cycles if you keep clicking).</li>
                                        <li><strong>Drag</strong> the numbered dots to move a level; positions update automatically.</li>
                                        <li>Open <strong>Edit points</strong> (beside Clear) for exact Left % / Top % and labels.</li>
                                    </ul>
                                </div>
                                <p class="teacher-map-pins-map-caption" id="teacherMapPinPlacementHint"><i class="fas fa-crosshairs"></i> Click the map to set each level’s position in order.</p>
                                <div class="teacher-map-pins-map-frame">
                                    <img src="{{ asset('images/quest_map_bg.png') }}" alt="" id="teacherMapPinPreview">
                                    <div id="teacherMapPinMarkers" class="teacher-map-pin-markers-layer" aria-hidden="true"></div>
                                </div>
                            </div>
                            <div class="teacher-map-pins-list-col" id="teacherMapPinsListCol">
                                <p class="teacher-map-pins-list-title">Edit points</p>
                                <div class="teacher-map-pins-scroll">
                                    <div id="teacherMapPinTableBody" class="teacher-pin-rows"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <label class="form-label">Rewards</label>
                <div class="reward-grid">
                    <div class="reward-card">
                        <i class="fas fa-star" style="color: var(--primary);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">XP Reward</label>
                        <input type="number" name="xp_reward" class="form-control" value="{{ old('xp_reward', $quest->xp_reward ?? 100) }}">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">AP Reward</label>
                        <input type="number" name="ab_reward" class="form-control" value="{{ old('ab_reward', $quest->ab_reward ?? 50) }}">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-coins" style="color: var(--accent);"></i>
                        <label class="form-label" style="font-size: 0.8rem;">GP Reward</label>
                        <input type="number" name="gp_reward" class="form-control" value="{{ old('gp_reward', $quest->gp_reward ?? 25) }}">
                    </div>
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Assign Date</label>
                        <input type="datetime-local" name="assign_date" class="form-control" value="{{ old('assign_date', isset($quest) ? \Carbon\Carbon::parse($quest->assign_date)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date', isset($quest) ? \Carbon\Carbon::parse($quest->due_date)->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <label class="form-label" style="margin-top: 20px;">Game Settings</label>
                <div class="reward-grid">
                    <div class="reward-card">
                        <i class="fas fa-clock" style="color: #3b82f6;"></i>
                        <label class="form-label" style="font-size: 0.8rem;">Time Limit (minutes)</label>
                        <input type="number" name="time_limit_minutes" class="form-control" value="{{ old('time_limit_minutes', $quest->time_limit_minutes ?? 10) }}" min="1" placeholder="Minutes per level">
                    </div>
                    <div class="reward-card">
                        <i class="fas fa-heart-broken" style="color: #ef4444;"></i>
                        <label class="form-label" style="font-size: 0.8rem;">HP Penalty</label>
                        <input type="number" name="hp_penalty" class="form-control" value="{{ old('hp_penalty', $quest->hp_penalty ?? 10) }}" min="0" placeholder="HP lost per wrong answer">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 18px;">
                    <input type="hidden" name="require_fullscreen" value="0">
                    <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600;">
                        <input type="checkbox" name="require_fullscreen" value="1"
                            {{ (string) old('require_fullscreen', isset($quest) ? (int) $quest->require_fullscreen : '1') === '1' ? 'checked' : '' }}>
                        <span><i class="fas fa-expand-arrows-alt"></i> Require fullscreen in student quest play</span>
                    </label>
                    <small class="text-muted" style="display: block; margin-top: 8px; color: var(--text-muted);">
                        If off, students can continue playing without entering browser fullscreen.
                    </small>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                Next: Add Challenges <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Challenges -->
    <div class="step-content" id="step2">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quest Challenges</h2>
                <button type="button" class="btn btn-secondary" onclick="openAiQuestionModal()">
                    <i class="fas fa-magic"></i> AI Forge
                </button>
            </div>
            <div class="card-body">
                <div id="questionsContainer" class="question-list">
                    <!-- Questions will be added here -->
                </div>

                <div style="background: var(--bg-main); padding: 20px; border-radius: var(--radius-sm); margin-top: 20px;">
                    <h3 style="font-size: 1rem; margin-bottom: 16px;"><i class="fas fa-plus"></i> Add Question</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Question Text</label>
                        <textarea id="questionText" class="form-control" rows="2" placeholder="Enter your question..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select id="questionType" class="form-control" onchange="toggleOptions()">
                                <option value="">Select Type</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="identification">Identification</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Level</label>
                            <select id="questionLevel" class="form-control">
                                <!-- Options will be populated dynamically based on total levels -->
                            </select>
                        </div>
                    </div>

                    <div id="optionsContainer" style="display: none;">
                        <label class="form-label">Options</label>
                        <div id="optionsList"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addOption()" style="margin-top: 8px;">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correct Answer</label>
                        <input type="text" id="correctAnswer" class="form-control" placeholder="Enter the correct answer">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Points</label>
                        <input type="number" id="questionPoints" class="form-control" value="10" style="width: 150px;">
                    </div>

                    <button type="button" class="btn btn-primary" onclick="addQuestion()">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                Next: Target Party <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Target -->
    <div class="step-content" id="step3">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Target Party</h2>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-graduation-cap"></i> Grade</label>
                        <select name="grade_id" id="gradeSelect" class="form-control" onchange="loadSections()" required>
                            <option value="">Select Grade</option>
                            @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', isset($quest) ? $quest->grade_id : null) == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-users"></i> Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>

                <div style="background: #fef3c7; padding: 16px; border-radius: var(--radius-sm); margin-top: 20px; color: #92400e;">
                    <i class="fas fa-lightbulb"></i>
                    You can reuse this quest later by assigning it to different sections or grades.
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="submit" id="questSubmitBtn" class="btn btn-success btn-lg" aria-busy="false">
                <span class="submit-idle"><i class="fas fa-check"></i> {{ isset($quest) ? 'Update Quest' : 'Create Quest' }}</span>
                <span class="submit-loading" aria-hidden="true"><i class="fas fa-spinner fa-spin"></i> Saving…</span>
            </button>
        </div>
    </div>
</form>

@php
    $questAiModels = config('services.quest_ai.models', []);
    $questAiDefault = config('services.quest_ai.default');
@endphp

<!-- AI Modal -->
<div id="aiModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-magic"></i> AI Quest Generator</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-robot"></i> AI model</label>
                @include('teacher.quest._ai_model_picker', [
                    'hiddenId' => 'aiQuestModelSelect',
                    'questAiModels' => $questAiModels,
                    'questAiDefault' => $questAiDefault,
                    'title' => 'Choose which model generates this quest',
                ])
                <small class="text-muted" style="display: block; margin-top: 6px; color: var(--text-muted);">OpenRouter models need <code style="font-size: 0.85em;">OPENROUTER_API_KEY</code> in <code style="font-size: 0.85em;">.env</code>.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Topic</label>
                <textarea id="aiTopic" class="form-control" rows="3" placeholder="e.g., Introduction to Algebra, Photosynthesis..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="closeAiModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateWithAI()">
                    <i class="fas fa-sparkles"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Question Forge Modal -->
<div id="aiQuestionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 20px;">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-magic"></i> AI Question Forge</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-robot"></i> AI model</label>
                @include('teacher.quest._ai_model_picker', [
                    'hiddenId' => 'aiQuestionModelSelect',
                    'questAiModels' => $questAiModels,
                    'questAiDefault' => $questAiDefault,
                    'title' => 'Choose which model generates this question',
                ])
            </div>
            <div class="form-group">
                <label class="form-label">Topic/Subject</label>
                <input type="text" id="aiQuestionTopic" class="form-control" placeholder="e.g., Algebra, History, Science...">
            </div>
            <div class="form-group">
                <label class="form-label">Question Type</label>
                <select id="aiQuestionType" class="form-control">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="identification">Identification</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Target Level</label>
                <select id="aiQuestionLevel" class="form-control">
                    <!-- Populated dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Difficulty</label>
                <select id="aiQuestionDifficulty" class="form-control">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="closeAiQuestionModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateQuestionWithAI()">
                    <i class="fas fa-sparkles"></i> Generate Question
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    $questInitialQuestions = isset($quest)
        ? $quest->questions->map(fn ($q) => [
            'id' => $q->id,
            'text' => $q->question,
            'type' => $q->type,
            'level' => (int) $q->level,
            'answer' => $q->answer,
            'points' => (int) $q->points,
            'options' => $q->options,
        ])->values()->all()
        : [];
    $questIsEdit = isset($quest);
    $questInitialSectionId = $questIsEdit ? old('section_id', $quest->section_id) : null;
    $questScriptBootstrap = [
        'questions' => $questInitialQuestions,
        'isEdit' => $questIsEdit,
        'initialSectionId' => $questInitialSectionId,
        'questAiDefault' => $questAiDefault,
        'defaultMapAssetUrl' => asset('images/quest_map_bg.png'),
        'storageUrlPrefix' => rtrim(asset('storage'), '/'),
        'mapPinEditor' => [
            'initialPins' => ($questIsEdit && isset($quest) && is_array($quest->map_pins)) ? array_values($quest->map_pins) : [],
            'schoolDefaultPins' => \App\Models\QuestMapLayout::basePinsForImage(null),
        ],
    ];
@endphp
<script type="application/json" id="quest-bootstrap-data">
{!! json_encode($questScriptBootstrap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
const QUEST_AI_STORAGE_KEY = 'asianista_teacher_quest_ai_model';
const __questBoot = JSON.parse(document.getElementById('quest-bootstrap-data').textContent);
const __questAiDefault = __questBoot.questAiDefault;
const __defaultMapAssetUrl = __questBoot.defaultMapAssetUrl;
const __storageUrlPrefix = __questBoot.storageUrlPrefix;
let questions = __questBoot.questions;
const questIsEdit = __questBoot.isEdit;
const questInitialSectionId = __questBoot.initialSectionId;
let options = [];
let currentStep = 1;
let uploadedMaps = [];
let teacherMapPins = [];
let teacherMapPinsActiveIndex = 0;
let teacherPinPointerDrag = null;

function getQuestTotalLevels() {
    const v = document.getElementById('questTotalLevelsInput')?.value;
    let n = parseInt(v, 10);
    if (Number.isNaN(n)) n = 3;
    return Math.min(30, Math.max(1, n));
}

function updateQuestAiPickerDisplay(wrap, value) {
    const hidden = wrap.querySelector('.js-quest-ai-model');
    const inner = wrap.querySelector('[data-quest-ai-trigger-inner]');
    const opt = wrap.querySelector('.quest-ai-model-option[data-value="' + value + '"]');
    if (!hidden || !inner || !opt) return;
    hidden.value = value;
    inner.innerHTML = opt.innerHTML;
}

function setGlobalQuestAiModel(value, skipStorage) {
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        updateQuestAiPickerDisplay(wrap, value);
    });
    if (!skipStorage) {
        localStorage.setItem(QUEST_AI_STORAGE_KEY, value);
    }
}

function closeAllQuestAiPickers() {
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        const menu = wrap.querySelector('[data-quest-ai-menu]');
        const trigger = wrap.querySelector('[data-quest-ai-trigger]');
        if (menu) menu.hidden = true;
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        wrap.classList.remove('is-open');
    });
}

function syncQuestAiModelSelects() {
    const first = document.querySelector('[data-quest-ai-picker]');
    if (!first) return;
    let v = localStorage.getItem(QUEST_AI_STORAGE_KEY) || __questAiDefault;
    const valid = Array.from(first.querySelectorAll('.quest-ai-model-option')).map(function (li) {
        return li.getAttribute('data-value');
    });
    if (valid.indexOf(v) === -1) v = __questAiDefault;
    if (valid.indexOf(v) === -1 && valid.length) v = valid[0];
    setGlobalQuestAiModel(v, true);
}

function initQuestAiModelSelects() {
    syncQuestAiModelSelects();
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        const trigger = wrap.querySelector('[data-quest-ai-trigger]');
        const menu = wrap.querySelector('[data-quest-ai-menu]');
        if (!trigger || !menu) return;
        wrap.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        trigger.addEventListener('click', function () {
            const willOpen = menu.hidden;
            closeAllQuestAiPickers();
            if (willOpen) {
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                wrap.classList.add('is-open');
            }
        });
        menu.querySelectorAll('.quest-ai-model-option').forEach(function (li) {
            li.addEventListener('click', function () {
                const v = li.getAttribute('data-value');
                setGlobalQuestAiModel(v, false);
                closeAllQuestAiPickers();
            });
        });
    });
    document.addEventListener('click', function () {
        closeAllQuestAiPickers();
    });
}

function getSelectedQuestAiModel() {
    const el = document.querySelector('.js-quest-ai-model');
    return el ? el.value : __questAiDefault;
}

function expandBasePinsToLevelsFromSchoolBase(base, levelCount) {
    levelCount = Math.max(1, Math.min(30, levelCount));
    base = (base && base.length) ? base.map(normalizeTeacherPin) : [{ left: 50, top: 50, name: '', icon: 'fa-map-marker-alt' }];
    const out = [];
    const n = base.length;
    for (let i = 1; i <= levelCount; i++) {
        const idx = i - 1;
        if (idx < n) {
            const raw = Object.assign({}, base[idx]);
            if (!raw.name) raw.name = 'Level ' + i;
            out.push(normalizeTeacherPin(raw));
            continue;
        }
        const pLast = base[n - 1];
        const pPrev = n >= 2 ? base[n - 2] : base[0];
        const steps = idx - n + 1;
        const dl = (pLast.left - pPrev.left) * 0.12 * steps;
        const dt = (pLast.top - pPrev.top) * 0.12 * steps;
        out.push(normalizeTeacherPin({
            left: Math.min(95, Math.max(5, pLast.left + dl)),
            top: Math.min(95, Math.max(5, pLast.top + dt)),
            name: 'Level ' + i,
            icon: pLast.icon || 'fa-map-marker-alt'
        }));
    }
    return out;
}

function extrapolateMorePins(currentPins, targetCount) {
    targetCount = Math.max(1, Math.min(30, targetCount));
    let base = currentPins.map(normalizeTeacherPin);
    if (base.length === 0) {
        const sd = (__questBoot.mapPinEditor?.schoolDefaultPins || []).map(normalizeTeacherPin);
        return expandBasePinsToLevelsFromSchoolBase(sd, targetCount);
    }
    if (targetCount <= base.length) {
        return base.slice(0, targetCount).map((p, i) => normalizeTeacherPin(Object.assign({}, p, {
            name: p.name || ('Level ' + (i + 1))
        })));
    }
    const n = base.length;
    const pLast0 = base[n - 1];
    const pPrev0 = n >= 2 ? base[n - 2] : base[0];
    const out = base.slice();
    for (let idx = n; idx < targetCount; idx++) {
        const steps = idx - n + 1;
        const dl = (pLast0.left - pPrev0.left) * 0.12 * steps;
        const dt = (pLast0.top - pPrev0.top) * 0.12 * steps;
        out.push(normalizeTeacherPin({
            left: Math.min(95, Math.max(5, pLast0.left + dl)),
            top: Math.min(95, Math.max(5, pLast0.top + dt)),
            name: 'Level ' + (idx + 1),
            icon: pLast0.icon || 'fa-map-marker-alt'
        }));
    }
    return out;
}

function syncTeacherPinsToLevelCount() {
    if (!document.getElementById('useCustomMapPins')?.checked) return;
    const n = getQuestTotalLevels();
    if (teacherMapPins.length > n) {
        teacherMapPins = teacherMapPins.slice(0, n);
    } else if (teacherMapPins.length < n) {
        teacherMapPins = extrapolateMorePins(teacherMapPins, n);
    } else {
        teacherMapPins = teacherMapPins.map((p, i) => normalizeTeacherPin(Object.assign({}, p, {
            name: p.name || ('Level ' + (i + 1))
        })));
    }
    if (teacherMapPinsActiveIndex >= teacherMapPins.length) {
        teacherMapPinsActiveIndex = 0;
    }
    updateTeacherMapPinPlacementHint();
}

function normalizeTeacherPin(p) {
    return {
        left: Math.round((Number(p.left) || 0) * 100) / 100,
        top: Math.round((Number(p.top) || 0) * 100) / 100,
        name: (p.name != null && String(p.name).trim() !== '') ? String(p.name).trim() : '',
        icon: (p.icon != null && String(p.icon).trim() !== '') ? String(p.icon).trim() : 'fa-map-marker-alt',
    };
}

function getTeacherMapPreviewSrc() {
    const v = document.getElementById('mapImage').value;
    if (!v || v === 'default') return __defaultMapAssetUrl;
    if (v.startsWith('existing:')) {
        const p = v.slice('existing:'.length).replace(/^\/+/, '');
        return __storageUrlPrefix + '/' + p;
    }
    if (v.startsWith('data:image')) return v;
    const found = uploadedMaps.find(m => m.id === v);
    if (found && found.data) return found.data;
    return __defaultMapAssetUrl;
}

function refreshTeacherMapPreview() {
    const img = document.getElementById('teacherMapPinPreview');
    if (!img) return;
    img.src = getTeacherMapPreviewSrc();
}

function updateTeacherMapPinHint() {
    const el = document.getElementById('mapPinLevelHint');
    if (!el) return;
    const n = getQuestTotalLevels();
    const p = teacherMapPins.length;
    el.innerHTML = `<strong>Total levels:</strong> ${n} — the editor keeps <strong>${p}</strong> point(s), <strong>one per level</strong> (updates when you change Total levels).<br><span style="font-weight:500;opacity:.95"><strong>Drag</strong> a numbered dot on the map, or <strong>click</strong> the map to place levels in order (cycles). Left % / Top % stay in sync.</span>`;
}

function updateTeacherMapPinPlacementHint() {
    const el = document.getElementById('teacherMapPinPlacementHint');
    if (!el) return;
    if (!document.getElementById('useCustomMapPins')?.checked) return;
    const n = getQuestTotalLevels();
    if (n < 1 || !teacherMapPins.length) {
        el.innerHTML = '<i class="fas fa-crosshairs"></i> Set <strong>Total levels</strong> above, then click the map to place each level.';
        return;
    }
    const i = teacherMapPinsActiveIndex % n;
    const nextNum = i + 1;
    el.innerHTML = `<i class="fas fa-crosshairs"></i> <strong>Click</strong> the map to set <strong>Level ${nextNum}</strong> of ${n} (cycles), or <strong>drag</strong> a numbered dot. You can also edit Left % / Top %.`;
}

function getTeacherMapPinFrame() {
    return document.querySelector('.teacher-map-pins-map-frame');
}

function teacherMapPinPercentFromClient(clientX, clientY) {
    const frame = getTeacherMapPinFrame();
    if (!frame) return { left: 0, top: 0 };
    const r = frame.getBoundingClientRect();
    if (r.width <= 0 || r.height <= 0) return { left: 0, top: 0 };
    const x = ((clientX - r.left) / r.width) * 100;
    const y = ((clientY - r.top) / r.height) * 100;
    return {
        left: Math.round(Math.min(100, Math.max(0, x)) * 100) / 100,
        top: Math.round(Math.min(100, Math.max(0, y)) * 100) / 100
    };
}

function syncTeacherPinFormInputsFromIndex(i) {
    if (!teacherMapPins[i]) return;
    const leftInp = document.querySelector('#teacherMapPinTableBody .tpm-left[data-i="' + i + '"]');
    const topInp = document.querySelector('#teacherMapPinTableBody .tpm-top[data-i="' + i + '"]');
    if (leftInp) leftInp.value = teacherMapPins[i].left;
    if (topInp) topInp.value = teacherMapPins[i].top;
}

function onTeacherMapPinMarkerPointerDown(e) {
    if (!document.getElementById('useCustomMapPins')?.checked) return;
    if (e.button !== undefined && e.button !== 0) return;
    const dot = e.currentTarget;
    const idx = parseInt(dot.dataset.pinIndex, 10);
    if (Number.isNaN(idx) || !teacherMapPins[idx]) return;
    teacherPinPointerDrag = { index: idx, pointerId: e.pointerId, dotEl: dot };
    dot.classList.add('is-dragging');
    dot.setPointerCapture(e.pointerId);
    e.preventDefault();
    e.stopPropagation();
}

function onTeacherMapPinMarkerPointerMove(e) {
    if (!teacherPinPointerDrag || teacherPinPointerDrag.pointerId !== e.pointerId) return;
    const { left, top } = teacherMapPinPercentFromClient(e.clientX, e.clientY);
    const idx = teacherPinPointerDrag.index;
    teacherMapPins[idx].left = left;
    teacherMapPins[idx].top = top;
    const dot = teacherPinPointerDrag.dotEl;
    dot.style.left = left + '%';
    dot.style.top = top + '%';
    syncTeacherPinFormInputsFromIndex(idx);
}

function onTeacherMapPinMarkerPointerUp(e) {
    if (!teacherPinPointerDrag) return;
    if (e.pointerId !== undefined && teacherPinPointerDrag.pointerId !== e.pointerId) return;
    const dot = teacherPinPointerDrag.dotEl;
    try {
        dot.releasePointerCapture(teacherPinPointerDrag.pointerId);
    } catch (err) { /* ignore */ }
    dot.classList.remove('is-dragging');
    teacherPinPointerDrag = null;
}

function renderTeacherMapPinMarkers() {
    const wrap = document.getElementById('teacherMapPinMarkers');
    if (!wrap) return;
    wrap.innerHTML = '';
    teacherMapPins.forEach((pin, i) => {
        const dot = document.createElement('div');
        dot.className = 'teacher-map-pin-marker';
        dot.dataset.pinIndex = String(i);
        dot.textContent = String(i + 1);
        dot.setAttribute('role', 'button');
        dot.setAttribute('aria-label', 'Drag to move level ' + (i + 1));
        dot.style.left = pin.left + '%';
        dot.style.top = pin.top + '%';
        dot.addEventListener('pointerdown', onTeacherMapPinMarkerPointerDown);
        dot.addEventListener('pointermove', onTeacherMapPinMarkerPointerMove);
        dot.addEventListener('pointerup', onTeacherMapPinMarkerPointerUp);
        dot.addEventListener('pointercancel', onTeacherMapPinMarkerPointerUp);
        wrap.appendChild(dot);
    });
}

function escapeTeacherAttr(s) {
    return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
}

function renderTeacherMapPinTable() {
    const tbody = document.getElementById('teacherMapPinTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (teacherMapPins.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'teacher-pin-empty';
        empty.innerHTML = 'Set <strong>Total levels</strong> and turn on custom positions, or use <strong>Load school default path</strong>.';
        tbody.appendChild(empty);
        renderTeacherMapPinMarkers();
        updateTeacherMapPinHint();
        updateTeacherMapPinPlacementHint();
        return;
    }
    teacherMapPins.forEach((pin, i) => {
        const card = document.createElement('div');
        card.className = 'teacher-pin-card';
        card.innerHTML = `
            <div class="teacher-pin-card-top">
                <span class="teacher-pin-badge">${i + 1}</span>
                <div class="teacher-pin-actions">
                    <button type="button" class="btn btn-sm btn-secondary tpm-up" title="Move up" data-i="${i}" ${i === 0 ? 'disabled' : ''}><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="btn btn-sm btn-secondary tpm-down" title="Move down" data-i="${i}" ${i === teacherMapPins.length - 1 ? 'disabled' : ''}><i class="fas fa-arrow-down"></i></button>
                </div>
            </div>
            <div class="teacher-pin-fields">
                <div>
                    <label class="teacher-pin-field-label">Left %</label>
                    <input type="number" step="0.1" min="0" max="100" class="teacher-pin-field-input tpm-left" data-i="${i}" value="${pin.left}">
                </div>
                <div>
                    <label class="teacher-pin-field-label">Top %</label>
                    <input type="number" step="0.1" min="0" max="100" class="teacher-pin-field-input tpm-top" data-i="${i}" value="${pin.top}">
                </div>
                <div class="teacher-pin-field-wide">
                    <label class="teacher-pin-field-label">Label</label>
                    <input type="text" class="teacher-pin-field-input tpm-name" data-i="${i}" value="${escapeTeacherAttr(pin.name)}" placeholder="e.g. Level ${i + 1}">
                </div>
            </div>`;
        tbody.appendChild(card);
    });
    tbody.querySelectorAll('.tpm-left, .tpm-top, .tpm-name').forEach(inp => {
        inp.addEventListener('change', onTeacherPinFieldChange);
        inp.addEventListener('input', onTeacherPinFieldChange);
    });
    tbody.querySelectorAll('.tpm-up').forEach(b => b.addEventListener('click', () => moveTeacherPin(Number(b.dataset.i), -1)));
    tbody.querySelectorAll('.tpm-down').forEach(b => b.addEventListener('click', () => moveTeacherPin(Number(b.dataset.i), 1)));
    renderTeacherMapPinMarkers();
    updateTeacherMapPinHint();
    updateTeacherMapPinPlacementHint();
}

function onTeacherPinFieldChange(e) {
    const i = Number(e.target.dataset.i);
    if (Number.isNaN(i) || !teacherMapPins[i]) return;
    const row = e.target.closest('.teacher-pin-card');
    const left = row.querySelector('.tpm-left');
    const top = row.querySelector('.tpm-top');
    const name = row.querySelector('.tpm-name');
    teacherMapPins[i].left = Math.min(100, Math.max(0, parseFloat(left.value) || 0));
    teacherMapPins[i].top = Math.min(100, Math.max(0, parseFloat(top.value) || 0));
    teacherMapPins[i].name = name.value.trim();
    left.value = teacherMapPins[i].left;
    top.value = teacherMapPins[i].top;
    renderTeacherMapPinMarkers();
}

function moveTeacherPin(i, dir) {
    const j = i + dir;
    if (j < 0 || j >= teacherMapPins.length) return;
    const t = teacherMapPins[i];
    teacherMapPins[i] = teacherMapPins[j];
    teacherMapPins[j] = t;
    renderTeacherMapPinTable();
}

function syncTeacherMapPinsEditorVisibility() {
    const on = document.getElementById('useCustomMapPins')?.checked;
    const box = document.getElementById('teacherMapPinsEditor');
    if (box) box.style.display = on ? 'block' : 'none';
    if (on) {
        refreshTeacherMapPreview();
        updateTeacherMapPinHint();
    }
}

function serializeTeacherMapPins() {
    const hidden = document.getElementById('mapPinsJson');
    if (!hidden) return;
    if (!document.getElementById('useCustomMapPins')?.checked) {
        hidden.value = '';
        return;
    }
    syncTeacherPinsToLevelCount();
    hidden.value = JSON.stringify(teacherMapPins);
}

function initTeacherMapPinsEditor() {
    const img = document.getElementById('teacherMapPinPreview');
    const useCb = document.getElementById('useCustomMapPins');
    if (!img || !useCb) return;

    if (__questBoot.mapPinEditor && Array.isArray(__questBoot.mapPinEditor.initialPins) && __questBoot.mapPinEditor.initialPins.length) {
        teacherMapPins = __questBoot.mapPinEditor.initialPins.map(normalizeTeacherPin);
    }

    img.addEventListener('click', function (e) {
        if (!useCb.checked) return;
        syncTeacherPinsToLevelCount();
        const n = getQuestTotalLevels();
        if (n < 1 || teacherMapPins.length === 0) return;
        const r = img.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width) * 100;
        const y = ((e.clientY - r.top) / r.height) * 100;
        const left = Math.round(Math.min(100, Math.max(0, x)) * 100) / 100;
        const top = Math.round(Math.min(100, Math.max(0, y)) * 100) / 100;
        const i = teacherMapPinsActiveIndex % n;
        const prev = teacherMapPins[i];
        teacherMapPins[i] = normalizeTeacherPin({
            left,
            top,
            name: (prev && prev.name) ? prev.name : ('Level ' + (i + 1)),
            icon: (prev && prev.icon) ? prev.icon : 'fa-map-marker-alt'
        });
        teacherMapPinsActiveIndex = (i + 1) % n;
        renderTeacherMapPinTable();
    });

    useCb.addEventListener('change', function () {
        syncTeacherMapPinsEditorVisibility();
        if (useCb.checked) {
            syncTeacherPinsToLevelCount();
            teacherMapPinsActiveIndex = 0;
            renderTeacherMapPinTable();
        }
    });

    document.getElementById('btnTeacherPinsSchoolDefault')?.addEventListener('click', function () {
        const sd = (__questBoot.mapPinEditor?.schoolDefaultPins || []).map(normalizeTeacherPin);
        teacherMapPins = expandBasePinsToLevelsFromSchoolBase(sd, getQuestTotalLevels());
        teacherMapPinsActiveIndex = 0;
        renderTeacherMapPinTable();
    });

    document.getElementById('btnTeacherPinsClear')?.addEventListener('click', function () {
        if (!confirm('Reset all positions to the default path for your ' + getQuestTotalLevels() + ' levels?')) return;
        teacherMapPins = extrapolateMorePins([], getQuestTotalLevels());
        teacherMapPinsActiveIndex = 0;
        renderTeacherMapPinTable();
    });

    document.getElementById('btnTeacherPinsToggleEdit')?.addEventListener('click', function () {
        const grid = document.getElementById('teacherMapPinsGrid');
        const btn = document.getElementById('btnTeacherPinsToggleEdit');
        if (!grid || !btn) return;
        const isOpen = grid.classList.toggle('teacher-map-pins-grid--with-sidebar');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        btn.classList.toggle('is-active', isOpen);
    });

    const lvlEl = document.getElementById('questTotalLevelsInput');
    if (lvlEl) {
        lvlEl.addEventListener('input', function () {
            updateTeacherMapPinHint();
            updateTeacherMapPinPlacementHint();
        });
        lvlEl.addEventListener('change', function () {
            if (useCb.checked) {
                syncTeacherPinsToLevelCount();
                teacherMapPinsActiveIndex = 0;
                renderTeacherMapPinTable();
            }
        });
    }

    syncTeacherMapPinsEditorVisibility();
    if (useCb.checked) {
        syncTeacherPinsToLevelCount();
        teacherMapPinsActiveIndex = 0;
        renderTeacherMapPinTable();
    } else {
        refreshTeacherMapPreview();
    }
}

function selectMapExisting(element) {
    const path = element.getAttribute('data-existing-path');
    if (path) {
        selectMap(element, 'existing:' + path);
    }
}

function selectMap(element, mapValue) {
    document.querySelectorAll('.map-option').forEach(el => {
        if (!el.classList.contains('map-upload-btn')) {
            el.classList.remove('selected');
        }
    });
    element.classList.add('selected');
    document.getElementById('mapImage').value = mapValue;
    refreshTeacherMapPreview();
}

function handleMapUpload(input) {
    const file = input.files[0];
    if (!file) return;

    const maxBytes = 5 * 1024 * 1024; // keep in sync with TeacherQuestController::QUEST_MAP_UPLOAD_MAX_BYTES
    if (file.size > maxBytes) {
        teacherNotify('File size must be 5 MB or smaller.', 'warning');
        return;
    }
    
    // Create preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const mapId = 'custom_' + Date.now();
        uploadedMaps.push({ id: mapId, data: e.target.result });
        
        // Add new map option before upload button
        const selector = document.getElementById('mapSelector');
        const uploadBtn = selector.querySelector('.map-upload-btn');
        
        const newMap = document.createElement('div');
        newMap.className = 'map-option';
        newMap.setAttribute('data-map', mapId);
        newMap.onclick = function() { selectMap(this, mapId); };
        newMap.innerHTML = `
            <img src="${e.target.result}" alt="Custom Map">
            <span class="map-label">Custom Map ${uploadedMaps.length}</span>
            <span class="check-icon"><i class="fas fa-check"></i></span>
        `;
        
        selector.insertBefore(newMap, uploadBtn);
        
        // Select the newly uploaded map
        selectMap(newMap, mapId);
        
        // Store base64 in hidden input for submission
        document.getElementById('mapImage').value = e.target.result;
        refreshTeacherMapPreview();
    };
    reader.readAsDataURL(file);
    
    // Reset input
    input.value = '';
}

function nextStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
    document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
    currentStep = step;
    
    // Update level dropdowns when entering step 2
    if (step === 2) {
        updateLevelDropdowns();
    }
}

function updateLevelDropdowns() {
    const totalLevels = parseInt(document.querySelector('input[name="level"]').value) || 3;
    const levelSelects = ['questionLevel', 'aiQuestionLevel'];
    
    levelSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            const currentValue = select.value;
            select.innerHTML = '';
            for (let i = 1; i <= totalLevels; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Level ${i}`;
                select.appendChild(option);
            }
            // Restore previous selection if valid
            if (currentValue && currentValue <= totalLevels) {
                select.value = currentValue;
            }
        }
    });
}

function openAiQuestionModal() {
    syncQuestAiModelSelects();
    updateLevelDropdowns();
    document.getElementById('aiQuestionModal').style.display = 'flex';
}

function closeAiQuestionModal() {
    document.getElementById('aiQuestionModal').style.display = 'none';
}

async function generateQuestionWithAI() {
    const topic = document.getElementById('aiQuestionTopic').value;
    const type = document.getElementById('aiQuestionType').value;
    const level = document.getElementById('aiQuestionLevel').value;
    const difficulty = document.getElementById('aiQuestionDifficulty').value;
    
    if (!topic) {
        teacherNotify('Please enter a topic', 'warning');
        return;
    }
    
    const btn = document.querySelector('#aiQuestionModal .btn-primary');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Forging...';
    
    try {
        const response = await fetch('{{ route("teacher.ai.generate-question") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                topic,
                type,
                difficulty,
                ai_model: getSelectedQuestAiModel()
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Add the generated question
            const q = result.data;
            questions.push({
                text: q.text,
                type: q.type,
                level: parseInt(level),
                answer: q.answer,
                points: q.points || 10,
                options: q.type === 'multiple_choice' ? q.options : null
            });
            
            renderQuestions();
            closeAiQuestionModal();
            
            // Clear modal inputs
            document.getElementById('aiQuestionTopic').value = '';
            
            teacherNotify('Question forged successfully!', 'success');
        } else {
            teacherNotify('Failed to generate question: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('AI Question Error:', error);
        teacherNotify('Failed to connect to the Neural Forge', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function prevStep(step) {
    nextStep(step);
}

function toggleOptions() {
    const type = document.getElementById('questionType').value;
    const container = document.getElementById('optionsContainer');
    container.style.display = type === 'multiple_choice' ? 'block' : 'none';
}

function addOption() {
    const index = options.length;
    options.push('');
    renderOptions();
}

function renderOptions() {
    const container = document.getElementById('optionsList');
    container.innerHTML = options.map((opt, i) => `
        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <input type="text" class="form-control" value="${opt}" onchange="options[${i}] = this.value" placeholder="Option ${i + 1}">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(${i})"><i class="fas fa-trash"></i></button>
        </div>
    `).join('');
}

function removeOption(index) {
    options.splice(index, 1);
    renderOptions();
}

function addQuestion() {
    const text = document.getElementById('questionText').value;
    const type = document.getElementById('questionType').value;
    const level = document.getElementById('questionLevel').value;
    const answer = document.getElementById('correctAnswer').value;
    const points = document.getElementById('questionPoints').value;

    if (!text || !type || !answer) {
        teacherNotify('Please fill in all required fields', 'warning');
        return;
    }

    questions.push({
        text,
        type,
        level,
        answer,
        points,
        options: type === 'multiple_choice' ? [...options] : null
    });

    renderQuestions();
    
    // Clear form
    document.getElementById('questionText').value = '';
    document.getElementById('correctAnswer').value = '';
    options = [];
    renderOptions();
}

function renderQuestions() {
    const container = document.getElementById('questionsContainer');
    container.innerHTML = questions.map((q, i) => {
        const preview = (q.text && q.text.length > 50) ? q.text.substring(0, 50) + '...' : (q.text || '');
        return `
        <div class="question-item">
            <div>
                <strong>Level ${q.level}:</strong> ${preview}
                <div style="font-size: 0.8rem; color: var(--text-muted);">${q.type} • ${q.points} pts</div>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(${i})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    }).join('');

    // Update hidden inputs for form submission
    updateHiddenInputs();
}

function removeQuestion(index) {
    questions.splice(index, 1);
    renderQuestions();
}

function updateHiddenInputs() {
    // Remove existing hidden inputs
    document.querySelectorAll('input[name^="questions"]').forEach(el => el.remove());
    
    // Add new hidden inputs
    const form = document.getElementById('questForm');
    questions.forEach((q, i) => {
        Object.keys(q).forEach(key => {
            if (q[key] === null || q[key] === undefined) {
                return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `questions[${i}][${key}]`;
            input.value = typeof q[key] === 'object' ? JSON.stringify(q[key]) : q[key];
            form.appendChild(input);
        });
    });
}

function loadSections() {
    const gradeId = document.getElementById('gradeSelect').value;
    const sectionSelect = document.getElementById('sectionSelect');
    
    console.log('Loading sections for grade:', gradeId);
    
    if (!gradeId) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        return Promise.resolve();
    }

    // Show loading state
    sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
    sectionSelect.disabled = true;

    return fetch(`{{ url('/api/grades') }}/${gradeId}/sections`)
        .then(res => {
            console.log('Response status:', res.status);
            if (!res.ok) {
                throw new Error('Network response was not ok: ' + res.status);
            }
            return res.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.error) {
                throw new Error(data.error);
            }
            if (data.length === 0) {
                sectionSelect.innerHTML = '<option value="">No sections available</option>';
            } else {
                sectionSelect.innerHTML = '<option value="">Select Section</option>' +
                    data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            }
            sectionSelect.disabled = false;
        })
        .catch((error) => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error: ' + error.message + '</option>';
            sectionSelect.disabled = false;
        });
}

function openAiModal() {
    syncQuestAiModelSelects();
    document.getElementById('aiModal').style.display = 'flex';
}

function closeAiModal() {
    document.getElementById('aiModal').style.display = 'none';
}

function generateWithAI() {
    const topic = document.getElementById('aiTopic').value;
    if (!topic) {
        teacherNotify('Please enter a topic', 'warning');
        return;
    }

    const btn = document.querySelector('#aiModal .btn-primary');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    fetch('{{ route("teacher.ai.generate-quest") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            topic,
            ai_model: getSelectedQuestAiModel(),
            total_levels: getQuestTotalLevels()
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Populate form with generated data
            document.querySelector('input[name="title"]').value = data.data.title;
            document.querySelector('textarea[name="description"]').value = data.data.description;
            document.querySelector('input[name="xp_reward"]').value = data.data.xp_reward;
            document.querySelector('input[name="ab_reward"]').value = data.data.ab_reward;
            document.querySelector('input[name="gp_reward"]').value = data.data.gp_reward;
            
            questions = data.data.challenges || [];
            renderQuestions();
            closeAiModal();
            teacherNotify('Quest generated successfully!', 'success');
        } else {
            teacherNotify('Failed to generate quest: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(() => teacherNotify('Failed to generate quest', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Form validation + submit loading state (do NOT disable the submit button — that can cancel the POST in some browsers)
let questFormSubmitting = false;
document.getElementById('questForm').addEventListener('submit', function(e) {
    serializeTeacherMapPins();
    if (questions.length === 0) {
        e.preventDefault();
        teacherNotify('Please add at least one question', 'warning');
        nextStep(2);
        return;
    }
    if (questFormSubmitting) {
        e.preventDefault();
        return;
    }
    const titleEl = document.querySelector('#questForm input[name="title"]');
    const descEl = document.querySelector('#questForm textarea[name="description"]');
    const gradeEl = document.getElementById('gradeSelect');
    const sectionEl = document.getElementById('sectionSelect');
    if (!titleEl || !String(titleEl.value || '').trim()) {
        e.preventDefault();
        teacherNotify('Please enter a quest title (Step 1).', 'warning');
        nextStep(1);
        titleEl?.focus();
        return;
    }
    if (!descEl || !String(descEl.value || '').trim()) {
        e.preventDefault();
        teacherNotify('Please enter a description (Step 1).', 'warning');
        nextStep(1);
        descEl?.focus();
        return;
    }
    if (!gradeEl || !gradeEl.value) {
        e.preventDefault();
        teacherNotify('Please select a grade (Step 3).', 'warning');
        nextStep(3);
        gradeEl?.focus();
        return;
    }
    if (!sectionEl || !sectionEl.value) {
        e.preventDefault();
        teacherNotify('Please select a section (Step 3).', 'warning');
        nextStep(3);
        sectionEl?.focus();
        return;
    }
    questFormSubmitting = true;
    const submitBtn = document.getElementById('questSubmitBtn');
    if (submitBtn) {
        submitBtn.classList.add('is-loading');
        submitBtn.setAttribute('aria-busy', 'true');
    }
});

// Initialize level dropdowns on page load
document.addEventListener('DOMContentLoaded', function() {
    initQuestAiModelSelects();
    updateLevelDropdowns();
    initTeacherMapPinsEditor();
    if (questIsEdit) {
        if (questions.length) {
            renderQuestions();
        }
        const gradeEl = document.getElementById('gradeSelect');
        if (gradeEl && gradeEl.value) {
            loadSections().then(() => {
                const sectionEl = document.getElementById('sectionSelect');
                if (sectionEl && questInitialSectionId != null) {
                    sectionEl.value = String(questInitialSectionId);
                }
            });
        }
    }
});
</script>
@endpush
@endsection
