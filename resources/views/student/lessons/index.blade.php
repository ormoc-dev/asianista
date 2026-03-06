@extends('student.dashboard')

@section('content')
<div class="card">
    <h2>Available Lessons ({{ $lessons->count() }})</h2>
    <p class="text-sm text-gray-600 mb-6">Click <strong>View</strong> to download or open lesson files</p>

    @if($lessons->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Teacher</th>
                        <th>Section</th>
                        <th>Uploaded</th>
                        <th class="text-center">File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lessons as $lesson)
                        <tr>
                            <td>
                                <div class="font-medium text-gray-800">{{ $lesson->title }}</div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span>{{ $lesson->teacher->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($lesson->section)
                                    <span class="badge section">{{ $lesson->section }}</span>
                                @else
                                    <span class="text-gray-400 text-sm italic">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm text-gray-600">
                                    {{ $lesson->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($lesson->file_path)
                                    <a href="{{ asset('storage/' . $lesson->file_path) }}"
                                       target="_blank"
                                       class="btn-view">
                                        View
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">No file</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="emoji">No lessons</div>
            <p class="text-lg font-medium text-gray-600">No lessons uploaded yet!</p>
            <p class="text-sm text-gray-500 mt-1">Check back soon — your teacher will add new materials</p>
        </div>
    @endif
</div>
@endsection