@extends('admin.dashboard')

@section('content')
<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-8 border border-gray-100">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                📚 Content Control
            </h2>
            <p class="text-gray-500 text-sm">
                Review, approve, or reject lessons uploaded by teachers.
            </p>
        </div>
        <span class="text-gray-400 text-sm">
            Updated: {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 bg-green-100 border border-green-300 text-green-800 text-sm rounded-lg p-3">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Lessons Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full bg-white">
            <thead class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white text-sm uppercase">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Title</th>
                    <th class="px-6 py-3 text-left font-semibold">Teacher</th>
                    <th class="px-6 py-3 text-left font-semibold">Section</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($lessons as $lesson)
                    <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $lesson->title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $lesson->teacher->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $lesson->section ?? 'N/A' }}</td>

                        <td class="px-6 py-4">
                            @if($lesson->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">🕒 Pending</span>
                            @elseif($lesson->status === 'approved')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">✅ Approved</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">❌ Rejected</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2 flex-wrap">
                                @if($lesson->status === 'pending')
                                    {{-- Approve Button --}}
                                    <form action="{{ route('admin.lessons.approve', $lesson->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition">
                                            ✅ Approve
                                        </button>
                                    </form>

                                    {{-- Reject Button --}}
                                    <form action="{{ route('admin.lessons.reject', $lesson->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition">
                                            ❌ Reject
                                        </button>
                                    </form>
                                @endif

                                @if($lesson->file_path)
                                    <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank"
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                        📂 View File
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500 italic">
                            No lessons submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="mt-6 text-sm text-gray-400 text-center">
        Showing <strong>{{ $lessons->count() }}</strong> lesson(s)
    </div>
</div>
@endsection
