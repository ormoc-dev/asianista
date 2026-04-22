@extends('student.dashboard')

@section('content')
<audio id="quest-bg-music" src="{{ asset('music/bg_music.mp3') }}" loop preload="metadata" hidden></audio>
<div id="quest-play-mount">
    @include('student.quest.play-fragment')
</div>
@include('student.quest.play-styles')
<script>
let activePower = null;
let eliminatedOptions = [];
let questPlay = {};

function refreshQuestPlayConfig() {
    const el = document.getElementById('quest-play-config');
    questPlay = el && el.dataset ? Object.assign({}, el.dataset) : {};
}

refreshQuestPlayConfig();

let timeRemaining = parseInt(questPlay.timeSeconds || '0', 10);
let timerInterval = null;
let extraTimeAdded = 0;
let bossCurrentHp = parseInt(questPlay.bossCurrentHp || '0', 10);
let bossMaxHp = Math.max(1, parseInt(questPlay.bossMaxHp || '1', 10));

let questMusicBindingsDone = false;
let questMusicGestureResumeFn = null;

function bindQuestBgMusicOnce() {
    if (questMusicBindingsDone) return;
    questMusicBindingsDone = true;
    const storageKey = 'questBgMusicVolume';
    const playStateKey = 'questBgMusicShouldPlay';

    document.addEventListener('click', function (e) {
        const toggle = e.target.closest('#quest-music-toggle');
        if (!toggle) return;
        const audio = document.getElementById('quest-bg-music');
        if (!audio) return;
        if (audio.paused) {
            audio.play().then(function () {
                try { sessionStorage.setItem(playStateKey, '1'); } catch (err) { /* ignore */ }
                syncQuestBgMusicUi();
            }).catch(function () { syncQuestBgMusicUi(); });
        } else {
            audio.pause();
            try { sessionStorage.setItem(playStateKey, '0'); } catch (err) { /* ignore */ }
            syncQuestBgMusicUi();
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target.id !== 'quest-music-volume') return;
        const audio = document.getElementById('quest-bg-music');
        const vol = e.target;
        if (!audio || !vol) return;
        let pct = parseInt(vol.value, 10);
        if (isNaN(pct)) pct = 45;
        pct = Math.max(0, Math.min(100, pct));
        audio.volume = pct / 100;
        audio.muted = pct === 0;
        vol.style.setProperty('--vol-pct', pct + '%');
        try { localStorage.setItem(storageKey, String(audio.volume)); } catch (err) { /* ignore */ }
        const volIconWrap = document.querySelector('.music-volume-icon');
        if (volIconWrap) {
            const i = volIconWrap.querySelector('i');
            if (i) {
                if (audio.muted || audio.volume === 0) i.className = 'fas fa-volume-mute';
                else if (audio.volume < 0.45) i.className = 'fas fa-volume-down';
                else i.className = 'fas fa-volume-up';
            }
        }
    });

    const audioGlobal = document.getElementById('quest-bg-music');
    if (audioGlobal && !audioGlobal.dataset.questVolumeBound) {
        audioGlobal.dataset.questVolumeBound = '1';
        audioGlobal.addEventListener('volumechange', function () {
            const vol = document.getElementById('quest-music-volume');
            if (!vol) return;
            const pct = Math.round(audioGlobal.volume * 100);
            if (document.activeElement !== vol) vol.value = String(pct);
            vol.style.setProperty('--vol-pct', pct + '%');
            const volIconWrap = document.querySelector('.music-volume-icon');
            if (volIconWrap) {
                const i = volIconWrap.querySelector('i');
                if (i) {
                    if (audioGlobal.muted || audioGlobal.volume === 0) i.className = 'fas fa-volume-mute';
                    else if (audioGlobal.volume < 0.45) i.className = 'fas fa-volume-down';
                    else i.className = 'fas fa-volume-up';
                }
            }
        });
    }
}

