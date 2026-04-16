<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserManagementController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['teacher', 'student'])
            ->orderByRaw("CASE role WHEN 'teacher' THEN 0 WHEN 'student' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get();

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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student',
            'status' => 'required|in:pending,approved,rejected',
        ];

        if ($request->input('role') === 'student') {
            $rules['character'] = 'nullable|string|max:255';
            $rules['level'] = 'nullable|integer|min:1';
            $rules['xp'] = 'nullable|integer|min:0';
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if ($validated['role'] === 'student') {
            $data['character'] = $validated['character'] ?? null;
            $data['level'] = isset($validated['level']) ? (int) $validated['level'] : 1;
            $data['xp'] = isset($validated['xp']) ? (int) $validated['xp'] : 0;
        } else {
            $data['character'] = null;
        }

        $user->update($data);

        return redirect()
            ->route('admin.user-management')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.user-management')
            ->with('status', 'User deleted successfully.');
    }
}
