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

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student',
            'character' => 'nullable|string',
            'level' => 'nullable|integer|min:1',
            'xp' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.user-management')
            ->with('status', 'Hero updated successfully in the realm!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.user-management')
            ->with('status', 'User deleted successfully.');
    }
}
