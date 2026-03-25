@extends('teacher.layouts.app')

@section('title', 'Edit Random Event')
@section('page-title', 'Edit Random Event')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Random Event</h2>
        <a href="{{ route('teacher.random-events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.random-events.update', $randomEvent) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="title">Event Title <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title', $randomEvent->title) }}" required>
                @error('title')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description <span style="color: #ef4444;">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="2" required>{{ old('description', $randomEvent->description) }}</textarea>
                <small style="color: var(--text-muted);">A short flavorful description of the event.</small>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="effect">Effect Description <span style="color: #ef4444;">*</span></label>
                <textarea class="form-control @error('effect') is-invalid @enderror" 
                          id="effect" name="effect" rows="3" required>{{ old('effect', $randomEvent->effect) }}</textarea>
                <small style="color: var(--text-muted);">Explain what happens when this event is triggered.</small>
                @error('effect')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="event_type">Event Type <span style="color: #ef4444;">*</span></label>
                    <select class="form-control @error('event_type') is-invalid @enderror" 
                            id="event_type" name="event_type" required>
                        <option value="">Select Type</option>
                        <option value="positive" {{ old('event_type', $randomEvent->event_type) == 'positive' ? 'selected' : '' }}>Positive (Reward)</option>
                        <option value="negative" {{ old('event_type', $randomEvent->event_type) == 'negative' ? 'selected' : '' }}>Negative (Penalty)</option>
                        <option value="neutral" {{ old('event_type', $randomEvent->event_type) == 'neutral' ? 'selected' : '' }}>Neutral</option>
                        <option value="challenge" {{ old('event_type', $randomEvent->event_type) == 'challenge' ? 'selected' : '' }}>Challenge</option>
                    </select>
                    @error('event_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_type">Target Type <span style="color: #ef4444;">*</span></label>
                    <select class="form-control @error('target_type') is-invalid @enderror" 
                            id="target_type" name="target_type" required>
                        <option value="">Select Target</option>
                        <option value="single" {{ old('target_type', $randomEvent->target_type) == 'single' ? 'selected' : '' }}>Single Player</option>
                        <option value="all" {{ old('target_type', $randomEvent->target_type) == 'all' ? 'selected' : '' }}>All Players</option>
                        <option value="pair" {{ old('target_type', $randomEvent->target_type) == 'pair' ? 'selected' : '' }}>Pair</option>
                        <option value="random" {{ old('target_type', $randomEvent->target_type) == 'random' ? 'selected' : '' }}>Random Selection</option>
                    </select>
                    @error('target_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="xp_reward">XP Reward</label>
                    <input type="number" class="form-control @error('xp_reward') is-invalid @enderror" 
                           id="xp_reward" name="xp_reward" value="{{ old('xp_reward', $randomEvent->xp_reward) }}" min="0">
                    @error('xp_reward')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="xp_penalty">XP Penalty</label>
                    <input type="number" class="form-control @error('xp_penalty') is-invalid @enderror" 
                           id="xp_penalty" name="xp_penalty" value="{{ old('xp_penalty', $randomEvent->xp_penalty) }}" min="0">
                    @error('xp_penalty')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                           id="sort_order" name="sort_order" value="{{ old('sort_order', $randomEvent->sort_order) }}" min="0">
                    <small style="color: var(--text-muted);">Display order</small>
                    @error('sort_order')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" 
                       {{ old('is_active', $randomEvent->is_active) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                <label for="is_active" style="margin: 0;">Active (available for drawing)</label>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Event
                </button>
                <a href="{{ route('teacher.random-events.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
