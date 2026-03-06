@extends('teacher.dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/registration.css') }}">

<div class="registration-container">

    <h2>👨‍🏫 Student Registrations</h2>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="code-section">
        <a href="{{ route('teacher.registration.generate-code') }}" class="btn-primary">
            🔑 Generate Registration Code
        </a>

        @if(session('registration_code'))
            <div class="code-display">
                Current Code: <strong>{{ session('registration_code') }}</strong>
            </div>
        @endif
    </div>

    <div class="students-table">
        <h3>Students List</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <span class="status {{ strtolower($student->status) }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">No students registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