function syncQuestBgMusicUi() {
    const audio = document.getElementById('quest-bg-music');
    const toggle = document.getElementById('quest-music-toggle');
    const vol = document.getElementById('quest-music-volume');
    const volIconWrap = document.querySelector('.music-volume-icon');
    if (!audio || !toggle || !vol) return;

    const storageKey = 'questBgMusicVolume';
    const playStateKey = 'questBgMusicShouldPlay';

    function setSliderTrackPct(pct) {
        vol.style.setProperty('--vol-pct', pct + '%');
    }

    function syncVolumeIcon() {
        if (!volIconWrap) return;
        const i = volIconWrap.querySelector('i');
        if (!i) return;
        const v = audio.volume;
        if (audio.muted || v === 0) i.className = 'fas fa-volume-mute';
        else if (v < 0.45) i.className = 'fas fa-volume-down';
        else i.className = 'fas fa-volume-up';
    }

    function updateToggleUi() {
        const icon = toggle.querySelector('i');
        const playing = !audio.paused;
        toggle.setAttribute('aria-pressed', playing ? 'true' : 'false');
        toggle.setAttribute('aria-label', playing ? 'Pause background music' : 'Play background music');
        if (icon) icon.className = playing ? 'fas fa-pause' : 'fas fa-play';
    }

    const saved = localStorage.getItem(storageKey);
    if (saved !== null && saved !== '') {
        const parsed = parseFloat(saved);
        if (!isNaN(parsed)) {
            const pct = Math.round(Math.max(0, Math.min(1, parsed)) * 100);
            vol.value = String(pct);
        }
    }
    let pct = parseInt(vol.value, 10);
    if (isNaN(pct)) pct = 45;
    pct = Math.max(0, Math.min(100, pct));
    audio.volume = pct / 100;
    audio.muted = pct === 0;
    setSliderTrackPct(pct);
    syncVolumeIcon();
    updateToggleUi();

    function tryAutoResumeMusic() {
        try {
            if (sessionStorage.getItem(playStateKey) !== '1') return;
        } catch (err) {
            return;
        }
        if (questMusicGestureResumeFn) {
            document.removeEventListener('pointerdown', questMusicGestureResumeFn, true);
            document.removeEventListener('keydown', questMusicGestureResumeFn, true);
            questMusicGestureResumeFn = null;
        }
        audio.play().then(function () { updateToggleUi(); }).catch(function () {
            updateToggleUi();
            questMusicGestureResumeFn = function resumeOnce() {
                try {
                    if (sessionStorage.getItem(playStateKey) !== '1') return;
                } catch (e) { return; }
                audio.play().then(function () {
                    document.removeEventListener('pointerdown', questMusicGestureResumeFn, true);
                    document.removeEventListener('keydown', questMusicGestureResumeFn, true);
                    questMusicGestureResumeFn = null;
                    updateToggleUi();
                }).catch(function () {});
            };
            document.addEventListener('pointerdown', questMusicGestureResumeFn, true);
            document.addEventListener('keydown', questMusicGestureResumeFn, true);
        });
    }

    tryAutoResumeMusic();
}

function positionPulseAndHud() {
    document.querySelectorAll('.current-node-pulse').forEach(function (el) {
        var L = el.getAttribute('data-left');
        var T = el.getAttribute('data-top');
        if (L != null && L !== '') el.style.left = L + '%';
        if (T != null && T !== '') el.style.top = T + '%';
    });
    document.querySelectorAll('.hud-hp-fill[data-width]').forEach(function (el) {
        const width = parseFloat(el.getAttribute('data-width') || '0');
        el.style.width = `${Math.max(0, Math.min(100, width))}%`;
    });
}

