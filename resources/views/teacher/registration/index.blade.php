@extends('teacher.layouts.app')

@section('title', 'Registration')
@section('page-title', 'Registration')

@section('content')
<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 24px; padding: 16px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="margin-bottom: 24px; padding: 16px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24;">
        {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning" style="margin-bottom: 24px; padding: 16px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; color: #856404;">
        {{ session('warning') }}
    </div>
@endif

<!-- Excel Upload Section -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Bulk Student Registration</h2>
    </div>
    <div class="card-body">
        <div style="padding: 20px; background: var(--bg-main); border-radius: var(--radius-sm);">
            <p style="margin-bottom: 16px; color: var(--text-muted);">
                Upload an Excel file with student information (FNAME, LNAME, MNAME). 
                The system will automatically generate unique student codes, usernames, and default passwords.
            </p>

            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <!-- Download Template -->
                <a href="{{ route('teacher.registration.template') }}" class="btn btn-secondary" style="text-decoration: none;">
                    <i class="fas fa-download"></i> Download Template
                </a>

                <!-- Upload Form -->
                <form action="{{ route('teacher.registration.upload') }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 12px;">
                    @csrf
                    <input type="file" name="student_file" accept=".xlsx,.xls,.csv" required 
                        style="padding: 8px; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Students
                    </button>
                </form>
            </div>

            <div style="margin-top: 16px; padding: 12px; background: #e7f3ff; border-radius: 6px; font-size: 0.9rem;">
                <strong>Excel Format:</strong> Column headers should be FNAME, LNAME, MNAME (First Name, Last Name, Middle Name - optional).
            </div>
        </div>
    </div>
</div>

<!-- Pending Registrations -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Pending Registrations (Not Yet Claimed)</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Code</th>
                        <th>Username</th>
                        <th>Default Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRegistrations as $registration)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-weight: 500;">{{ $registration->full_name }}</span>
                            </div>
                        </td>
                        <td>
                            <code style="padding: 4px 8px; background: #f4f4f4; border-radius: 4px; font-family: monospace;">{{ $registration->student_code }}</code>
                        </td>
                        <td>{{ $registration->username }}</td>
                        <td>
                            <span style="font-family: monospace; background: #fff3cd; padding: 2px 6px; border-radius: 4px;">{{ $registration->default_password }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('teacher.registration.regenerate', $registration->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Regenerate credentials for {{ $registration->full_name }}?')">
                                        <i class="fas fa-sync"></i> Regenerate
                                    </button>
                                </form>
                                <form action="{{ route('teacher.registration.destroy-pending', $registration->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete pending registration for {{ $registration->full_name }}?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No pending registrations. Upload an Excel file to add students.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Registered Students -->
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
                        <th>Character</th>
                        <th>HP/AP</th>
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
                            @if($student->character)
                                <span class="badge" style="text-transform: capitalize; background: 
                                    {{ $student->character === 'mage' ? '#6c5ce7' : ($student->character === 'warrior' ? '#e17055' : '#00b894') }};">
                                    {{ $student->character }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Not Selected</span>
                            @endif
                        </td>
                        <td>
                            @if($student->hp > 0 || $student->ap > 0)
                                <span style="font-size: 0.85rem;">
                                    <span style="color: #e74c3c;">HP: {{ $student->hp }}</span> / 
                                    <span style="color: #3498db;">AP: {{ $student->ap }}</span>
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($student->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($student->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($student->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
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
