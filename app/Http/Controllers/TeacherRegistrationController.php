<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RegistrationCode;


class TeacherRegistrationController extends Controller
{
    // Show all students
    public function index()
    {
        $students = User::where('role', 'student')->get();
        return view('teacher.registration.index', compact('students'));
    }

    // Generate a registration code
    public function generateCode()
{
    $code = strtoupper(bin2hex(random_bytes(3))); // e.g., 6 chars
    RegistrationCode::create(['code' => $code]);
    return back()->with('success', "New code generated: $code");
}
}
