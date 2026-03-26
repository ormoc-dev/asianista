@extends('admin.layouts.app')

@section('title', 'View Random Event')
@section('page-title', 'View Random Event')

@section('content')
<style>
    .scroll-container {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    .scroll-top,
    .scroll-bottom {
        height: 40px;
        background: linear-gradient(180deg, #d4a574 0%, #c49a6c 50%, #b08d5f 100%);
        border-radius: 20px;
        position: relative;
        box-shadow: 
            inset 0 2px 4px rgba(255,255,255,0.3),
            inset 0 -2px 4px rgba(0,0,0,0.2),
            0 4px 8px rgba(0,0,0,0.3);
    }
    
    .scroll-top::before,
    .scroll-top::after,
    .scroll-bottom::before,
    .scroll-bottom::after {
        content: '';
        position: absolute;
        width: 30px;
        height: 50px;
        background: linear-gradient(90deg, #8b6914 0%, #a67c2e 50%, #8b6914 100%);
        border-radius: 0 0 15px 15px;
        top: 0;
        box-shadow: inset 0 -3px 6px rgba(0,0,0,0.3);
    }
    
    .scroll-top::before,
    .scroll-bottom::before {
        left: -10px;
    }
    
    .scroll-top::after,
    .scroll-bottom::after {
        right: -10px;
    }
    
    .scroll-bottom::before,
    .scroll-bottom::after {
        border-radius: 15px 15px 0 0;
        top: auto;
        bottom: 0;
    }
    
    .scroll-paper {
        background: linear-gradient(180deg, #f5e6d3 0%, #f0dcc0 50%, #ebd5b3 100%);
        padding: 40px 50px;
        margin: -5px 10px;
        position: relative;
        box-shadow: 
            inset 0 0 60px rgba(139, 105, 20, 0.1),
            0 4px 20px rgba(0,0,0,0.15);
    }
    
    .scroll-paper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 28px,
                rgba(139, 105, 20, 0.03) 28px,
                rgba(139, 105, 20, 0.03) 29px
            );
        pointer-events: none;
    }
    
    .scroll-title {
        font-family: 'Georgia', 'Times New Roman', serif;
        font-size: 2rem;
        color: #4a3728;
        text-align: center;
        margin-bottom: 15px;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
    }
    
    .scroll-description {
        font-style: italic;
        color: #6b5344;
        text-align: center;
        margin-bottom: 25px;
        font-size: 0.95rem;
    }
    
    .scroll-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #c4a77d, transparent);
        margin: 25px 0;
    }
    
    .scroll-effect {
        text-align: center;
        color: #4a3728;
        font-size: 1.1rem;
        line-height: 1.6;
        font-weight: 500;
    }
    
    .scroll-xp {
        text-align: center;
        margin-top: 25px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .scroll-xp .xp-reward {
        color: #22c55e;
    }
    
    .scroll-xp .xp-penalty {
        color: #ef4444;
    }
</style>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Event Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.random-events.edit', $randomEvent) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.random-events.destroy', $randomEvent) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('admin.random-events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="scroll-container">
            <!-- Scroll Top -->
            <div class="scroll-top"></div>
            
            <!-- Scroll Paper -->
            <div class="scroll-paper">
                <h2 class="scroll-title">{{ $randomEvent->title }}</h2>
                
                <p class="scroll-description">{{ $randomEvent->description }}</p>
                
                <div class="scroll-divider"></div>
                
                <div class="scroll-effect">
                    {{ $randomEvent->effect }}
                </div>
                
                <div class="scroll-xp">
                    @if($randomEvent->xp_reward > 0)
                        <span class="xp-reward">+{{ $randomEvent->xp_reward }} XP</span>
                    @elseif($randomEvent->xp_penalty > 0)
                        <span class="xp-penalty">-{{ $randomEvent->xp_penalty }} XP</span>
                    @endif
                </div>
            </div>
            
            <!-- Scroll Bottom -->
            <div class="scroll-bottom"></div>
        </div>

        <!-- Event Details -->
        <div style="margin-top: 30px; background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid var(--border);">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Event Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Event Type</label>
                    @php
                        $typeColors = [
                            'positive' => '#10b981',
                            'negative' => '#ef4444',
                            'neutral' => '#3b82f6',
                            'challenge' => '#f59e0b'
                        ];
                        $typeLabels = [
                            'positive' => 'Positive (Reward)',
                            'negative' => 'Negative (Penalty)',
                            'neutral' => 'Neutral',
                            'challenge' => 'Challenge'
                        ];
                    @endphp
                    <span style="font-weight: 600; color: {{ $typeColors[$randomEvent->event_type] ?? '#666' }};">
                        {{ $typeLabels[$randomEvent->event_type] ?? $randomEvent->event_type }}
                    </span>
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Target Type</label>
                    @php
                        $targetLabels = [
                            'single' => 'Single Player',
                            'all' => 'All Players',
                            'pair' => 'Pair',
                            'random' => 'Random Selection'
                        ];
                    @endphp
                    <span style="font-weight: 600;">{{ $targetLabels[$randomEvent->target_type] ?? $randomEvent->target_type }}</span>
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Status</label>
                    <span style="font-weight: 600; color: {{ $randomEvent->is_active ? '#10b981' : '#94a3b8' }};">
                        {{ $randomEvent->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Sort Order</label>
                    <span style="font-weight: 600;">{{ $randomEvent->sort_order }}</span>
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Created</label>
                    <span style="font-weight: 600;">{{ $randomEvent->created_at->format('M d, Y - h:i A') }}</span>
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Last Updated</label>
                    <span style="font-weight: 600;">{{ $randomEvent->updated_at->format('M d, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
