<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TeacherController extends Controller
{
    public function dashboard() {
        return view('teacher.dashboard');
    }

    public function registration()
{
    // Get all students assigned to this teacher
    $students = User::where('role', 'student')->get();
    return view('teacher.index', compact('students')); // Using index.blade.php
}

    public function lessons() {
        return view('teacher.lessons');
    }

    public function quizzes() {
        return view('teacher.quizzes');
    }

    public function gamification() {
        return view('teacher.gamification');
    }

    public function aiTrack() {
        return view('teacher.ai-track');
    }

    public function performance() {
        return view('teacher.performance');
    }

    public function feedback() {
        return view('teacher.feedback');
    }

    public function reports() {
        return view('teacher.reports');
    }

    public function contentReview() {
        return view('teacher.content-review');
    }

    public function quest()
    {
        // Add your logic for the quest page here.
        return view('teacher.quest');  // Make sure the view exists.
    }
}
