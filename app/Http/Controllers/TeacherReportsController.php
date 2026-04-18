<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use App\Models\QuestAttempt;
use Illuminate\Support\Facades\DB;

class TeacherReportsController extends Controller
{
    /**
     * Display student scores/XP report
     */
    public function scores(Request $request)
    {
        $grades = Grade::orderBy('name')->get();
        $gradeId = $request->query('grade_id');
        $sectionId = $request->query('section_id');

        $sections = collect();
        if ($gradeId) {
            $sections = Section::where('grade_id', $gradeId)->orderBy('name')->get();
        }

        $students = User::where('role', 'student')
            ->when($gradeId, fn ($q) => $q->where('grade_id', $gradeId))
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->with(['grade', 'section'])
            ->select('id', 'first_name', 'last_name', 'name', 'email', 'character', 'gender', 'hp', 'ap', 'xp', 'level', 'profile_pic', 'grade_id', 'section_id')
            ->orderBy('xp', 'desc')
            ->orderBy('level', 'desc')
            ->get();

        // Get quest completion stats for each student
        $questStats = QuestAttempt::select('user_id', 
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_quests'),
                DB::raw('SUM(score) as total_quest_score')
            )
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Calculate class averages
        $classAverage = [
            'avg_xp' => $students->avg('xp'),
            'avg_level' => $students->avg('level'),
            'avg_hp' => $students->avg('hp'),
            'avg_ap' => $students->avg('ap'),
            'total_students' => $students->count(),
        ];

        return view('teacher.reports.scores', compact(
            'students',
            'questStats',
            'classAverage',
            'grades',
            'sections',
            'gradeId',
            'sectionId'
        ));
    }

    /**
     * Display detailed report for a specific student
     */
    public function studentDetail(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        // Get quest attempts
        $questAttempts = QuestAttempt::where('user_id', $student->id)
            ->with('quest')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get XP breakdown from random events (if tracked)
        $xpHistory = DB::table('active_events')
            ->whereJsonContains('affected_students', $student->id)
            ->join('random_events', 'active_events.random_event_id', '=', 'random_events.id')
            ->select('random_events.title', 'random_events.xp_reward', 'random_events.xp_penalty', 'active_events.started_at')
            ->orderBy('active_events.started_at', 'desc')
            ->get();

        return view('teacher.reports.student-detail', compact('student', 'questAttempts', 'xpHistory'));
    }

    /**
     * Update student XP (for manual adjustments)
     */
    public function updateXp(Request $request, User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $validated = $request->validate([
            'xp' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $oldXp = $student->xp;
        $student->xp = $validated['xp'];
        
        // Auto-calculate level based on XP (every 100 XP = 1 level)
        $student->level = floor($student->xp / 100) + 1;
        
        $student->save();

        return redirect()->back()
            ->with('success', "XP updated from {$oldXp} to {$student->xp}. Student is now Level {$student->level}!");
    }
}
