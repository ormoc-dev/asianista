<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserManagementController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['teacher', 'student'])->get();
        return view('admin.user-management.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.user-management.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.user-management.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.user-management')
            ->with('status', 'User deleted successfully.');
    }
}
