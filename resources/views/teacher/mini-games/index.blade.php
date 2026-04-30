@extends('teacher.layouts.app')

@section('title', 'Mini Games')
@section('page-title', 'Mini Games')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-gamepad" style="color: var(--primary);"></i> Mini Games</h2>
    </div>
    <div class="card-body">
        @if(empty($games))
            <p style="margin: 0; color: var(--text-secondary);">
                No mini games are currently available. Please check back later.
            </p>
        @else
            <p style="margin: 0 0 16px; color: var(--text-secondary);">
                Teachers assign active mini games to specific grade and section. Teachers cannot play these games.
            </p>
            <div style="display: grid; gap: 12px;">
                @foreach($games as $game)
                    <div style="border: 1px solid var(--border); border-radius: var(--radius); padding: 16px;">
                        <div style="margin: 0 0 12px;">
                            <img src="{{ asset($game['image'] ?? 'images/default-pp.png') }}" alt="{{ $game['name'] }}" style="width: 100%; max-width: 420px; height: 160px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border);">
                        </div>
                        <h3 style="margin: 0 0 8px; font-size: 1rem;">{{ $game['name'] }}</h3>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.88rem;">
                            <strong>Type:</strong> {{ $game['type'] }}<br>
                            <strong>Mechanics:</strong> {{ $game['mechanics'] }}<br>
                            <strong>Gamification:</strong> {{ $game['gamification'] }}<br>
                            <strong>Best for:</strong> {{ $game['best_for'] }}
                        </p>

                        <div style="margin-top: 12px;">
                            <button type="button" class="btn btn-primary btn-sm js-open-game-panel" data-game="{{ $game['slug'] }}">
                                Open Game
                            </button>
                        </div>

                        <form method="POST" action="{{ route('teacher.mini-games.assign', $game['slug']) }}" class="js-game-panel" data-game-panel="{{ $game['slug'] }}" style="margin-top: 12px;" hidden>
                            @csrf
                            <div style="display: grid; gap: 10px;">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                                    <div>
                                        <label class="form-label">Grade</label>
                                        <select name="grade_id" class="form-control js-grade" data-game="{{ $game['slug'] }}" required>
                                            <option value="">Select grade</option>
                                            @foreach($grades as $grade)
                                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Section</label>
                                        <select name="section_id" class="form-control js-section" data-game="{{ $game['slug'] }}" required>
                                            <option value="">Select section</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Typing Paragraph</label>
                                    <div style="display: grid; grid-template-columns: 1fr 120px auto; gap: 8px; margin-bottom: 8px;">
                                        <input type="text" class="form-control js-topic" data-game="{{ $game['slug'] }}" placeholder="Optional topic (e.g. cyber safety)">
                                        <input type="number" min="3" max="15" value="8" class="form-control js-sentences" data-game="{{ $game['slug'] }}" title="Number of sentences">
                                        <button type="button" class="btn btn-secondary btn-sm js-generate" data-game="{{ $game['slug'] }}">Generate AI</button>
                                    </div>
                                    <textarea name="paragraph" class="form-control js-paragraph" data-game="{{ $game['slug'] }}" rows="7" required>Technology empowers students to solve real world problems through logic, creativity, and consistent practice.</textarea>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary btn-sm js-assign-btn">
                                        <span class="js-assign-default">Assign to Class</span>
                                        <span class="js-assign-loading" style="display:none;">
                                            <i class="fas fa-spinner fa-spin"></i> Assigning...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="card" style="margin-top: 16px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-list-check" style="color: var(--primary);"></i> Assigned Mini Games</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Started</th>
                        <th>Paragraph</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->game->name ?? 'Mini Game' }}</td>
                            <td>{{ $assignment->grade->name ?? '—' }}</td>
                            <td>{{ $assignment->section->name ?? '—' }}</td>
                            <td>{{ optional($assignment->starts_at)->format('M d, Y h:i A') ?? '—' }}</td>
                            <td style="max-width: 460px;">{{ \Illuminate\Support\Str::limit($assignment->paragraph, 120) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">No mini game assignments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="mini-games-data" data-grades='@json($grades->map(function($g){ return ["id" => $g->id, "sections" => $g->sections->map(function($s){ return ["id" => $s->id, "name" => $s->name]; })->values()]; })->values())' style="display:none;"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataEl = document.getElementById('mini-games-data');
    const grades = dataEl ? JSON.parse(dataEl.dataset.grades || '[]') : [];
    const gradeMap = {};
    grades.forEach(g => { gradeMap[String(g.id)] = g.sections; });

    document.querySelectorAll('.js-grade').forEach(function (gradeSelect) {
        gradeSelect.addEventListener('change', function () {
            const game = gradeSelect.dataset.game;
            const sectionSelect = document.querySelector('.js-section[data-game="' + game + '"]');
            if (!sectionSelect) return;
            const sections = gradeMap[String(gradeSelect.value)] || [];
            let html = '<option value="">Select section</option>';
            sections.forEach(function (s) { html += '<option value="' + s.id + '">' + s.name + '</option>'; });
            sectionSelect.innerHTML = html;
        });
    });

    document.querySelectorAll('.js-generate').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const game = btn.dataset.game;
            const topicInput = document.querySelector('.js-topic[data-game="' + game + '"]');
            const sentencesInput = document.querySelector('.js-sentences[data-game="' + game + '"]');
            const paragraphField = document.querySelector('.js-paragraph[data-game="' + game + '"]');
            if (!paragraphField) return;

            btn.disabled = true;
            const oldLabel = btn.textContent;
            btn.textContent = 'Generating...';

            try {
                const response = await fetch("{{ url('teacher/mini-games') }}/" + game + '/generate-paragraph', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        topic: topicInput ? topicInput.value : '',
                        sentences: sentencesInput ? Number(sentencesInput.value || 8) : 8
                    })
                });

                const result = await response.json();
                if (result.status === 'success' && result.paragraph) {
                    paragraphField.value = result.paragraph;
                } else if (window.teacherNotify) {
                    window.teacherNotify(result.message || 'Unable to generate paragraph.', 'warning');
                }
            } catch (_) {
                if (window.teacherNotify) {
                    window.teacherNotify('Unable to generate paragraph right now.', 'error');
                }
            } finally {
                btn.disabled = false;
                btn.textContent = oldLabel;
            }
        });
    });

    document.querySelectorAll('.js-open-game-panel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const game = btn.dataset.game;
            const panel = document.querySelector('.js-game-panel[data-game-panel="' + game + '"]');
            if (!panel) return;

            const isHidden = panel.hasAttribute('hidden');
            if (isHidden) {
                panel.removeAttribute('hidden');
                btn.textContent = 'Hide Game Setup';
            } else {
                panel.setAttribute('hidden', 'hidden');
                btn.textContent = 'Open Game';
            }
        });
    });

    document.querySelectorAll('.js-game-panel').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitBtn = form.querySelector('.js-assign-btn');
            if (!submitBtn || submitBtn.disabled) return;

            const defaultText = submitBtn.querySelector('.js-assign-default');
            const loadingText = submitBtn.querySelector('.js-assign-loading');

            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-busy', 'true');
            if (defaultText) defaultText.style.display = 'none';
            if (loadingText) loadingText.style.display = 'inline-flex';
        });
    });
});
</script>
@endpush
@endsection
