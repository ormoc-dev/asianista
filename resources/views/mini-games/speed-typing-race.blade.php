@extends($layout)

@section('title', $game['name'])
@section('page-title', $game['name'])

@section('content')
<div class="card typing-arena">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-keyboard" style="color: var(--primary);"></i>
            {{ $game['name'] }}
            @if($isTestMode)
                <span class="badge badge-info" style="margin-left: 8px;">Admin Test Mode</span>
            @endif
        </h2>
    </div>
    <div class="card-body">
        @if($isTestMode)
            <div style="margin-bottom: 14px;">
                <img src="{{ asset($game['image'] ?? 'images/default-pp.png') }}" alt="{{ $game['name'] }}" style="width: 100%; max-width: 680px; height: 220px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border);">
            </div>
        @endif
        <p style="margin: 0 0 12px; color: var(--text-secondary);">
            <strong>Type:</strong> {{ $game['type'] }} |
            <strong>Best for:</strong> {{ $game['best_for'] }}
        </p>
        <p style="margin: 0 0 16px; color: var(--text-secondary);">
            {{ $game['mechanics'] }} Build speed and accuracy to rank on the leaderboard.
        </p>

        <div class="typing-target-box">
            <p id="typingParagraph" class="typing-target">
                {{ $assignedParagraph ?? 'Technology empowers students to solve real world problems through logic, creativity, and consistent practice.' }}
            </p>
        </div>

        <div class="form-group">
            <label class="form-label" for="typingInput">Type the paragraph here</label>
            <textarea id="typingInput" class="form-control" rows="5" placeholder="Start typing..."></textarea>
        </div>

        <div class="typing-controls">
            <button id="startTypingBtn" class="btn btn-primary btn-sm">Start Race</button>
            <button id="finishTypingBtn" class="btn btn-success btn-sm" disabled>Finish</button>
            <button id="resetTypingBtn" class="btn btn-secondary btn-sm">Reset</button>
        </div>

        <div class="typing-stats">
            <div><strong>Time:</strong> <span id="typingTime">0.0s</span></div>
            <div><strong>Accuracy:</strong> <span id="typingAccuracy">0%</span></div>
            <div><strong>WPM:</strong> <span id="typingWpm">0</span></div>
        </div>

        <div class="keyboard-wrap">
            <div class="keyboard-row">
                <span class="k" data-k="q">Q</span><span class="k" data-k="w">W</span><span class="k" data-k="e">E</span><span class="k" data-k="r">R</span><span class="k" data-k="t">T</span><span class="k" data-k="y">Y</span><span class="k" data-k="u">U</span><span class="k" data-k="i">I</span><span class="k" data-k="o">O</span><span class="k" data-k="p">P</span>
            </div>
            <div class="keyboard-row">
                <span class="k" data-k="a">A</span><span class="k" data-k="s">S</span><span class="k" data-k="d">D</span><span class="k" data-k="f">F</span><span class="k" data-k="g">G</span><span class="k" data-k="h">H</span><span class="k" data-k="j">J</span><span class="k" data-k="k">K</span><span class="k" data-k="l">L</span>
            </div>
            <div class="keyboard-row">
                <span class="k" data-k="z">Z</span><span class="k" data-k="x">X</span><span class="k" data-k="c">C</span><span class="k" data-k="v">V</span><span class="k" data-k="b">B</span><span class="k" data-k="n">N</span><span class="k" data-k="m">M</span><span class="k k-wide" data-k="backspace">Backspace</span>
            </div>
            <div class="keyboard-row">
                <span class="k k-space" data-k=" ">Space</span>
            </div>
        </div>
    </div>
</div>

