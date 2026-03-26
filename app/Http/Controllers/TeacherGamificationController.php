<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherGamificationController extends Controller
{
    public function index()
    {
        // Get real challenges from database
        $challenges = Challenge::orderBy('created_at', 'desc')->get();

        // Get students for leaderboard (sorted by XP)
        $students = User::where('role', 'student')
            ->orderBy('xp', 'desc')
            ->take(10)
            ->get();

        return view('teacher.gamification.index', compact('challenges', 'students'));
    }

    public function create()
    {
        return view('teacher.gamification.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        \App\Models\Challenge::create([
            'title' => $request->title,
            'points' => $request->points,
            'description' => $request->description,
        ]);

        return redirect()->route('teacher.gamification.index')
                         ->with('success', '🎉 Challenge created successfully!');
    }

    public function edit($id)
    {
        $challenge = Challenge::findOrFail($id);

        return view('teacher.gamification.edit', compact('challenge'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $challenge = \App\Models\Challenge::findOrFail($id);
        $challenge->update([
            'title' => $request->title,
            'points' => $request->points,
            'description' => $request->description,
        ]);

        return redirect()->route('teacher.gamification.index')
                         ->with('success', '✅ Challenge updated successfully!');
    }

    public function destroy($id)
    {
        \App\Models\Challenge::destroy($id);
        return redirect()->route('teacher.gamification.index')
                         ->with('success', '🗑 Challenge deleted successfully!');
    }
}
