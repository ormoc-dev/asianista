{{-- Expects: $generateUrl (route URL for POST JSON) --}}
@if(!empty($generateUrl))
<div class="card" style="margin-bottom: 24px; border: 1px dashed rgba(79, 70, 229, 0.35); background: rgba(79, 70, 229, 0.04);">
    <div class="card-header">
        <h3 class="card-title" style="margin: 0;"><i class="fas fa-wand-magic-sparkles"></i> AI event generator</h3>
    </div>
    <div class="card-body">
        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 16px;">
            Describe a topic or classroom moment. AI suggests title, description, effect, targets, and XP — review and edit before saving.
        </p>
        <div class="form-group">
            <label for="ai_event_topic">Topic or idea</label>
            <textarea id="ai_event_topic" class="form-control" rows="2" placeholder="e.g. Whole class stayed focused through a tough review — quick celebration event"></textarea>
        </div>
        <div class="form-group">
            <label for="ai_event_type">Tone (optional)</label>
            <select id="ai_event_type" class="form-control">
                <option value="auto">Let AI choose</option>
                <option value="positive">Positive / reward</option>
                <option value="negative">Negative / setback</option>
                <option value="neutral">Neutral</option>
                <option value="challenge">Challenge</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" id="btnAiGenerateRandomEvent" data-generate-url="{{ $generateUrl }}">
            <i class="fas fa-wand-magic-sparkles"></i> Generate with AI
        </button>
        <span id="ai_random_event_status" style="margin-left: 12px; font-size: 0.875rem; color: var(--text-muted);"></span>
    </div>
</div>
<script>
(function () {
    var btn = document.getElementById('btnAiGenerateRandomEvent');
    if (!btn) return;
    var generateUrl = btn.getAttribute('data-generate-url');
    if (!generateUrl) return;
    btn.addEventListener('click', function () {
        var topicEl = document.getElementById('ai_event_topic');
        var typeEl = document.getElementById('ai_event_type');
        var statusEl = document.getElementById('ai_random_event_status');
        var topic = topicEl && topicEl.value ? topicEl.value.trim() : '';
        if (!topic) {
            if (statusEl) statusEl.textContent = 'Please enter a topic or idea.';
            return;
        }
        if (statusEl) statusEl.textContent = 'Generating…';
        btn.disabled = true;
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
        fetch(generateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ topic: topic, event_type: typeEl ? typeEl.value : 'auto' }),
        })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (json.status !== 'success' || !json.data) {
                    if (statusEl) statusEl.textContent = json.message || 'Generation failed.';
                    return;
                }
                var d = json.data;
                var setVal = function (id, v) {
                    var el = document.getElementById(id);
                    if (el) el.value = v;
                };
                setVal('title', d.title || '');
                setVal('description', d.description || '');
                setVal('effect', d.effect || '');
                setVal('event_type', d.event_type || 'neutral');
                setVal('target_type', d.target_type || 'single');
                setVal('xp_reward', d.xp_reward != null ? d.xp_reward : 0);
                setVal('xp_penalty', d.xp_penalty != null ? d.xp_penalty : 0);
                if (statusEl) statusEl.textContent = 'Fields filled — review and save.';
            })
            .catch(function () {
                if (statusEl) statusEl.textContent = 'Request failed.';
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
</script>
@endif
