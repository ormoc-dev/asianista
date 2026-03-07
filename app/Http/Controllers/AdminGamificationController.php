<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminGamificationController extends Controller
{
    public function index()
    {
        $config = [
            'point_ratio' => \App\Models\Setting::get('gamification_point_ratio', '10 points per lesson'),
            'badges' => json_decode(\App\Models\Setting::get('gamification_badges', json_encode(['Quiz Master', 'Fast Learner', 'Top Scorer'])), true),
            'leaderboard_enabled' => (bool) \App\Models\Setting::get('gamification_leaderboard_enabled', true),
        ];

        return view('admin.gamification.index', compact('config'));
    }

    public function update(Request $request)
    {
        // Save settings to database
        \App\Models\Setting::set('gamification_point_ratio', $request->input('point_ratio'));
        
        $badges = array_map('trim', explode(',', $request->input('badges')));
        \App\Models\Setting::set('gamification_badges', json_encode($badges));
        
        \App\Models\Setting::set('gamification_leaderboard_enabled', $request->has('leaderboard_enabled'));

        return back()->with('success', '✅ Realm Mastery settings updated successfully!');
    }
}