function reinitQuestPlayAfterSwap() {
    refreshQuestPlayConfig();
    activePower = null;
    eliminatedOptions = [];
    extraTimeAdded = 0;
    bossCurrentHp = parseInt(questPlay.bossCurrentHp || '0', 10);
    bossMaxHp = Math.max(1, parseInt(questPlay.bossMaxHp || '1', 10));
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    timeRemaining = parseInt(questPlay.timeSeconds || '0', 10);
    positionPulseAndHud();
    const hint = document.getElementById('active-power-hint');
    if (hint) hint.classList.remove('show');
    const hintText = document.getElementById('hint-text');
    if (hintText) hintText.innerHTML = '';
    const qp = document.getElementById('battle-question-panel');
    if (qp) qp.style.display = 'block';
    const modal = document.getElementById('quest-feedback-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
    if (timeRemaining > 0) startTimer();
    syncQuestBgMusicUi();
}

document.addEventListener('DOMContentLoaded', function () {
    bindQuestBgMusicOnce();
    positionPulseAndHud();
    if (timeRemaining > 0) startTimer();
    syncQuestBgMusicUi();
});

document.addEventListener('turbolinks:before-visit', function () {
    const a = document.getElementById('quest-bg-music');
    if (a) a.pause();
});

function startTimer() {
    if (timeRemaining <= 0) return;
    const timerDisplay = document.getElementById('timer-count');
    const timerContainer = document.getElementById('quest-timer');
    if (!timerDisplay || !timerContainer) return;

    timerInterval = setInterval(() => {
        timeRemaining--;

        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeRemaining <= 30 && !timerContainer.classList.contains('warning')) {
            timerContainer.classList.add('warning');
        }

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timerInterval = null;
            handleTimeUp();
        }
    }, 1000);
}

function handleTimeUp() {
    const submitBtn = document.getElementById('submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-clock"></i> Time\'s Up!';
    }

    fetch(questPlay.timeoutUrl || '', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': questPlay.csrf || ''
        }
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                if (result.new_hp !== undefined) {
                    updateHPDisplay(result.new_hp);
                }
                showBattle('defeat', result.message, () => navigateAfterQuest(result.next_url));
            } else {
                showBattle('defeat', 'Time\'s up!', () => navigateAfterQuest(result.next_url || questPlay.questShowUrl || ''));
            }
        })
        .catch(error => {
            console.error('Timeout error:', error);
            showBattle('defeat', 'Time\'s up!', () => {
                navigateAfterQuest(questPlay.questShowUrl || '');
            });
        });
}

function addExtraTime() {
    extraTimeAdded += 30;
    timeRemaining += 30;

    const timerDisplay = document.getElementById('timer-count');
    const timerContainer = document.getElementById('quest-timer');
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    if (timerDisplay) timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

    if (timerContainer && timeRemaining > 30) {
        timerContainer.classList.remove('warning');
    }
}

function getQuestCorrectAnswer() {
    const raw = questPlay.correctAnswer;
    if (raw === undefined || raw === null) return '';
    return String(raw).trim();
}

function isRadioOptionIncorrect(opt) {
    const correct = getQuestCorrectAnswer();
    if (!correct) return false;
    const qType = String(questPlay.questionType || '').toLowerCase();
    const val = String(opt.value).trim();
    if (qType === 'multiple_choice' || qType === 'true_false') {
        return val !== correct.trim();
    }
    return val.toLowerCase() !== correct.toLowerCase();
}

function canEliminateWrongAnswer() {
    const options = document.querySelectorAll('.option-item input[type="radio"]');
    if (options.length === 0 || !getQuestCorrectAnswer()) return false;
    const wrongPool = Array.from(options).filter(function (opt) {
        if (opt.checked) return false;
        if (opt.closest('.option-item').classList.contains('eliminated')) return false;
        return isRadioOptionIncorrect(opt);
    });
    return wrongPool.length >= 1;
}

function finalizePowerButton(btn) {
    btn.disabled = true;
    btn.classList.add('used');
    const costEl = btn.querySelector('.power-ap-tag');
    if (costEl) costEl.remove();
    const badge = document.createElement('span');
    badge.className = 'power-used-badge';
    badge.innerHTML = '<i class="fas fa-check"></i> Used';
    btn.appendChild(badge);
}

