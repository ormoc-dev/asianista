<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentQuestController extends Controller
{
    public function index()
    {
        // Your logic for the quest page here
        return view('student.quest.index');  // Ensure this view exists
    }
}
