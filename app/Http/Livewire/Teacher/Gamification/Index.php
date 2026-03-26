<?php

namespace App\Http\Livewire\Teacher\Gamification;

use Livewire\Component;
use App\Models\User;
use App\Models\Challenge;
use App\Models\QuestAttempt;

class Index extends Component
{
    public function deleteChallenge($id)
    {
        Challenge::destroy($id);
        session()->flash('success', '🗑 Challenge deleted successfully!');
    }

    public function render()
    {
        // Calculate leaderboard based on student XP (sum of QuestAttempt scores)
        $students = User::where('role', 'student')
            ->withSum('questAttempts as points_sum_value', 'score')
            ->orderBy('points_sum_value', 'desc')
            ->get();

        // Calculate Level (simple logic: 1 level per 200 XP)
        foreach ($students as $student) {
            $student->level = floor(($student->points_sum_value ?? 0) / 200) + 1;
        }

        $challenges = Challenge::orderBy('created_at', 'desc')->get();

        return view('livewire.teacher.gamification.index', [
            'students' => $students,
            'challenges' => $challenges
        ])->layout('livewire.teacher.app-layout');
    }
}
