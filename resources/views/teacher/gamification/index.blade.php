@extends('teacher.dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 p-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div class="text-center md:text-left">
            <h1 class="text-4xl font-extrabold text-indigo-800 flex items-center justify-center md:justify-start gap-3">
                🏆 Class Leaderboard
            </h1>
            <p class="text-gray-600 mt-2 text-lg">Motivate your students through XP, badges, and milestones!</p>
        </div>
        <a href="{{ route('teacher.gamification.create') }}"
           class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold px-6 py-3 rounded-2xl shadow-lg hover:shadow-2xl hover:scale-105 transition duration-300 flex items-center gap-2 mt-4 md:mt-0">
           ➕ Create Challenge
        </a>
    </div>

    <!-- Leaderboard -->
    <div class="bg-white rounded-3xl shadow-2xl border border-indigo-100 p-8 mb-12">
        <h2 class="text-3xl font-bold text-indigo-700 mb-8 text-center">🏅 Top Performers</h2>

        @if($students->count())
            <div class="grid md:grid-cols-3 gap-8 mb-10">
                @foreach($students->take(3) as $index => $student)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-3xl p-6 text-center shadow hover:shadow-xl transition transform hover:-translate-y-2">
                        <div class="relative">
                            @if($index == 0)
                                <span class="absolute -top-3 right-3 text-3xl">👑</span>
                            @endif
                            <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"
                                 class="w-20 h-20 rounded-full mx-auto border-4 border-indigo-400 shadow-md" alt="avatar">
                        </div>
                        <h3 class="text-xl font-bold mt-4 text-indigo-800">{{ $student->name }}</h3>
                        <p class="text-gray-500 text-sm">Level {{ $student->level ?? 1 }}</p>
                        <div class="mt-3">
                            <p class="text-indigo-700 font-semibold text-lg">{{ $student->points_sum_value ?? 0 }} XP</p>
                            <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                                @php $progress = min(($student->points_sum_value ?? 0) / 2000 * 100, 100); @endphp
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center gap-2">
                            @php $badges = collect($student->badges); @endphp
                            @forelse($badges->take(3) as $badge)
                                <span class="text-2xl">{{ $badge->emoji }}</span>
                            @empty
                                <span class="text-gray-400 text-sm">No badges</span>
                            @endforelse
                            @if($badges->count() > 3)
                                <span class="text-gray-500 text-sm">+{{ $badges->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Full Leaderboard Table -->
            <div class="overflow-x-auto bg-indigo-50 rounded-2xl p-4 shadow-inner">
                <table class="min-w-full table-auto border-collapse text-sm">
                    <thead class="bg-indigo-200 text-indigo-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Rank</th>
                            <th class="px-4 py-3 text-left font-semibold">Student</th>
                            <th class="px-4 py-3 text-center font-semibold">XP</th>
                            <th class="px-4 py-3 text-center font-semibold">Badges</th>
                            <th class="px-4 py-3 text-center font-semibold">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-100">
                        @foreach($students as $index => $student)
                        <tr class="hover:bg-indigo-100 transition">
                            <td class="px-4 py-3 font-bold text-center">
                                @if($index == 0)
                                    🥇
                                @elseif($index == 1)
                                    🥈
                                @elseif($index == 2)
                                    🥉
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-indigo-800">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-center text-indigo-700 font-semibold">{{ $student->points_sum_value ?? 0 }} XP</td>
                            <td class="px-4 py-3 text-center">
                                @php $badges = collect($student->badges); @endphp
                                @forelse($badges->take(3) as $badge)
                                    <span class="text-xl">{{ $badge->emoji }}</span>
                                @empty
                                    <span class="text-gray-400 text-sm">None</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3">
                                @php $progress = min(($student->points_sum_value ?? 0) / 2000 * 100, 100); @endphp
                                <div class="w-full bg-gray-200 h-2 rounded-full">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500 text-lg">
                😅 No students found yet. Once your class starts earning XP, they’ll appear here!
            </div>
        @endif
    </div>

    <!-- Challenges -->
    <div class="bg-white rounded-3xl shadow-2xl border border-indigo-100 p-8">
        <h2 class="text-3xl font-bold text-indigo-700 mb-6 text-center">🎯 Active Challenges</h2>

        @if($challenges->count())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($challenges as $challenge)
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-3xl p-6 shadow-md hover:shadow-xl hover:scale-105 transition">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-lg font-bold text-indigo-800">{{ $challenge->title }}</h4>
                        <span class="bg-indigo-600 text-white text-xs px-3 py-1 rounded-full shadow">+{{ $challenge->points }} XP</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-5">{{ $challenge->description ?? 'Complete this challenge to earn XP!' }}</p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('teacher.gamification.edit', $challenge->id) }}"
                           class="text-indigo-600 hover:text-indigo-800 font-medium">✏️ Edit</a>
                        <form action="{{ route('teacher.gamification.destroy', $challenge->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this challenge?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">🗑 Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500 text-lg">
                🚀 No challenges created yet.  
                <span class="text-indigo-600 font-semibold">Add some to boost engagement!</span>
            </div>
        @endif
    </div>
</div>
@endsection