async function usePower(e, powerName, powerDesc) {
    const btn = e.currentTarget;
    if (!btn || btn.classList.contains('used')) return;

    const pLower = powerName.toLowerCase();
    if (pLower === 'arcane analysis' && !canEliminateWrongAnswer()) {
        alert('Arcane Analysis needs at least one incorrect option to eliminate on this question.');
        return;
    }

    let data;
    try {
        const res = await fetch(questPlay.usePowerUrl || '', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': questPlay.csrf || ''
            },
            body: JSON.stringify({
                power_name: powerName,
                level: parseInt(questPlay.questionLevel || '0', 10)
            })
        });
        data = await res.json().catch(function () { return {}; });
        if (!res.ok) {
            if (data.insufficient_ap) {
                alert('Not enough AP. This power costs ' + data.required + ' AP (you have ' + data.current + ').');
            } else {
                alert(data.error || 'Could not use power.');
            }
            return;
        }
    } catch (err) {
        console.error('Power request failed:', err);
        alert('Network error. Please try again.');
        return;
    }

    if (data.new_ap !== undefined && data.new_ap !== null) {
        updateAPDisplay(Number(data.new_ap));
    }

    const hintBox = document.getElementById('active-power-hint');
    const hintText = document.getElementById('hint-text');

    switch (pLower) {
        case 'spell of insight':
            hintText.innerHTML = '<strong>Spell of Insight:</strong> ' + getHintForQuestion();
            hintBox.classList.add('show');
            activePower = 'insight';
            break;
        case 'arcane analysis':
            if (eliminateWrongAnswer()) {
                hintText.innerHTML = '<strong>Arcane Analysis:</strong> One incorrect option has been eliminated!';
                hintBox.classList.add('show');
                activePower = 'analysis';
            }
            break;
        case 'time warp':
            hintText.innerHTML = '<strong>Time Warp:</strong> Extra 30 seconds granted!';
            hintBox.classList.add('show');
            activePower = 'timewarp';
            addExtraTime();
            break;
        case 'power strike':
            hintText.innerHTML = '<strong>Power Strike:</strong> Next correct answer worth double points!';
            hintBox.classList.add('show');
            activePower = 'powerstrike';
            break;
        case 'shield guard':
            hintText.innerHTML = '<strong>Shield Guard:</strong> Protected from HP loss on next wrong answer!';
            hintBox.classList.add('show');
            activePower = 'shield';
            break;
        case 'revive':
            hintText.innerHTML = '<strong>Revive:</strong> If your next answer is wrong, you will not lose HP (you still move to the next challenge).';
            hintBox.classList.add('show');
            activePower = 'revive';
            break;
        case 'focus aura':
            hintText.innerHTML = '<strong>Focus Aura:</strong> If your next answer is wrong, you will not lose HP (you still move to the next challenge).';
            hintBox.classList.add('show');
            activePower = 'focus';
            break;
        default:
            hintText.innerHTML = '<strong>' + powerName + ':</strong> ' + powerDesc;
            hintBox.classList.add('show');
    }

    finalizePowerButton(btn);
}

document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('#quest-powers-list .power-btn');
    if (!btn || btn.disabled || btn.classList.contains('used')) return;
    const name = btn.getAttribute('data-power-name');
    if (!name) return;
    const desc = btn.getAttribute('data-power-desc') || '';
    usePower({ currentTarget: btn }, name, desc);
});

function getHintForQuestion() {
    const hints = [
        'Read the question carefully and look for key words.',
        'Eliminate obviously wrong answers first.',
        'Think about what you learned in the lessons.',
        'Consider the context of the question.',
        'Trust your first instinct - it\'s often correct!'
    ];
    return hints[Math.floor(Math.random() * hints.length)];
}

