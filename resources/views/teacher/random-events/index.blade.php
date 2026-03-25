@extends('teacher.layouts.app')

@section('title', 'Random Events')
@section('page-title', 'Random Events')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">30 Random Event Cards</h2>
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-success" id="drawBtn" style="cursor: pointer; position: relative; z-index: 100; pointer-events: auto;">
                <i class="fas fa-dice"></i> Draw Random Event
            </button>
            <a href="{{ route('teacher.random-events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Event
            </a>
        </div>
    </div>
    <div class="card-body">
        <p style="color: var(--text-muted); margin-bottom: 0;">
            Manage random events that can be triggered during class. Teachers can draw random events to add excitement to lessons.
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 20px;">
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table" id="eventsTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Target</th>
                        <th>XP</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td>{{ $event->sort_order }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $event->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($event->description, 60) }}</div>
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'positive' => 'success',
                                    'negative' => 'danger',
                                    'neutral' => 'info',
                                    'challenge' => 'warning'
                                ];
                                $typeLabels = [
                                    'positive' => 'Positive',
                                    'negative' => 'Negative',
                                    'neutral' => 'Neutral',
                                    'challenge' => 'Challenge'
                                ];
                            @endphp
                            <span class="badge badge-{{ $typeColors[$event->event_type] ?? 'info' }}">
                                {{ $typeLabels[$event->event_type] ?? $event->event_type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $targetLabels = [
                                    'single' => 'Single Player',
                                    'all' => 'All Players',
                                    'pair' => 'Pair',
                                    'random' => 'Random'
                                ];
                            @endphp
                            <span class="badge badge-purple">{{ $targetLabels[$event->target_type] ?? $event->target_type }}</span>
                        </td>
                        <td>
                            @if($event->xp_reward > 0)
                                <span style="color: #10b981; font-weight: 600;">+{{ $event->xp_reward }}</span>
                            @elseif($event->xp_penalty > 0)
                                <span style="color: #ef4444; font-weight: 600;">-{{ $event->xp_penalty }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('teacher.random-events.toggle', $event) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $event->is_active ? 'btn-success' : 'btn-secondary' }}" style="min-width: 70px;">
                                    {{ $event->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('teacher.random-events.show', $event) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teacher.random-events.edit', $event) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('teacher.random-events.destroy', $event) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <div style="color: var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                No random events found. <a href="{{ route('teacher.random-events.create') }}">Create one</a> or run the seeder.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Random Event Draw Modal -->
<div id="drawModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="fas fa-dice"></i> Random Event Drawn!</h3>
            <button onclick="closeDrawModal()" class="btn-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="drawModalBody">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.modal.show {
    display: flex;
}
.modal-content {
    background: white;
    border-radius: 16px;
    max-width: 500px;
    width: 90%;
    animation: modalPop 0.3s ease-out;
}
@keyframes modalPop {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}
.modal-header h3 {
    margin: 0;
    color: var(--primary);
}
.modal-body {
    padding: 24px;
}
.btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #94a3b8;
}
.event-card-drawn {
    position: relative;
    max-width: 450px;
    margin: 0 auto;
}

.scroll-draw-top,
.scroll-draw-bottom {
    height: 35px;
    background: linear-gradient(180deg, #d4a574 0%, #c49a6c 50%, #b08d5f 100%);
    border-radius: 17px;
    position: relative;
    box-shadow: 
        inset 0 2px 4px rgba(255,255,255,0.3),
        inset 0 -2px 4px rgba(0,0,0,0.2),
        0 4px 8px rgba(0,0,0,0.3);
}

.scroll-draw-top::before,
.scroll-draw-top::after,
.scroll-draw-bottom::before,
.scroll-draw-bottom::after {
    content: '';
    position: absolute;
    width: 25px;
    height: 45px;
    background: linear-gradient(90deg, #8b6914 0%, #a67c2e 50%, #8b6914 100%);
    border-radius: 0 0 12px 12px;
    top: 0;
    box-shadow: inset 0 -3px 6px rgba(0,0,0,0.3);
}

.scroll-draw-top::before,
.scroll-draw-bottom::before {
    left: -8px;
}

.scroll-draw-top::after,
.scroll-draw-bottom::after {
    right: -8px;
}

.scroll-draw-bottom::before,
.scroll-draw-bottom::after {
    border-radius: 12px 12px 0 0;
    top: auto;
    bottom: 0;
}

.scroll-draw-paper {
    background: linear-gradient(180deg, #f5e6d3 0%, #f0dcc0 50%, #ebd5b3 100%);
    padding: 35px 40px;
    margin: -5px 8px;
    position: relative;
    box-shadow: 
        inset 0 0 60px rgba(139, 105, 20, 0.1),
        0 4px 20px rgba(0,0,0,0.15);
}

.scroll-draw-paper::before {
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

.scroll-draw-title {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 1.6rem;
    color: #4a3728;
    text-align: center;
    margin-bottom: 12px;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
}

.scroll-draw-description {
    font-style: italic;
    color: #6b5344;
    text-align: center;
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.scroll-draw-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent, #c4a77d, transparent);
    margin: 20px 0;
}

.scroll-draw-effect {
    text-align: center;
    color: #4a3728;
    font-size: 1rem;
    line-height: 1.5;
    font-weight: 500;
}

.scroll-draw-xp {
    text-align: center;
    margin-top: 20px;
    font-size: 1.2rem;
    font-weight: 700;
}

.scroll-draw-xp .xp-reward {
    color: #22c55e;
}

.scroll-draw-xp .xp-penalty {
    color: #ef4444;
}
</style>

<script>
function drawRandomEvent() {
    fetch('{{ route("teacher.random-events.draw") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(event => {
        const modalBody = document.getElementById('drawModalBody');
        const xpDisplay = event.xp_reward > 0 
            ? `<span style="color: #10b981;">+${event.xp_reward} XP</span>`
            : event.xp_penalty > 0
                ? `<span style="color: #ef4444;">-${event.xp_penalty} XP</span>`
                : '<span style="color: #94a3b8;">No XP Change</span>';
        
        const xpClass = event.xp_reward > 0 ? 'xp-reward' : (event.xp_penalty > 0 ? 'xp-penalty' : '');
        
        modalBody.innerHTML = `
            <div class="event-card-drawn">
                <div class="scroll-draw-top"></div>
                <div class="scroll-draw-paper">
                    <h2 class="scroll-draw-title">${event.title}</h2>
                    <div class="scroll-draw-description">${event.description}</div>
                    <div class="scroll-draw-divider"></div>
                    <div class="scroll-draw-effect">${event.effect}</div>
                    <div class="scroll-draw-xp ${xpClass}">${xpDisplay}</div>
                </div>
                <div class="scroll-draw-bottom"></div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button onclick="closeDrawModal()" class="btn btn-primary" style="padding: 12px 40px; border-radius: 25px;">
                    NEXT
                </button>
            </div>
        `;
        const modal = document.getElementById('drawModal');
        modal.style.display = 'flex';
        modal.classList.add('show');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to draw random event. Error: ' + error.message);
    });
}

function closeDrawModal() {
    const modal = document.getElementById('drawModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
}

// Close modal on outside click
document.addEventListener('DOMContentLoaded', function() {
    const drawModal = document.getElementById('drawModal');
    const drawBtn = document.getElementById('drawBtn');
    
    if (drawModal) {
        drawModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDrawModal();
            }
        });
    }
    
    if (drawBtn) {
        drawBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            drawRandomEvent();
        });
    }
});
</script>
@endsection
