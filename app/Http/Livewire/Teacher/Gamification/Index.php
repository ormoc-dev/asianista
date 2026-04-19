<?php

namespace App\Http\Livewire\Teacher\Gamification;

use Livewire\Component;
use App\Models\User;
use App\Models\Challenge;
use App\Models\QuestAttempt;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function deleteChallenge($id)
    {
        $challenge = Challenge::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);
        $challenge->delete();
        session()->flash('success', '🗑 Challenge deleted successfully!');
    }

    public function render()
    {
        $teacherId = (int) Auth::id();

        $students = User::where('role', 'student')
            ->registeredByTeacher($teacherId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        foreach ($students as $student) {
            $student->points_sum_value = (int) QuestAttempt::query()
                ->where('user_id', $student->id)
                ->whereHas('quest', fn ($q) => $q->where('teacher_id', $teacherId))
                ->sum('score');
            $student->level = floor(($student->points_sum_value ?? 0) / 200) + 1;
        }

        $students = $students->sortByDesc('points_sum_value')->values();

        $challenges = Challenge::query()
            ->ownedByTeacher($teacherId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.teacher.gamification.index', [
            'students' => $students,
            'challenges' => $challenges
        ])->layout('livewire.teacher.app-layout');
    }
}
