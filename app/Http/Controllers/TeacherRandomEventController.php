<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RandomEvent;
use App\Models\ActiveEvent;
use App\Models\EventDrawHistory;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class TeacherRandomEventController extends Controller
{
    /**
     * Display the dice roll page for teachers
     */
    public function index()
    {
        $drawHistory = EventDrawHistory::with('randomEvent')
            ->where('teacher_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('teacher.random-events.index', compact('drawHistory'));
    }

    /**
     * Get a random event (for AJAX requests)
     */
    public function drawRandom()
    {
        $event = RandomEvent::getRandomEvent();
        
        if (!$event) {
            return response()->json(['error' => 'No active events found'], 404);
        }

        // Deactivate any previous active events
        ActiveEvent::deactivateAll();

        // Create new active event
        $activeEvent = ActiveEvent::create([
            'random_event_id' => $event->id,
            'teacher_id' => auth()->id(),
            'started_at' => now(),
            'expires_at' => now()->addMinutes(5), // Event lasts 5 minutes
            'is_active' => true,
            'affected_students' => [],
        ]);

        // Save to draw history
        EventDrawHistory::create([
            'random_event_id' => $event->id,
            'teacher_id' => auth()->id(),
            'event_title' => $event->title,
            'event_description' => $event->description,
            'event_type' => $event->event_type,
            'xp_reward' => $event->xp_reward ?? 0,
            'xp_penalty' => $event->xp_penalty ?? 0,
            'target_type' => $event->target_type,
            'effect' => $event->effect,
        ]);

        // Store in session for immediate student display
        Session::put('last_drawn_event', [
            'event_id' => $event->id,
            'drawn_at' => now()->toIso8601String(),
            'active_event_id' => $activeEvent->id,
        ]);

        return response()->json($event);
    }
}
