<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActiveEvent;
use App\Models\RandomEvent;
use Illuminate\Support\Facades\Session;

class StudentEventController extends Controller
{
    /**
     * Check for new random events (polling endpoint)
     */
    public function checkNewEvent()
    {
        $studentId = auth()->id();
        
        // Get current active event
        $activeEvent = ActiveEvent::getCurrentActive();
        
        if (!$activeEvent) {
            return response()->json(['has_event' => false]);
        }
        
        // Check if student has already seen this event
        $seenEvents = Session::get('seen_events', []);
        $eventKey = 'event_' . $activeEvent->id;
        
        if (in_array($eventKey, $seenEvents)) {
            return response()->json(['has_event' => false]);
        }
        
        // Mark as seen
        $seenEvents[] = $eventKey;
        Session::put('seen_events', $seenEvents);
        
        // Add student to affected list
        $affected = $activeEvent->affected_students ?? [];
        if (!in_array($studentId, $affected)) {
            $affected[] = $studentId;
            $activeEvent->update(['affected_students' => $affected]);
        }
        
        return response()->json([
            'has_event' => true,
            'event' => [
                'id' => $activeEvent->randomEvent->id,
                'title' => $activeEvent->randomEvent->title,
                'description' => $activeEvent->randomEvent->description,
                'effect' => $activeEvent->randomEvent->effect,
                'xp_reward' => $activeEvent->randomEvent->xp_reward,
                'xp_penalty' => $activeEvent->randomEvent->xp_penalty,
                'target_type' => $activeEvent->randomEvent->target_type,
                'event_type' => $activeEvent->randomEvent->event_type,
            ],
            'active_event_id' => $activeEvent->id,
            'expires_at' => $activeEvent->expires_at,
        ]);
    }
    
    /**
     * Mark event as acknowledged by student
     */
    public function acknowledgeEvent(Request $request)
    {
        $activeEventId = $request->input('active_event_id');
        
        $activeEvent = ActiveEvent::find($activeEventId);
        if ($activeEvent) {
            $acknowledged = $activeEvent->affected_students ?? [];
            $studentId = auth()->id();
            
            if (!in_array($studentId, $acknowledged)) {
                $acknowledged[] = $studentId;
                $activeEvent->update(['affected_students' => $acknowledged]);
            }
        }
        
        return response()->json(['success' => true]);
    }
}
