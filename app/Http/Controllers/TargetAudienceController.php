<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;

class TargetAudienceController extends Controller
{
    public function index()
    {
        $grades = Grade::with('sections')->get();
        return view('admin.target-audience.index', compact('grades'));
    }

    public function storeGrade(Request $request)
    {
        $request->validate(['name' => 'required']);
        Grade::create($request->all());
        return back()->with('success', 'Grade Added!');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'grade_id' => 'required',
            'name' => 'required'
        ]);

        Section::create($request->all());
        return back()->with('success', 'Section Added!');
    }

    public function deleteGrade($id)
    {
        Grade::findOrFail($id)->delete();
        return back()->with('success', 'Grade Deleted!');
    }

    public function deleteSection($id)
    {
        Section::findOrFail($id)->delete();
        return back()->with('success', 'Section Deleted!');
    }

    public function updateGrade(Request $request, $id)
{
    $request->validate([
        'name' => 'required'
    ]);

    $grade = Grade::findOrFail($id);
    $grade->update([
        'name' => $request->name
    ]);

    return back()->with('success', 'Grade updated successfully!');
}

public function updateSection(Request $request, $id)
{
    $request->validate([
        'grade_id' => 'required',
        'name' => 'required'
    ]);

    $section = Section::findOrFail($id);
    $section->update([
        'grade_id' => $request->grade_id,
        'name'     => $request->name
    ]);

    return back()->with('success', 'Section updated successfully!');
}
}
