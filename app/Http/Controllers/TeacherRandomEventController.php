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

        $students = User::where('role', 'student')
            ->where('status', 'approved')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->orderBy('name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'name', 'email']);

        return view('teacher.random-events.index', compact('drawHistory', 'students'));
    }

    /**
     * Get a random event (for AJAX requests)
     */
    public function drawRandom(Request $request)
    {
        $validated = $request->validate([
            'recipient_mode' => 'required|in:all,random,selected',
            'random_count' => 'nullable|integer|min:1|max:500',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        $event = RandomEvent::getRandomEvent();

        if (!$event) {
            return response()->json(['error' => 'No active events found'], 404);
        }

        $mode = $validated['recipient_mode'];
        $pool = User::where('role', 'student')
            ->where('status', 'approved')
            ->pluck('id')
            ->all();

        $recipientStudentIds = null;

        if ($mode === 'random') {
            $n = (int) ($validated['random_count'] ?? 1);
            if ($n < 1) {
                $n = 1;
            }
            if (count($pool) === 0) {
                return response()->json(['error' => 'There are no approved students to pick from.'], 422);
            }
            $n = min($n, count($pool));
            shuffle($pool);
            $recipientStudentIds = array_values(array_slice($pool, 0, $n));
        } elseif ($mode === 'selected') {
            $requested = collect($validated['student_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (count($requested) === 0) {
                return response()->json(['error' => 'Select at least one student.'], 422);
            }

            $recipientStudentIds = User::where('role', 'student')
                ->where('status', 'approved')
                ->whereIn('id', $requested)
                ->pluck('id')
                ->all();

            if (count($recipientStudentIds) === 0) {
                return response()->json(['error' => 'No valid approved students in your selection.'], 422);
            }
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
            'recipient_mode' => $mode,
            'recipient_student_ids' => $recipientStudentIds,
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
            'recipient_mode' => $mode,
            'recipient_student_ids' => $recipientStudentIds,
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
