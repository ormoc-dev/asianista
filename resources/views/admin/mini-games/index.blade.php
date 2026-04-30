@extends('admin.layouts.app')

@section('title', 'Mini Games')
@section('page-title', 'Mini Games')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-gamepad" style="color: var(--primary);"></i> Mini Games</h2>
    </div>
    <div class="card-body">
        <p style="margin: 0 0 16px; color: var(--text-secondary);">
            Turn games ON/OFF to control what teachers can access. OFF means hidden for teachers.
        </p>

        <div class="mini-games-grid">
            @foreach($games as $game)
                <div class="mini-game-card">
                    <div class="mini-game-thumb-wrap">
                        <img src="{{ asset($game['image'] ?? 'images/default-pp.png') }}" alt="{{ $game['name'] }}" class="mini-game-thumb">
                    </div>
                    <div class="mini-game-body">
                        <h3 class="mini-game-title">{{ $game['name'] }}</h3>
                        <p class="mini-game-text"><strong>Type:</strong> {{ $game['type'] }}</p>
                        <p class="mini-game-text"><strong>Mechanics:</strong> {{ $game['mechanics'] }}</p>
                        <p class="mini-game-text"><strong>Gamification:</strong> {{ $game['gamification'] }}</p>
                        <p class="mini-game-text"><strong>Best for:</strong> {{ $game['best_for'] }}</p>
                    </div>

                    <div class="mini-game-footer">
                        <div class="mini-game-actions">
                            <span class="badge {{ $game['enabled'] ? 'badge-success' : 'badge-warning' }}">
                                {{ $game['enabled'] ? 'ON' : 'OFF' }}
                            </span>
                            <form method="POST" action="{{ route('admin.mini-games.toggle', $game['slug']) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $game['enabled'] ? 'btn-danger' : 'btn-success' }}">
                                    {{ $game['enabled'] ? 'Turn Off' : 'Turn On' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.mini-games.test', $game['slug']) }}" class="btn btn-sm btn-secondary">
                                Test Game
                            </a>
                            <button type="button" class="btn btn-sm btn-primary" data-edit-toggle="edit-{{ $game['slug'] }}">
                                Edit
                            </button>
                        </div>
                    </div>

                    <form id="edit-{{ $game['slug'] }}" method="POST" action="{{ route('admin.mini-games.update', $game['slug']) }}" enctype="multipart/form-data" class="mini-game-edit-form" hidden>
                        @csrf
                        <div class="mini-game-edit-grid">
                            <div class="mini-game-edit-row">
                                <div>
                                    <label class="form-label">Game Name</label>
                                    <input type="text" name="name" value="{{ old('name', $game['name']) }}" class="form-control" required>
                                </div>
                                <div>
                                    <label class="form-label">Type</label>
                                    <input type="text" name="type" value="{{ old('type', $game['type']) }}" class="form-control" required>
                                </div>
                                <div>
                                    <label class="form-label">Best for</label>
                                    <input type="text" name="best_for" value="{{ old('best_for', $game['best_for']) }}" class="form-control" required>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Mechanics</label>
                                <textarea name="mechanics" class="form-control" rows="2" required>{{ old('mechanics', $game['mechanics']) }}</textarea>
                            </div>
                            <div>
                                <label class="form-label">Gamification</label>
                                <textarea name="gamification" class="form-control" rows="2" required>{{ old('gamification', $game['gamification']) }}</textarea>
                            </div>
                            <div>
                                <label class="form-label">Game Image (optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm">Save Game Details</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .mini-games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 340px));
        gap: 14px;
        justify-content: start;
        align-items: start;
    }
    .mini-game-card {
        width: 340px;
        max-width: 100%;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg-card);
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .mini-game-thumb-wrap { margin: 0; }
    .mini-game-thumb { width: 100%; height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border); }
    .mini-game-body { display: grid; gap: 6px; }
    .mini-game-title { margin: 0; font-size: 0.98rem; }
    .mini-game-text { margin: 0; color: var(--text-secondary); font-size: 0.84rem; line-height: 1.35; }
    .mini-game-footer { border-top: 1px solid var(--border); padding-top: 10px; margin-top: 2px; }
    .mini-game-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .mini-game-edit-form { margin-top: 6px; border-top: 1px solid var(--border); padding-top: 10px; }
    .mini-game-edit-grid { display: grid; gap: 10px; }
    .mini-game-edit-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; }
    @media (max-width: 760px) {
        .mini-games-grid { grid-template-columns: 1fr; }
        .mini-game-card { width: 100%; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-edit-toggle]');
    if (!trigger) return;

    const formId = trigger.getAttribute('data-edit-toggle');
    const form = document.getElementById(formId);
    if (!form) return;

    const isHidden = form.hasAttribute('hidden');
    if (isHidden) {
        form.removeAttribute('hidden');
        trigger.textContent = 'Close Edit';
    } else {
        form.setAttribute('hidden', 'hidden');
        trigger.textContent = 'Edit';
    }
});
</script>
@endpush
@endsection
