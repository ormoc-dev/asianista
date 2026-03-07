<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        $totalUsers = \App\Models\User::count();
        $teachersCount = \App\Models\User::where('role', 'teacher')->count();
        $studentsCount = \App\Models\User::where('role', 'student')->count();
        $pendingApprovals = \App\Models\User::where('status', 'pending')->count();
        $totalLessons = \App\Models\Lesson::count();
        
        $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.home', compact(
            'totalUsers', 
            'teachersCount', 
            'studentsCount', 
            'pendingApprovals', 
            'totalLessons',
            'recentUsers'
        ));
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
        return view('admin.ai-management.index');
    }

    public function data() {
        return view('admin.sections.data');
    }

    public function security() {
        return view('admin.sections.security');
    }
}
