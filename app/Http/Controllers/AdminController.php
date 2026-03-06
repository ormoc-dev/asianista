<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
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
        return view('admin.sections.ai-management');
    }

    public function data() {
        return view('admin.sections.data');
    }

    public function security() {
        return view('admin.sections.security');
    }
}
