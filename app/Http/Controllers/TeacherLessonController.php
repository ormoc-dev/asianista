<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class TeacherLessonController extends Controller
{
    /**
     * Display all lessons uploaded by the current teacher.
     */
    public function index()
    {
        // For now, no authentication — show all lessons
        $lessons = Lesson::orderBy('created_at', 'desc')->get();

        return view('teacher.lessons.index', compact('lessons'));
    }

    /**
     * Show the form to upload a new lesson (text or file).
     */
    public function create()
    {
        $grades = Grade::with('sections')->orderBy('name')->get();

        return view('teacher.lessons.create', compact('grades'));
    }

    /**
     * Store the uploaded lesson (text, file, or both); published immediately.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,docx,pptx,zip,txt|max:20480',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::findOrFail($request->section_id);
        if ((int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

        $grade = Grade::findOrFail($request->grade_id);
        $sectionLabel = $grade->name.' - '.$section->name;

        $filePath = null;

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        // ✅ Temporarily fake teacher ID (since Auth isn't set up yet)
        $teacherId = 1; // you can change this to match your dummy teacher

        Lesson::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'file_path'   => $filePath,
            'teacher_id'  => $teacherId,
            'status'      => 'approved',
            'section'     => $sectionLabel,
            'grade_id'    => $request->grade_id,
            'section_id'  => $request->section_id,
        ]);

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson published successfully.');
    }

    /**
     * Download the uploaded lesson file.
     */
    public function download($filename)
    {
        $path = 'lessons/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            return response()->download(storage_path('app/public/' . $path));
        }

        return back()->with('error', 'File not found.');
    }

    /**
     * Edit a specific lesson.
     */
    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);
        $grades = Grade::with('sections')->orderBy('name')->get();

        $initialGradeId = null;
        $initialSectionId = null;
        if ($lesson->section) {
            foreach ($grades as $g) {
                foreach ($g->sections as $sec) {
                    if (($g->name.' - '.$sec->name) === $lesson->section) {
                        $initialGradeId = $g->id;
                        $initialSectionId = $sec->id;
                        break 2;
                    }
                }
            }
        }

        return view('teacher.lessons.edit', compact('lesson', 'grades', 'initialGradeId', 'initialSectionId'));
    }

    /**
     * Update the lesson content.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,docx,pptx,zip,txt|max:20480',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::findOrFail($request->section_id);
        if ((int) $section->grade_id !== (int) $request->grade_id) {
            return back()->withErrors(['section_id' => 'The section must belong to the selected grade.'])->withInput();
        }

        $grade = Grade::findOrFail($request->grade_id);
        $sectionLabel = $grade->name.' - '.$section->name;

        $filePath = $lesson->file_path;

        if ($request->hasFile('file')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        $lesson->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'file_path'   => $filePath,
            'section'     => $sectionLabel,
            'status'      => 'approved',
            'grade_id'    => $request->grade_id,
            'section_id'  => $request->section_id,
        ]);

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson updated successfully.');
    }

    /**
     * Delete a lesson.
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('teacher.lessons.index')->with('success', 'Lesson deleted successfully.');
    }
}