function eliminateWrongAnswer() {
    const options = document.querySelectorAll('.option-item input[type="radio"]');
    if (options.length === 0 || !getQuestCorrectAnswer()) return false;
    const wrongPool = Array.from(options).filter(function (opt) {
        if (opt.checked) return false;
        if (opt.closest('.option-item').classList.contains('eliminated')) return false;
        return isRadioOptionIncorrect(opt);
    });
    if (wrongPool.length === 0) return false;
    const toEliminate = wrongPool[Math.floor(Math.random() * wrongPool.length)];
    toEliminate.closest('.option-item').classList.add('eliminated');
    eliminatedOptions.push(toEliminate.value);
    return true;
}

function isQuestPlayStepUrl(url) {
    try {
        const u = new URL(url, window.location.origin);
        return /\/quest\/\d+\/play(\/|$)/.test(u.pathname);
    } catch (e) {
        return typeof url === 'string' && url.indexOf('/play') !== -1;
    }
}

async function navigateAfterQuest(url) {
    if (!url) return;
    if (window.Turbolinks && typeof window.Turbolinks.clearCache === 'function') {
        window.Turbolinks.clearCache();
    }
    if (!isQuestPlayStepUrl(url)) {
        window.location.href = url;
        return;
    }
    try {
        const res = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });
        if (!res.ok) throw new Error('Navigation failed');
        const html = await res.text();
        const mount = document.getElementById('quest-play-mount');
        if (!mount) {
            window.location.href = url;
            return;
        }
        mount.innerHTML = html;
        reinitQuestPlayAfterSwap();
    } catch (err) {
        console.error(err);
        window.location.href = url;
    }
}

function showBattle(outcome, message, onContinue) {
    const questionPanel = document.getElementById('battle-question-panel');
    if (questionPanel) questionPanel.style.display = 'none';

    const modal = document.getElementById('quest-feedback-modal');
    const result = document.getElementById('battle-result');
    const title = document.getElementById('battle-title');
    const msg = document.getElementById('battle-message');
    const eyebrow = document.getElementById('battle-result-eyebrow');
    const badge = document.getElementById('battle-result-badge');
    const nextBtn = document.getElementById('modal-next-btn');
    const nextBtnLabel = nextBtn ? nextBtn.querySelector('.btn-battle-action__text') : null;
    const dragonFire = document.getElementById('dragon-fire');
    const heroFire = document.getElementById('hero-fire');
    const heroSprite = document.getElementById('hero-sprite');
    const dragonSprite = document.getElementById('dragon-sprite');

    if (dragonFire) dragonFire.classList.remove('active');
    if (heroFire) heroFire.classList.remove('active');
    if (heroSprite) heroSprite.classList.remove('hit');
    if (dragonSprite) dragonSprite.classList.remove('hit');
    if (result) result.className = 'battle-result';

    if (outcome === 'victory') {
        if (eyebrow) eyebrow.textContent = 'Round cleared';
        if (badge) badge.innerHTML = '<i class="fas fa-trophy" aria-hidden="true"></i>';
        if (title) title.textContent = 'Victory!';
    } else {
        if (eyebrow) eyebrow.textContent = 'Hit taken';
        if (badge) badge.innerHTML = '<i class="fas fa-shield-alt" aria-hidden="true"></i>';
        if (title) title.textContent = 'Defeat';
    }
    if (msg) msg.textContent = message;
    if (nextBtn) {
        if (nextBtnLabel) {
            nextBtnLabel.textContent = outcome === 'victory' ? 'Continue' : 'Try again';
        }
        nextBtn.onclick = onContinue;
    }
    if (result) result.classList.add(outcome);

    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }

    setTimeout(function () {
        if (outcome === 'defeat') {
            if (dragonFire) dragonFire.classList.add('active');
            setTimeout(function () {
                if (heroSprite) heroSprite.classList.add('hit');
            }, 300);
        } else {
            if (heroFire) heroFire.classList.add('active');
            setTimeout(function () {
                if (dragonSprite) dragonSprite.classList.add('hit');
            }, 300);
        }
    }, 120);

    setTimeout(function () {
        if (modal) {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }
    }, 950);
}

