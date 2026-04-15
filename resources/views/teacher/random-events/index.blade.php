@extends('teacher.layouts.app')

@section('title', 'Random Events')
@section('page-title', 'Random Events')

@section('content')
<div class="event-draw-container">
    <!-- Dice Roll Section -->
    <div class="dice-section">
        <div class="dice-card">
            <h2 class="dice-title">Draw a Random Event</h2>
            <p class="dice-subtitle">Click the dice to draw a random event for your class</p>
            
            <div class="dice-container">
                <div class="dice" id="dice">
                    <div class="dice-face front">
                        <span class="dice-dot"></span>
                    </div>
                    <div class="dice-face back">
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                    </div>
                    <div class="dice-face right">
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                    </div>
                    <div class="dice-face left">
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                    </div>
                    <div class="dice-face top">
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                    </div>
                    <div class="dice-face bottom">
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                        <span class="dice-dot"></span>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-success btn-lg draw-btn" id="drawBtn">
                <i class="fas fa-dice"></i> Draw Event
            </button>
        </div>
    </div>
</div>

<!-- Event Result Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-scroll"></i> Event Drawn!</h3>
            <button onclick="closeEventModal()" class="btn-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="eventModalBody">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<!-- Draw History Section -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-history"></i> Draw History</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Target</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drawHistory as $draw)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $draw->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $draw->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $draw->event_title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($draw->event_description, 50) }}</div>
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
                            <span class="badge badge-{{ $typeColors[$draw->event_type] ?? 'info' }}">
                                {{ $typeLabels[$draw->event_type] ?? $draw->event_type }}
                            </span>
                        </td>
                        <td>
                            @php
                                $targetLabels = [
                                    'single' => 'Single',
                                    'all' => 'All Players',
                                    'pair' => 'Pair',
                                    'random' => 'Random'
                                ];
                            @endphp
                            <span class="badge badge-purple">{{ $targetLabels[$draw->target_type] ?? $draw->target_type }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">
                            <div style="color: var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                No events drawn yet. Click the dice to draw your first event!
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($drawHistory->hasMorePages())
        <div style="padding: 16px; border-top: 1px solid var(--border);">
            {{ $drawHistory->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.event-draw-container {
    display: flex;
    justify-content: center;
    margin-bottom: 24px;
}

.dice-section {
    width: 100%;
    max-width: 500px;
}

.dice-card {
  
    text-align: center;
    
}

.dice-title {
    color: #0d0d0dff;
    font-size: 1.8rem;
    margin-bottom: 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.dice-subtitle {
    color: rgba(14, 14, 14, 0.8);
    margin-bottom: 30px;
}

.dice-container {
    perspective: 600px;
    margin-bottom: 30px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dice {
    width: 80px;
    height: 80px;
    position: relative;
    transform-style: preserve-3d;
    transform: rotateX(-20deg) rotateY(20deg);
    transition: transform 0.1s;
    cursor: pointer;
}

.dice.rolling {
    animation: roll 1s ease-out;
}

@keyframes roll {
    0% { transform: rotateX(0deg) rotateY(0deg); }
    25% { transform: rotateX(360deg) rotateY(180deg); }
    50% { transform: rotateX(720deg) rotateY(360deg); }
    75% { transform: rotateX(1080deg) rotateY(540deg); }
    100% { transform: rotateX(1440deg) rotateY(720deg); }
}

.dice-face {
    position: absolute;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    padding: 10px;
    gap: 8px;
}

.dice-face.front { transform: translateZ(40px); }
.dice-face.back { transform: rotateY(180deg) translateZ(40px); }
.dice-face.right { transform: rotateY(90deg) translateZ(40px); }
.dice-face.left { transform: rotateY(-90deg) translateZ(40px); }
.dice-face.top { transform: rotateX(90deg) translateZ(40px); }
.dice-face.bottom { transform: rotateX(-90deg) translateZ(40px); }

.dice-dot {
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 50%;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
}

.draw-btn {
    padding: 14px 40px;
    font-size: 1.1rem;
    border-radius: 30px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
    transition: all 0.3s ease;
}

.draw-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.draw-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Modal Styles */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.modal.show {
    display: flex;
}

.modal-content {
    background: transparent;
    max-width: 500px;
    width: 90%;
    animation: modalPop 0.3s ease-out;
}

@keyframes modalPop {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.modal-header {
    display: none;
}

.modal-body {
    padding: 0;
}

.btn-close {
    background: rgba(255,255,255,0.2);
    border: none;
    font-size: 1rem;
    cursor: pointer;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Scroll Paper Styles */
.event-card-drawn {
    position: relative;
    max-width: 450px;
    margin: 0 auto;
}

.scroll-draw-top,
.scroll-draw-bottom {
    height: 40px;
    background: linear-gradient(180deg, #d4a574 0%, #c49a6c 50%, #b08d5f 100%);
    border-radius: 20px;
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
    width: 30px;
    height: 50px;
    background: linear-gradient(90deg, #8b6914 0%, #a67c2e 50%, #8b6914 100%);
    border-radius: 0 0 15px 15px;
    top: 0;
    box-shadow: inset 0 -3px 6px rgba(0,0,0,0.3);
}

.scroll-draw-top::before,
.scroll-draw-bottom::before {
    left: -10px;
}

.scroll-draw-top::after,
.scroll-draw-bottom::after {
    right: -10px;
}

.scroll-draw-bottom::before,
.scroll-draw-bottom::after {
    border-radius: 15px 15px 0 0;
    top: auto;
    bottom: 0;
}

.scroll-draw-paper {
    background: linear-gradient(180deg, #f5e6d3 0%, #f0dcc0 50%, #ebd5b3 100%);
    padding: 40px 45px;
    margin: -5px 10px;
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
    font-size: 1.8rem;
    color: #4a3728;
    text-align: center;
    margin-bottom: 15px;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
}

.scroll-draw-description {
    font-style: italic;
    color: #6b5344;
    text-align: center;
    margin-bottom: 25px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.scroll-draw-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent, #c4a77d, transparent);
    margin: 25px 0;
}

.scroll-draw-effect {
    text-align: center;
    color: #4a3728;
    font-size: 1.1rem;
    line-height: 1.6;
    font-weight: 500;
}

.scroll-draw-xp {
    text-align: center;
    margin-top: 25px;
    font-size: 1.4rem;
    font-weight: 700;
}

.scroll-draw-xp .xp-reward {
    color: #16a34a;
}

.scroll-draw-xp .xp-penalty {
    color: #dc2626;
}

.scroll-draw-footer {
    text-align: center;
    margin-top: 20px;
}

.scroll-draw-footer .btn {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #fff;
    padding: 12px 40px;
    border-radius: 25px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
}

.scroll-draw-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.close-btn-container {
    position: absolute;
    top: -50px;
    right: 0;
}
</style>

<script>
function drawRandomEvent() {
    const dice = document.getElementById('dice');
    const drawBtn = document.getElementById('drawBtn');
    
    // Disable button and start dice animation
    drawBtn.disabled = true;
    dice.classList.add('rolling');
    
    fetch('{{ route("teacher.random-events.draw") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(event => {
        // Wait for dice animation to complete
        setTimeout(() => {
            dice.classList.remove('rolling');
            showEventModal(event);
            drawBtn.disabled = false;
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        dice.classList.remove('rolling');
        drawBtn.disabled = false;
        alert('Failed to draw random event. Please try again.');
    });
}

function showEventModal(event) {
    const modalBody = document.getElementById('eventModalBody');

    modalBody.innerHTML = `
        <div class="event-card-drawn">
            <div class="close-btn-container">
                <button onclick="closeEventModal()" class="btn-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="scroll-draw-top"></div>
            <div class="scroll-draw-paper">
                <h2 class="scroll-draw-title">${event.title}</h2>
                <div class="scroll-draw-description">${event.description}</div>
                <div class="scroll-draw-divider"></div>
                <div class="scroll-draw-effect">${event.effect}</div>
                <div class="scroll-draw-footer">
                    <button onclick="closeEventModal()" class="btn">
                        <i class="fas fa-check"></i> Done
                    </button>
                </div>
            </div>
            <div class="scroll-draw-bottom"></div>
        </div>
    `;
    
    const modal = document.getElementById('eventModal');
    modal.classList.add('show');
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('show');
    
    // Reload page to show new history
    window.location.reload();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const drawBtn = document.getElementById('drawBtn');
    const eventModal = document.getElementById('eventModal');
    
    if (drawBtn) {
        drawBtn.addEventListener('click', function(e) {
            e.preventDefault();
            drawRandomEvent();
        });
    }
    
    if (eventModal) {
        eventModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEventModal();
            }
        });
    }
});
</script>
@endsection
