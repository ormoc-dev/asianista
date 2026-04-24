@extends('admin.layouts.app')

@section('title', 'Quest map — level positions')
@section('page-title', 'Quest map level positions')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body">
        <p style="color: var(--text-secondary); margin: 0;">
            Click the map to add the next level point. Order is <strong>Level 1 → 2 → 3…</strong> along the path.
            Teachers can add many levels; markers use these points in order (extra levels continue past the last point).
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 12px;">
        <h2 class="card-title" style="margin: 0;">
            @if($mapKey === 'default')
                Default map
            @else
                {{ $mapKey }}
            @endif
        </h2>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.quest-maps.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCopyDefault"><i class="fas fa-copy"></i> Copy from default template</button>
            <button type="button" class="btn btn-secondary btn-sm" id="btnClear"><i class="fas fa-eraser"></i> Clear all</button>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.quest-maps.layout.update') }}" method="POST" id="layoutForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="map_key" value="{{ $mapKey }}">

            <div class="layout-editor-grid">
                <div class="layout-editor-map-col">
                    <p class="form-label" style="margin-bottom: 8px;">Map preview (click to add · drag dots to move)</p>
                    <div style="position: relative; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border); cursor: crosshair; aspect-ratio: 1000 / 600; background: var(--bg-main);">
                        <img src="{{ $imageUrl }}" alt="" id="layoutMapImage" style="width: 100%; height: 100%; object-fit: cover; display: block; vertical-align: top;">
                        <div id="layoutMarkers" style="position: absolute; inset: 0; pointer-events: none;"></div>
                    </div>
                </div>
                <div class="layout-pins-panel">
                    <p class="form-label" style="margin-bottom: 10px;">Points <span style="font-weight: 400; color: var(--text-muted);">(reorder with arrows · edit % or label)</span></p>
                    <div class="layout-pins-scroll" id="pinRowsContainer">
                        <div id="pinTableBody" class="layout-pin-rows"></div>
                    </div>
                    @error('pins')
                        <p style="color: var(--danger); margin-top: 8px;">{{ $message }}</p>
                    @enderror
                    @foreach ($errors->get('pins.*') as $messages)
                        @foreach ($messages as $msg)
                            <p style="color: var(--danger); margin-top: 4px;">{{ $msg }}</p>
                        @endforeach
                    @endforeach
                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save positions</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .layout-editor-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 400px);
        gap: 24px;
        align-items: start;
    }
    .layout-editor-grid > .layout-editor-map-col,
    .layout-editor-grid > .layout-pins-panel {
        min-width: 0;
    }
    .layout-pins-panel .layout-pins-scroll {
        max-height: min(52vh, 520px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 4px 8px 4px 2px;
        margin: 0 -4px;
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
    }
    .layout-pin-rows {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 8px 4px 8px 0;
    }
    .layout-pin-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px;
        box-shadow: var(--shadow);
    }
    .layout-pin-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .layout-pin-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        border-radius: 8px;
        background: #e0e7ff;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.9rem;
    }
    .layout-pin-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-end;
    }
    .layout-pin-actions .btn {
        min-width: 36px;
    }
    .layout-pin-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .layout-pin-fields .layout-pin-field--wide {
        grid-column: 1 / -1;
    }
    .layout-pins-panel .pin-field-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .layout-pins-panel .pin-field-input {
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
    .layout-pins-panel .pin-field-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .layout-pins-panel .pin-field-input::placeholder {
        color: var(--text-muted);
        opacity: 1;
    }
    @media (max-width: 960px) {
        .layout-editor-grid {
            grid-template-columns: 1fr;
        }
        .layout-pins-panel .layout-pins-scroll {
            max-height: none;
        }
    }
    .pin-row-actions { display: flex; gap: 4px; flex-wrap: wrap; }
    .layout-pin-dot {
        position: absolute;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        font-size: 0.7rem;
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
        z-index: 2;
    }
    .layout-pin-dot.is-dragging {
        cursor: grabbing;
        z-index: 30;
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
        transform: translate(-50%, -50%) scale(1.08);
    }
    #layoutMarkers {
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const initialPins = @json($pins);
    const fallbackPins = @json(\App\Models\QuestMapLayout::fallbackPins());
    let pins = Array.isArray(initialPins) && initialPins.length ? initialPins.map(normalizePin) : [];

    function normalizePin(p) {
        return {
            left: Math.round((Number(p.left) || 0) * 100) / 100,
            top: Math.round((Number(p.top) || 0) * 100) / 100,
            name: (p.name != null && String(p.name).trim() !== '') ? String(p.name) : '',
            icon: (p.icon != null && String(p.icon).trim() !== '') ? String(p.icon) : 'fa-map-marker-alt',
        };
    }

    const img = document.getElementById('layoutMapImage');
    const tbody = document.getElementById('pinTableBody');
    const markers = document.getElementById('layoutMarkers');
    const form = document.getElementById('layoutForm');
    let layoutPinPointerDrag = null;

    function getLayoutMapFrame() {
        const img = document.getElementById('layoutMapImage');
        return img ? img.parentElement : null;
    }

    function layoutPinPercentFromClient(clientX, clientY) {
        const frame = getLayoutMapFrame();
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

    function syncLayoutPinFormInputsFromIndex(i) {
        if (!pins[i]) return;
        const leftInp = document.querySelector('#pinTableBody .pin-left[data-i="' + i + '"]');
        const topInp = document.querySelector('#pinTableBody .pin-top[data-i="' + i + '"]');
        if (leftInp) leftInp.value = pins[i].left;
        if (topInp) topInp.value = pins[i].top;
    }

    function onLayoutPinMarkerPointerDown(e) {
        if (e.button !== undefined && e.button !== 0) return;
        const dot = e.currentTarget;
        const idx = parseInt(dot.dataset.pinIndex, 10);
        if (Number.isNaN(idx) || !pins[idx]) return;
        layoutPinPointerDrag = { index: idx, pointerId: e.pointerId, dotEl: dot };
        dot.classList.add('is-dragging');
        dot.setPointerCapture(e.pointerId);
        e.preventDefault();
        e.stopPropagation();
    }

    function onLayoutPinMarkerPointerMove(e) {
        if (!layoutPinPointerDrag || layoutPinPointerDrag.pointerId !== e.pointerId) return;
        const { left, top } = layoutPinPercentFromClient(e.clientX, e.clientY);
        const idx = layoutPinPointerDrag.index;
        pins[idx].left = left;
        pins[idx].top = top;
        const dot = layoutPinPointerDrag.dotEl;
        dot.style.left = left + '%';
        dot.style.top = top + '%';
        syncLayoutPinFormInputsFromIndex(idx);
    }

    function onLayoutPinMarkerPointerUp(e) {
        if (!layoutPinPointerDrag) return;
        if (e.pointerId !== undefined && layoutPinPointerDrag.pointerId !== e.pointerId) return;
        const dot = layoutPinPointerDrag.dotEl;
        try {
            dot.releasePointerCapture(layoutPinPointerDrag.pointerId);
        } catch (err) { /* ignore */ }
        dot.classList.remove('is-dragging');
        layoutPinPointerDrag = null;
    }

    function renderMarkers() {
        markers.innerHTML = '';
        pins.forEach((p, i) => {
            const el = document.createElement('div');
            el.className = 'layout-pin-dot';
            el.dataset.pinIndex = String(i);
            el.textContent = String(i + 1);
            el.setAttribute('title', 'Drag to move');
            el.style.left = p.left + '%';
            el.style.top = p.top + '%';
            el.addEventListener('pointerdown', onLayoutPinMarkerPointerDown);
            el.addEventListener('pointermove', onLayoutPinMarkerPointerMove);
            el.addEventListener('pointerup', onLayoutPinMarkerPointerUp);
            el.addEventListener('pointercancel', onLayoutPinMarkerPointerUp);
            markers.appendChild(el);
        });
    }

    function renderTable() {
        tbody.innerHTML = '';
        if (pins.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'layout-pins-empty';
            empty.style.cssText = 'padding: 24px 16px; text-align: center; color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin: 0; border: 1px dashed var(--border); border-radius: var(--radius-sm); background: var(--bg-card);';
            empty.innerHTML = 'No points yet. <strong>Click the map</strong> to add Level 1, 2, 3…<br>or use <strong>Copy from default template</strong> above.';
            tbody.appendChild(empty);
            renderMarkers();
            return;
        }
        pins.forEach((p, i) => {
            const card = document.createElement('div');
            card.className = 'layout-pin-card';
            card.innerHTML = `
                <div class="layout-pin-card-top">
                    <span class="layout-pin-badge">${i + 1}</span>
                    <div class="layout-pin-actions">
                        <button type="button" class="btn btn-sm btn-secondary pin-up" title="Move up" data-i="${i}" ${i === 0 ? 'disabled' : ''}><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-sm btn-secondary pin-down" title="Move down" data-i="${i}" ${i === pins.length - 1 ? 'disabled' : ''}><i class="fas fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-sm btn-danger pin-del" title="Remove" data-i="${i}"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
                <div class="layout-pin-fields">
                    <div>
                        <label class="pin-field-label">Left %</label>
                        <input type="number" step="0.1" min="0" max="100" class="pin-field-input pin-left" data-i="${i}" value="${p.left}">
                    </div>
                    <div>
                        <label class="pin-field-label">Top %</label>
                        <input type="number" step="0.1" min="0" max="100" class="pin-field-input pin-top" data-i="${i}" value="${p.top}">
                    </div>
                    <div class="layout-pin-field--wide">
                        <label class="pin-field-label">Label</label>
                        <input type="text" class="pin-field-input pin-name" data-i="${i}" value="${escapeAttr(p.name)}" placeholder="e.g. Level ${i + 1}">
                    </div>
                </div>
            `;
            tbody.appendChild(card);
        });

        tbody.querySelectorAll('.pin-left, .pin-top, .pin-name').forEach((inp) => {
            inp.addEventListener('change', onFieldChange);
            inp.addEventListener('input', onFieldChange);
        });
        tbody.querySelectorAll('.pin-up').forEach((b) => b.addEventListener('click', () => movePin(Number(b.dataset.i), -1)));
        tbody.querySelectorAll('.pin-down').forEach((b) => b.addEventListener('click', () => movePin(Number(b.dataset.i), 1)));
        tbody.querySelectorAll('.pin-del').forEach((b) => b.addEventListener('click', () => removePin(Number(b.dataset.i))));
        renderMarkers();
    }

    function escapeAttr(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;');
    }

    function onFieldChange(e) {
        const i = Number(e.target.dataset.i);
        if (Number.isNaN(i) || !pins[i]) return;
        const row = e.target.closest('.layout-pin-card');
        const left = row.querySelector('.pin-left');
        const top = row.querySelector('.pin-top');
        const name = row.querySelector('.pin-name');
        pins[i].left = Math.min(100, Math.max(0, parseFloat(left.value) || 0));
        pins[i].top = Math.min(100, Math.max(0, parseFloat(top.value) || 0));
        pins[i].name = name.value.trim();
        left.value = pins[i].left;
        top.value = pins[i].top;
        renderMarkers();
    }

    function movePin(i, dir) {
        const j = i + dir;
        if (j < 0 || j >= pins.length) return;
        const t = pins[i];
        pins[i] = pins[j];
        pins[j] = t;
        renderTable();
    }

    function removePin(i) {
        pins.splice(i, 1);
        renderTable();
    }

    img.addEventListener('click', function (e) {
        const r = img.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width) * 100;
        const y = ((e.clientY - r.top) / r.height) * 100;
        const left = Math.round(Math.min(100, Math.max(0, x)) * 100) / 100;
        const top = Math.round(Math.min(100, Math.max(0, y)) * 100) / 100;
        pins.push({ left, top, name: 'Level ' + (pins.length + 1), icon: 'fa-map-marker-alt' });
        renderTable();
    });

    document.getElementById('btnCopyDefault').addEventListener('click', function () {
        pins = fallbackPins.map(normalizePin);
        renderTable();
    });

    document.getElementById('btnClear').addEventListener('click', function () {
        if (!pins.length || confirm('Remove all points?')) {
            pins = [];
            renderTable();
        }
    });

    form.addEventListener('submit', function (e) {
        if (!pins.length) {
            e.preventDefault();
            alert('Add at least one point on the map (or use “Copy from default template”).');
            return;
        }
        document.querySelectorAll('input[name^="pins["]').forEach((n) => n.remove());
        pins.forEach((p, i) => {
            ['left', 'top', 'name', 'icon'].forEach((key) => {
                const h = document.createElement('input');
                h.type = 'hidden';
                h.name = `pins[${i}][${key}]`;
                h.value = key === 'left' || key === 'top' ? String(p[key]) : (p[key] || '');
                form.appendChild(h);
            });
        });
    });

    renderTable();
})();
</script>
@endpush
@endsection
