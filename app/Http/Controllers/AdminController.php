<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MinigameSetting;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard() {
        $totalUsers = \App\Models\User::count();
        $teachersCount = \App\Models\User::where('role', 'teacher')->count();
        $studentsCount = \App\Models\User::where('role', 'student')->count();
        $pendingApprovals = \App\Models\User::where('status', 'pending')->count();
        $totalLessons = \App\Models\Lesson::count();
        
        $recentUsers = \App\Models\User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.home', compact(
            'totalUsers', 
            'teachersCount', 
            'studentsCount', 
            'pendingApprovals', 
            'totalLessons',
            'recentUsers'
        ));
    }

    public function users() {
        return view('admin.sections.users');
    }

    public function lessons() {
        $lessons = \App\Models\Lesson::with('teacher')->orderBy('created_at', 'desc')->get();
        return view('admin.sections.lessons', compact('lessons'));
    }

    public function gamification() {
        return view('admin.sections.gamification');
    }

    public function aiManagement() {
        return view('admin.ai-management.index');
    }

    public function miniGames() {
        $games = $this->getMiniGamesCatalog();
        return view('admin.mini-games.index', compact('games'));
    }

    public function toggleMiniGame(Request $request, string $slug) {
        $games = $this->getMiniGamesCatalog();

        $game = MinigameSetting::where('slug', $slug)->firstOrFail();
        $game->is_enabled = !$game->is_enabled;
        $game->save();

        $status = $game->is_enabled ? 'enabled' : 'disabled';
        return redirect()->route('admin.mini-games')->with('success', "{$game->name} is now {$status} for teachers.");
    }

    public function updateMiniGame(Request $request, string $slug) {
        $game = MinigameSetting::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'mechanics' => 'required|string|max:600',
            'gamification' => 'required|string|max:600',
            'best_for' => 'required|string|max:200',
            'image' => 'nullable|image|max:4096',
        ]);

        $game->name = $validated['name'];
        $game->type = $validated['type'];
        $game->mechanics = $validated['mechanics'];
        $game->gamification = $validated['gamification'];
        $game->best_for = $validated['best_for'];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'mini-game-' . $slug . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/mini-games/uploads');

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $game->image = 'images/mini-games/uploads/' . $filename;
        }

        $game->save();

        return redirect()->route('admin.mini-games')->with('success', "{$game->name} has been updated.");
    }

    public function testMiniGame(string $slug) {
        $game = MinigameSetting::where('slug', $slug)->firstOrFail();

        return view('mini-games.speed-typing-race', [
            'layout' => 'admin.layouts.app',
            'game' => $this->gameToArray($game),
            'isTestMode' => true,
        ]);
    }

    public function data() {
        return view('admin.sections.data');
    }

    public function security() {
        return view('admin.sections.security');
    }

    private function getMiniGamesCatalog(): array
    {
        $this->ensureMiniGamesSeeded();

        return MinigameSetting::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (MinigameSetting $game) => [$game->slug => $this->gameToArray($game)])
            ->toArray();
    }

    private function gameToArray(MinigameSetting $game): array
    {
        return [
            'slug' => $game->slug,
            'name' => $game->name,
            'image' => $game->image,
            'type' => $game->type,
            'mechanics' => $game->mechanics,
            'gamification' => $game->gamification,
            'best_for' => $game->best_for,
            'enabled' => $game->is_enabled,
        ];
    }

    private function ensureMiniGamesSeeded(): void
    {
        $default = [
            'speed-typing-race' => [
                'slug' => 'speed-typing-race',
                'name' => 'Speed Typing Race',
                'image' => 'images/mini-games/speed-typing-race.svg',
                'type' => 'Skill-based',
                'mechanics' => 'Students race to type a given paragraph accurately.',
                'gamification' => 'Leaderboard ranking, accuracy score, and speed score.',
                'best_for' => 'ICT development',
                'enabled' => false,
            ],
        ];

        foreach ($default as $slug => $config) {
            MinigameSetting::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $config['name'],
                    'image' => $config['image'],
                    'type' => $config['type'],
                    'mechanics' => $config['mechanics'],
                    'gamification' => $config['gamification'],
                    'best_for' => $config['best_for'],
                    'is_enabled' => $config['enabled'],
                ]
            );
        }
    }
}
