<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentGamificationController extends Controller
{
    public function index()
    {
        // Example gamification data
        $stats = [
            'points' => 1200,
            'level' => 5,
            'badges' => ['Fast Learner', 'Quiz Master', 'Consistency Hero'],
            'next_level' => 6,
            'progress' => 70,
        ];

        $leaderboard = [
            ['name' => 'Alice', 'points' => 1500],
            ['name' => 'You', 'points' => 1200],
            ['name' => 'John', 'points' => 900],
        ];

        $challenges = [
            ['title' => 'Complete 5 Lessons', 'status' => 'Unlocked'],
            ['title' => 'Score 90% in Quiz', 'status' => 'Locked'],
        ];

        return view('student.gamification.index', compact('stats', 'leaderboard', 'challenges'));
    }
}
