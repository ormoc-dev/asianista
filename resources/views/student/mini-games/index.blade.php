@extends('student.dashboard')

@section('content')
<div class="card" style="background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px;">
    <h2 style="margin: 0 0 14px; color: #1e293b;">Mini Games</h2>
    @if($assignments->isEmpty())
        <p style="margin: 0; color: #64748b;">No mini games are active for your grade and section right now.</p>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
            @foreach($assignments as $assignment)
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #f8fafc;">
                    <img src="{{ asset($assignment->game->image ?? 'images/default-pp.png') }}" alt="{{ $assignment->game->name }}" style="width: 100%; height: 130px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <h3 style="margin: 10px 0 6px; font-size: 1rem;">{{ $assignment->game->name }}</h3>
                    <p style="margin: 0 0 6px; font-size: 0.83rem; color: #475569;"><strong>Teacher:</strong> {{ $assignment->teacher->name ?? 'Your Teacher' }}</p>
                    <p style="margin: 0 0 6px; font-size: 0.83rem; color: #475569;"><strong>Gamification:</strong> {{ $assignment->game->gamification }}</p>
                    <div style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #334155; font-size: 0.85rem; line-height: 1.5;">
                        {{ $assignment->paragraph }}
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="{{ route('student.mini-games.play', $assignment) }}" class="btn btn-primary btn-sm" style="text-decoration:none;">
                            Start Game
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
