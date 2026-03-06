<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminGamificationController extends Controller
{
    public function index()
    {
        $config = [
            'point_ratio' => '10 points per lesson',
            'badges' => ['Quiz Master', 'Fast Learner', 'Top Scorer'],
            'leaderboard_enabled' => true,
        ];

        return view('admin.gamification.index', compact('config'));
    }

    public function update(Request $request)
    {
        // Logic to save new config
        return back()->with('success', 'Gamification settings updated successfully.');
    }
}
