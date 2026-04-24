{{-- Expects: $lessonQuestAiModels, $lessonQuestAiDefault, $grades; optional $aiTopicInitial (string) --}}
@php
    $aiTopicInitial = $aiTopicInitial ?? '';
@endphp
<div class="ai-panel">
    <div class="ai-panel-header">
        <i class="fas fa-robot"></i>
        <div>
            <h3>AI Content Generator</h3>
            <p>Let AI help you create lesson content (optional)</p>
        </div>
    </div>
    <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label"><i class="fas fa-robot"></i> AI model</label>
        @include('teacher.quest._ai_model_picker', [
            'hiddenId' => 'lessonAiModelSelect',
            'questAiModels' => $lessonQuestAiModels,
            'questAiDefault' => $lessonQuestAiDefault,
            'title' => 'Choose which model generates this lesson',
        ])
        <small style="display: block; margin-top: 6px; font-size: 0.8rem; color: var(--text-muted);">Same models as quest AI. OpenRouter needs <code style="font-size: 0.85em;">OPENROUTER_API_KEY</code> in <code style="font-size: 0.85em;">.env</code>.</small>
    </div>
    <div class="ai-form-row">
        <div class="form-group">
            <label class="form-label">Topic / Subject</label>
            <input type="text" id="aiTopic" class="form-control" value="{{ $aiTopicInitial }}" placeholder="e.g., Photosynthesis, World War II" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="form-label">Grade Level</label>
            <select id="aiGradeLevel" class="form-control">
                <option value="general">General</option>
                @foreach ($grades as $grade)
                    <option value="grade{{ $grade->id }}">{{ $grade->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Lesson Type</label>
            <select id="aiLessonType" class="form-control">
                <option value="lecture">Lecture</option>
                <option value="discussion">Discussion</option>
                <option value="activity">Activity-based</option>
                <option value="reading">Reading</option>
            </select>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 16px; margin-top: 8px;">
        <button type="button" class="btn-ai" id="generateAiBtn" onclick="generateLessonContent()">
            <i class="fas fa-magic"></i> Generate with AI
        </button>
        <div class="ai-loading" id="aiLoading">
            <i class="fas fa-spinner fa-spin"></i> Generating content...
        </div>
    </div>
</div>
