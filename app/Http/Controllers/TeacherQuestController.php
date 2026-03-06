<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;

class TeacherQuestController extends Controller
{
    public function index()
    {
        // Your logic for the quest page here
        return view('teacher.quest.index');  // Ensure this view exists
    }
    public function create()
{
    $grades = Grade::with('sections')->get();
    return view('teacher.quest.create', compact('grades'));
}
}