<style>
.typing-arena {
    width: 100%;
    max-width: 1360px;
    margin: 0 auto;
    overflow: hidden;
}
.typing-arena .card-body {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    width: 100%;
    max-width: 100%;
}
.typing-arena .card-body > * {
    width: 100%;
    max-width: 100%;
}
.typing-arena .form-group,
.typing-target-box,
.typing-controls,
.typing-stats,
.keyboard-wrap {
    width: 100%;
    max-width: 100%;
}
.typing-arena #typingInput {
    width: 100%;
    max-width: 100%;
    resize: vertical;
}
.typing-target-box { padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #f8fafc; margin-bottom: 12px; }
.typing-target { margin: 0; line-height: 1.7; font-size: 0.95rem; }
.typing-target .ok { color: #16a34a; }
.typing-target .bad { color: #dc2626; background: #fee2e2; border-radius: 3px; }
.typing-target .pending { color: #334155; }
.typing-controls { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }
.typing-arena .typing-controls .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: 0.9rem;
    font-weight: 600;
    line-height: 1;
    min-height: 40px;
    text-decoration: none;
    cursor: pointer;
    transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
}
.typing-arena .typing-controls .btn:hover { transform: translateY(-1px); filter: brightness(0.98); }
.typing-arena .typing-controls .btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.typing-arena .typing-controls .btn-primary { background: #4f46e5; color: #fff; box-shadow: 0 6px 14px rgba(79, 70, 229, 0.25); }
.typing-arena .typing-controls .btn-success { background: #059669; color: #fff; box-shadow: 0 6px 14px rgba(5, 150, 105, 0.25); }
.typing-arena .typing-controls .btn-secondary { background: #e2e8f0; color: #1e293b; border-color: #cbd5e1; }
.typing-stats { display: flex; gap: 18px; flex-wrap: wrap; margin-top: 16px; color: var(--text-secondary); }
.keyboard-wrap { margin-top: 16px; padding: 12px; border: 1px solid var(--border); border-radius: 12px; background: #eef2ff; }
.keyboard-row { display: flex; justify-content: center; gap: 6px; margin-bottom: 6px; flex-wrap: wrap; }
.k { min-width: 36px; height: 36px; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 600; color: #334155; transition: all .08s ease; user-select: none; }
.k.k-wide { min-width: 96px; }
.k.k-space { min-width: 180px; width: min(100%, 280px); }
.k.active { background: #4f46e5; color: #fff; border-color: #4f46e5; transform: translateY(1px) scale(0.98); }
.k.good { box-shadow: inset 0 0 0 2px #16a34a; }
.k.bad { box-shadow: inset 0 0 0 2px #dc2626; }
@media (max-width: 768px) {
    .typing-arena { max-width: 100%; }
    .typing-target { font-size: 0.9rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paragraphEl = document.getElementById('typingParagraph');
    const paragraph = paragraphEl.textContent.trim();
    const input = document.getElementById('typingInput');
    const startBtn = document.getElementById('startTypingBtn');
    const finishBtn = document.getElementById('finishTypingBtn');
    const resetBtn = document.getElementById('resetTypingBtn');
    const timeEl = document.getElementById('typingTime');
    const accuracyEl = document.getElementById('typingAccuracy');
    const wpmEl = document.getElementById('typingWpm');

    let startedAt = null;
    let timer = null;
    const keyEls = Array.from(document.querySelectorAll('.k'));

    function renderProgress() {
        const typed = input.value;
        let html = '';
        for (let i = 0; i < paragraph.length; i++) {
            const ch = paragraph[i];
            if (i < typed.length) {
                html += typed[i] === ch ? '<span class="ok">' + escapeHtml(ch) + '</span>' : '<span class="bad">' + escapeHtml(ch) + '</span>';
            } else {
                html += '<span class="pending">' + escapeHtml(ch) + '</span>';
            }
        }
        paragraphEl.innerHTML = html;
    }

    function escapeHtml(char) {
        if (char === '&') return '&amp;';
        if (char === '<') return '&lt;';
        if (char === '>') return '&gt;';
        if (char === '"') return '&quot;';
        if (char === "'") return '&#39;';
        if (char === ' ') return ' ';
        return char;
    }

    function flashKey(key, isGood) {
        const normalized = key === 'Backspace' ? 'backspace' : key.toLowerCase();
        const keyEl = keyEls.find(el => el.dataset.k === normalized);
        if (!keyEl) return;
        keyEl.classList.add('active');
        keyEl.classList.remove('good', 'bad');
        keyEl.classList.add(isGood ? 'good' : 'bad');
        setTimeout(() => keyEl.classList.remove('active', 'good', 'bad'), 120);
    }

    function stopTimer() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    function resetGame() {
        stopTimer();
        startedAt = null;
        input.value = '';
        input.disabled = false;
        finishBtn.disabled = true;
        timeEl.textContent = '0.0s';
        accuracyEl.textContent = '0%';
        wpmEl.textContent = '0';
        paragraphEl.textContent = paragraph;
    }

    function elapsedSeconds() {
        if (!startedAt) return 0;
        return (Date.now() - startedAt) / 1000;
    }

    startBtn.addEventListener('click', function () {
        resetGame();
        startedAt = Date.now();
        finishBtn.disabled = false;
        input.focus();
        timer = setInterval(function () {
            timeEl.textContent = elapsedSeconds().toFixed(1) + 's';
        }, 100);
    });

    input.addEventListener('input', renderProgress);

    input.addEventListener('keydown', function (event) {
        if (!startedAt) return;
        const currentIndex = input.value.length;
        if (event.key === 'Backspace') {
            flashKey('Backspace', true);
            return;
        }
        if (event.key.length === 1 || event.key === ' ') {
            const expected = paragraph[currentIndex] || '';
            flashKey(event.key === ' ' ? ' ' : event.key, event.key === expected);
        }
    });

    finishBtn.addEventListener('click', function () {
        const seconds = Math.max(elapsedSeconds(), 0.1);
        stopTimer();
        input.disabled = true;
        finishBtn.disabled = true;

        const typed = input.value;
        let correct = 0;
        const compareLen = Math.max(typed.length, paragraph.length);
        for (let i = 0; i < Math.min(typed.length, paragraph.length); i++) {
            if (typed[i] === paragraph[i]) correct++;
        }

        const accuracy = compareLen ? (correct / compareLen) * 100 : 0;
        const wordsTyped = typed.trim().length / 5;
        const wpm = Math.round((wordsTyped / seconds) * 60);

        timeEl.textContent = seconds.toFixed(1) + 's';
        accuracyEl.textContent = accuracy.toFixed(1) + '%';
        wpmEl.textContent = String(Math.max(wpm, 0));
    });

    resetBtn.addEventListener('click', resetGame);
});
</script>
@endsection
