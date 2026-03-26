<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RandomEvent;

class AdminRandomEventController extends Controller
{
    /**
     * Display a listing of random events
     */
    public function index()
    {
        $events = RandomEvent::orderBy('sort_order')->get();
        return view('admin.random-events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        return view('admin.random-events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'effect' => 'required|string',
            'xp_reward' => 'nullable|integer|min:0',
            'xp_penalty' => 'nullable|integer|min:0',
            'target_type' => 'required|in:single,all,pair,random',
            'event_type' => 'required|in:positive,negative,neutral,challenge',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['xp_reward'] = $validated['xp_reward'] ?? 0;
        $validated['xp_penalty'] = $validated['xp_penalty'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        RandomEvent::create($validated);

        return redirect()->route('admin.random-events.index')
            ->with('success', 'Random event created successfully!');
    }

    /**
     * Display the specified event
     */
    public function show(RandomEvent $randomEvent)
    {
        return view('admin.random-events.show', compact('randomEvent'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(RandomEvent $randomEvent)
    {
        return view('admin.random-events.edit', compact('randomEvent'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, RandomEvent $randomEvent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'effect' => 'required|string',
            'xp_reward' => 'nullable|integer|min:0',
            'xp_penalty' => 'nullable|integer|min:0',
            'target_type' => 'required|in:single,all,pair,random',
            'event_type' => 'required|in:positive,negative,neutral,challenge',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['xp_reward'] = $validated['xp_reward'] ?? 0;
        $validated['xp_penalty'] = $validated['xp_penalty'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $randomEvent->update($validated);

        return redirect()->route('admin.random-events.index')
            ->with('success', 'Random event updated successfully!');
    }

    /**
     * Remove the specified event
     */
    public function destroy(RandomEvent $randomEvent)
    {
        $randomEvent->delete();

        return redirect()->route('admin.random-events.index')
            ->with('success', 'Random event deleted successfully!');
    }

    /**
     * Toggle event active status
     */
    public function toggleActive(RandomEvent $randomEvent)
    {
        $randomEvent->update(['is_active' => !$randomEvent->is_active]);

        return redirect()->route('admin.random-events.index')
            ->with('success', 'Event status updated!');
    }
}
