@extends('teacher.layouts.app')

@section('title', 'Registration')
@section('page-title', 'Registration')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Student Registration</h2>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 20px; padding: 20px; background: var(--bg-main); border-radius: var(--radius-sm);">
            <form action="{{ route('teacher.registration.generate-code') }}" method="GET">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> Generate Registration Code
                </button>
            </form>
            @if(session('success'))
                <div style="font-weight: 600; color: var(--primary);">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Registered Students</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" style="width: 36px; height: 36px; border-radius: 50%;">
                                <span style="font-weight: 500;">{{ $student->name }}</span>
                            </div>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <span class="badge badge-success">Active</span>
                        </td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No students registered yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
