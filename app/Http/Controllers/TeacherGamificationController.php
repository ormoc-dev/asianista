<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherGamificationController extends Controller
{
    public function index()
    {
        // Dummy Section for now (until integrated with your DB)
        $section = (object)[
            'id' => 1,
            'name' => 'Grade 10 - Ruby',
        ];

        // Example Students Leaderboard
        $students = collect([
            (object)[
                'id' => 1,
                'name' => 'John Dela Cruz',
                'points_sum_value' => 1450,
                'badges_count' => 3,
                'level' => 7,
                'badges' => [
                    (object)['emoji' => '🏅'],
                    (object)['emoji' => '🎯'],
                    (object)['emoji' => '📚'],
                ],
            ],
            (object)[
                'id' => 2,
                'name' => 'Maria Santos',
                'points_sum_value' => 1250,
                'badges_count' => 2,
                'level' => 6,
                'badges' => [
                    (object)['emoji' => '🥈'],
                    (object)['emoji' => '⭐'],
                ],
            ],
            (object)[
                'id' => 3,
                'name' => 'Carlos Reyes',
                'points_sum_value' => 970,
                'badges_count' => 1,
                'level' => 5,
                'badges' => [
                    (object)['emoji' => '🎖️'],
                ],
            ],
        ]);

        // Example Challenges
        $challenges = collect([
            (object)[
                'id' => 1,
                'title' => 'Complete 5 Lessons',
                'points' => 200,
                'description' => 'Finish the first 5 lessons to gain experience points!',
            ],
            (object)[
                'id' => 2,
                'title' => 'Score 90% in Quiz',
                'points' => 300,
                'description' => 'Achieve 90% or higher in any quiz!',
            ],
            (object)[
                'id' => 3,
                'title' => 'Participate in Discussion',
                'points' => 150,
                'description' => 'Post a meaningful response in a class discussion forum.',
            ],
        ]);

        return view('teacher.gamification.index', compact('challenges', 'students', 'section'));
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
        $challenge = (object)[
            'id' => $id,
            'title' => 'Complete 5 Lessons',
            'points' => 200,
            'description' => 'Finish the first five lessons to earn points!',
        ];

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
