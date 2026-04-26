<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherGamificationController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = (int) Auth::id();

        $grades = Grade::orderBy('name')->get();
        $gradeId = $request->query('grade_id');
        $sectionId = $request->query('section_id');

        $sections = collect();
        if ($gradeId) {
            $sections = Section::where('grade_id', $gradeId)->orderBy('name')->get();
        }

        $challenges = Challenge::query()
            ->ownedByTeacher($teacherId)
            ->with(['grade', 'section'])
            ->when($gradeId, function ($q) use ($gradeId, $sectionId) {
                $q->where(function ($q2) use ($gradeId, $sectionId) {
                    $q2->whereNull('grade_id')->whereNull('section_id');
                    $q2->orWhere(function ($q3) use ($gradeId, $sectionId) {
                        $q3->where('grade_id', $gradeId);
                        if ($sectionId) {
                            $q3->where('section_id', $sectionId);
                        }
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $students = User::where('role', 'student')
            ->registeredByTeacher($teacherId)
            ->when($gradeId, fn ($q) => $q->where('grade_id', $gradeId))
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->orderBy('xp', 'desc')
            ->take(10)
            ->get();

        return view('teacher.gamification.index', compact(
            'challenges',
            'students',
            'grades',
            'sections',
            'gradeId',
            'sectionId'
        ));
    }

    public function create()
    {
        $grades = Grade::orderBy('name')->get();

        return view('teacher.gamification.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::find($request->section_id);
        if (! $section || (int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

        Challenge::create([
            'title' => $request->title,
            'points' => $request->points,
            'description' => $request->description,
            'grade_id' => $request->grade_id,
            'section_id' => $request->section_id,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('teacher.gamification.index')
                         ->with('success', '🎉 Challenge created successfully!');
    }

    public function edit($id)
    {
        $challenge = Challenge::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);
        $grades = Grade::orderBy('name')->get();

        return view('teacher.gamification.edit', compact('challenge', 'grades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::find($request->section_id);
        if (! $section || (int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

        $challenge = Challenge::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);
        $challenge->update([
            'title' => $request->title,
            'points' => $request->points,
            'description' => $request->description,
            'grade_id' => $request->grade_id,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('teacher.gamification.index')
                         ->with('success', '✅ Challenge updated successfully!');
    }

    public function destroy($id)
    {
        $challenge = Challenge::query()->ownedByTeacher((int) Auth::id())->findOrFail($id);
        $challenge->delete();
        return redirect()->route('teacher.gamification.index')
                         ->with('success', '🗑 Challenge deleted successfully!');
    }

    public function updateStudentStats(Request $request, User $student)
    {
        $teacherId = (int) Auth::id();

        abort_unless(
            $student->role === 'student' && (int) $student->registered_by_teacher_id === $teacherId,
            403
        );

        $validated = $request->validate([
            'hp' => 'required|integer|min:0|max:9999',
            'xp' => 'required|integer|min:0|max:999999',
            'ap' => 'required|integer|min:0|max:9999',
        ]);

        $student->update($validated);

        return redirect()
            ->route('teacher.gamification.index', $request->only(['grade_id', 'section_id']))
            ->with('success', "{$student->full_name}'s HP, XP, and AP were updated.");
    }
}
