@extends('teacher.layouts.app')

@section('title', 'Student Scores')
@section('page-title', 'Student Scores')

@section('content')
<!-- Simple Stats -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body">
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div>
                <span style="color: var(--text-muted); font-size: 0.9rem;">Students</span>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ $classAverage['total_students'] }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.9rem;">Avg XP</span>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ number_format($classAverage['avg_xp']) }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.9rem;">Avg Level</span>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ number_format($classAverage['avg_level'], 1) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Student Scores Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Student Rankings</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Level</th>
                        <th>XP</th>
                        <th>HP</th>
                        <th>Quests Done</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    @php
                        $stats = $questStats->get($student->id);
                        $rank = $index + 1;
                    @endphp
                    <tr>
                        <td>{{ $rank }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ asset($student->profile_pic ?: 'images/default-pp.png') }}" 
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 500;">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $student->character ?: 'No Class' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->level }}</td>
                        <td style="font-weight: 600; color: #10b981;">{{ $student->xp }}</td>
                        <td>{{ $student->hp }}</td>
                        <td>{{ $stats ? $stats->completed_quests : 0 }}</td>
                        <td>
                            <a href="{{ route('teacher.reports.student', $student) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
