@extends('teacher.layouts.app')

@section('title', 'Approved students')
@section('page-title', 'Approved students')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-check" style="color: var(--primary);"></i> Your approved students</h2>
        <p style="margin: 8px 0 0; color: var(--text-muted); font-size: 0.9rem;">
            Learners you registered who have been approved and can use the platform.
        </p>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Email</th>
                        <th>Character</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" alt="" style="width: 36px; height: 36px; border-radius: 50%;">
                                <span style="font-weight: 500;">{{ $student->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($student->grade)
                                <span class="badge badge-secondary" style="font-weight: 500;">{{ $student->grade->name }}</span>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($student->section)
                                <span class="badge badge-info" style="font-weight: 500;">{{ $student->section->name }}</span>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td><span style="font-size: 0.9rem;">{{ $student->email }}</span></td>
                        <td>
                            @if($student->character)
                                <span class="badge badge-secondary">{{ ucfirst($student->character) }}</span>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('teacher.reports.student', $student) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-chart-line"></i> Report
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-user-check" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No approved students yet. Approve pending learners from <a href="{{ route('teacher.registration') }}">Registration</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