document.addEventListener('submit', async function (e) {
    const form = e.target;
    if (!form || form.id !== 'quest-answer-form') return;
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking Lore...';
    }

    const formData = new FormData(form);

    if (activePower) {
        formData.append('active_power', activePower);
    }

    const modal = document.getElementById('quest-feedback-modal');

    try {
        const response = await fetch(questPlay.submitUrl || '', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (result.success) {
            if (result.new_hp !== undefined) {
                updateHPDisplay(result.new_hp);
            }

            const isCorrect = result.correct !== false;
            const outcome = isCorrect ? 'victory' : 'defeat';
            if (isCorrect) {
                bossCurrentHp = Math.max(0, bossCurrentHp - 1);
                updateBossHPDisplay(bossCurrentHp);
            }

            const wasProtected = activePower && (activePower === 'shield' || activePower === 'revive' || activePower === 'focus');
            const protectedMessage = wasProtected && !isCorrect ? ' (Power protected you from HP loss!)' : '';

            showBattle(outcome, result.message + protectedMessage, () => navigateAfterQuest(result.next_url));
        } else {
            if (result.new_hp !== undefined) {
                updateHPDisplay(result.new_hp);
            }

            showBattle('defeat', result.message, () => {
                if (modal) {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                const qp = document.getElementById('battle-question-panel');
                if (qp) qp.style.display = 'block';
                document.getElementById('dragon-fire')?.classList.remove('active');
                document.getElementById('hero-fire')?.classList.remove('active');
                document.getElementById('hero-sprite')?.classList.remove('hit');
                document.getElementById('dragon-sprite')?.classList.remove('hit');
            });
        }
    } catch (error) {
        console.error('Submission error:', error);
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
});

function heroStatMax(kind) {
    const el = document.getElementById('hero-stat-max');
    if (!el) return 100;
    const raw = kind === 'ap' ? el.dataset.maxAp : el.dataset.maxHp;
    const n = parseInt(raw, 10);
    return Number.isFinite(n) && n > 0 ? n : 100;
}

function updateHPDisplay(newHP) {
    const hpFill = document.querySelector('rect.hp-fill');
    const hpValue = document.querySelector('.js-hp-value');
    const max = heroStatMax('hp');
    const capped = Math.min(Math.max(0, Number(newHP)), max);
    if (hpFill && hpValue) {
        hpFill.setAttribute('width', String(Math.min((capped / max) * 100, 100)));
        hpValue.textContent = String(capped);
    }
    const hudHpValue = document.getElementById('hud-student-hp-value');
    const hudHpFill = document.getElementById('hud-student-hp-fill');
    if (hudHpValue) hudHpValue.textContent = String(capped);
    if (hudHpFill) hudHpFill.style.width = `${Math.min((capped / max) * 100, 100)}%`;
}

function updateAPDisplay(newAP) {
    const apFill = document.querySelector('rect.ap-fill');
    const apValue = document.querySelector('.js-ap-value');
    const max = heroStatMax('ap');
    if (apFill && apValue) {
        apFill.setAttribute('width', String(Math.min((newAP / max) * 100, 100)));
        apValue.textContent = newAP;
    }
}

function updateBossHPDisplay(newBossHP) {
    const hudBossHpValue = document.getElementById('hud-boss-hp-value');
    const hudBossHpFill = document.getElementById('hud-boss-hp-fill');
    if (hudBossHpValue) hudBossHpValue.textContent = String(newBossHP);
    if (hudBossHpFill) {
        const width = Math.min(100, Math.max(0, (newBossHP / bossMaxHp) * 100));
        hudBossHpFill.style.width = `${width}%`;
    }
}
</script>
@endsection
