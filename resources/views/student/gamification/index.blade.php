@extends('student.dashboard')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">🏆 Gamification</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Points</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['points'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Level</h3>
            <p class="text-3xl font-bold text-green-600">Lv. {{ $stats['level'] }}</p>
            <p class="text-sm text-gray-500 mt-2">Progress to next level: {{ $stats['progress'] }}%</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-2">Badges</h3>
            <ul class="list-disc ml-4">
                @foreach($stats['badges'] as $badge)
                <li>{{ $badge }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <h3 class="font-bold text-xl mt-6 mb-2">🎯 Challenges</h3>
    <div class="bg-white rounded shadow p-4">
        <ul>
            @foreach($challenges as $c)
            <li class="border-b py-2 flex justify-between">
                <span>{{ $c['title'] }}</span>
                <span class="{{ $c['status'] === 'Unlocked' ? 'text-green-600' : 'text-gray-400' }}">{{ $c['status'] }}</span>
            </li>
            @endforeach
        </ul>
    </div>

    <h3 class="font-bold text-xl mt-6 mb-2">🏅 Leaderboard</h3>
    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
            <tr><th class="py-2 px-4 text-left">Name</th><th class="py-2 px-4 text-right">Points</th></tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $user)
            <tr>
                <td class="py-2 px-4 border-t">{{ $user['name'] }}</td>
                <td class="py-2 px-4 border-t text-right">{{ $user['points'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
