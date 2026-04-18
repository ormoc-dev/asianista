<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentGamificationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $stats = [
            'points' => (int) ($user->xp ?? 0),
            'level' => (int) ($user->level ?? 1),
            'badges' => json_decode(\App\Models\Setting::get('gamification_badges', json_encode(['Quiz Master', 'Fast Learner', 'Top Scorer'])), true) ?: [],
            'next_level' => max(2, (int) ($user->level ?? 1) + 1),
            'progress' => min(100, (int) (($user->xp ?? 0) % 100)),
        ];

        $leaderboard = User::query()
            ->where('role', 'student')
            ->when($user->grade_id, fn ($q) => $q->where('grade_id', $user->grade_id))
            ->when($user->section_id, fn ($q) => $q->where('section_id', $user->section_id))
            ->orderByDesc('xp')
            ->take(10)
            ->get()
            ->map(fn (User $u) => [
                'name' => trim(($u->first_name ?? '').' '.($u->last_name ?? '')) ?: ($u->name ?? 'Student'),
                'points' => (int) ($u->xp ?? 0),
            ])
            ->all();

        $visibleChallenges = Challenge::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn (Challenge $c) => $c->isVisibleToStudent($user));

        $challenges = $visibleChallenges->map(fn (Challenge $c) => [
            'title' => $c->title,
            'status' => 'Available',
            'points' => $c->points,
        ])->all();

        return view('student.gamification.index', compact('stats', 'leaderboard', 'challenges'));
    }
}
