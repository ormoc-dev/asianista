{{-- Expects: $lessonQuestAiDefault --}}
<script type="application/json" id="lesson-ai-bootstrap-data">
{!! json_encode(['questAiDefault' => $lessonQuestAiDefault], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
const LESSON_AI_STORAGE_KEY = 'asianista_teacher_lesson_ai_model';
const __lessonAiBoot = JSON.parse(document.getElementById('lesson-ai-bootstrap-data').textContent);
const __lessonAiDefault = __lessonAiBoot.questAiDefault;

function lessonUpdateAiPickerDisplay(wrap, value) {
    const hidden = wrap.querySelector('.js-quest-ai-model');
    const inner = wrap.querySelector('[data-quest-ai-trigger-inner]');
    const opt = wrap.querySelector('.quest-ai-model-option[data-value="' + value + '"]');
    if (!hidden || !inner || !opt) return;
    hidden.value = value;
    inner.innerHTML = opt.innerHTML;
}

function lessonSetGlobalAiModel(value, skipStorage) {
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        lessonUpdateAiPickerDisplay(wrap, value);
    });
    if (!skipStorage) {
        localStorage.setItem(LESSON_AI_STORAGE_KEY, value);
    }
}

function lessonCloseAllAiPickers() {
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        const menu = wrap.querySelector('[data-quest-ai-menu]');
        const trigger = wrap.querySelector('[data-quest-ai-trigger]');
        if (menu) menu.hidden = true;
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        wrap.classList.remove('is-open');
    });
}

function lessonSyncAiModelPicker() {
    const first = document.querySelector('[data-quest-ai-picker]');
    if (!first) return;
    let v = localStorage.getItem(LESSON_AI_STORAGE_KEY) || __lessonAiDefault;
    const valid = Array.from(first.querySelectorAll('.quest-ai-model-option')).map(function (li) {
        return li.getAttribute('data-value');
    });
    if (valid.indexOf(v) === -1) v = __lessonAiDefault;
    if (valid.indexOf(v) === -1 && valid.length) v = valid[0];
    lessonSetGlobalAiModel(v, true);
}

function lessonInitAiModelPicker() {
    lessonSyncAiModelPicker();
    document.querySelectorAll('[data-quest-ai-picker]').forEach(function (wrap) {
        const trigger = wrap.querySelector('[data-quest-ai-trigger]');
        const menu = wrap.querySelector('[data-quest-ai-menu]');
        if (!trigger || !menu) return;
        wrap.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        trigger.addEventListener('click', function () {
            const willOpen = menu.hidden;
            lessonCloseAllAiPickers();
            if (willOpen) {
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                wrap.classList.add('is-open');
            }
        });
        menu.querySelectorAll('.quest-ai-model-option').forEach(function (li) {
            li.addEventListener('click', function () {
                const v = li.getAttribute('data-value');
                lessonSetGlobalAiModel(v, false);
                lessonCloseAllAiPickers();
            });
        });
    });
    document.addEventListener('click', function () {
        lessonCloseAllAiPickers();
    });
}

function getSelectedLessonAiModel() {
    const el = document.querySelector('.js-quest-ai-model');
    return el ? el.value : __lessonAiDefault;
}

async function generateLessonContent() {
    const topic = document.getElementById('aiTopic').value.trim();
    const gradeLevel = document.getElementById('aiGradeLevel').value;
    const lessonType = document.getElementById('aiLessonType').value;

    if (!topic) {
        teacherNotify('Please enter a topic for the lesson.', 'warning');
        return;
    }

    const btn = document.getElementById('generateAiBtn');
    const loading = document.getElementById('aiLoading');

    btn.disabled = true;
    loading.classList.add('show');

    try {
        const response = await fetch("{{ route('teacher.ai.generate-lesson') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: topic,
                grade_level: gradeLevel,
                lesson_type: lessonType,
                ai_model: getSelectedLessonAiModel()
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;

            const titleEl = document.getElementById('lessonTitle');
            if (titleEl) titleEl.value = data.title || topic;

            let content = '';

            if (data.objectives && data.objectives.length > 0) {
                content += 'LEARNING OBJECTIVES\n';
                data.objectives.forEach((obj, i) => {
                    content += (i + 1) + '. ' + obj + '\n';
                });
                content += '\n';
            }

            if (data.introduction) {
                content += 'INTRODUCTION\n' + data.introduction + '\n\n';
            }

            if (data.main_content) {
                content += 'MAIN CONTENT\n' + data.main_content + '\n\n';
            }

            if (data.key_points && data.key_points.length > 0) {
                content += 'KEY POINTS\n';
                data.key_points.forEach(point => {
                    content += '- ' + point + '\n';
                });
                content += '\n';
            }

            if (data.activities && data.activities.length > 0) {
                content += 'ACTIVITIES\n';
                data.activities.forEach((act, i) => {
                    content += (i + 1) + '. ' + act + '\n';
                });
                content += '\n';
            }

            if (data.summary) {
                content += 'SUMMARY\n' + data.summary;
            }

            const contentEl = document.getElementById('lessonContent');
            if (contentEl) contentEl.value = content;

            teacherNotify('Lesson content generated successfully! Review and edit as needed.', 'success');
        } else {
            teacherNotify('Failed to generate content: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        teacherNotify('An error occurred while generating content.', 'error');
    } finally {
        btn.disabled = false;
        loading.classList.remove('show');
    }
}
</script>
