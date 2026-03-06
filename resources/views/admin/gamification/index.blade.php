@extends('admin.dashboard')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">🏆 Gamification Configuration</h2>

    <form action="{{ route('admin.gamification.update') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Point Ratio</label>
            <input type="text" name="point_ratio" value="{{ $config['point_ratio'] }}" class="border w-full p-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Available Badges</label>
            <textarea name="badges" class="border w-full p-2 rounded" rows="3">{{ implode(', ', $config['badges']) }}</textarea>
        </div>

        <div class="mb-4 flex items-center space-x-2">
            <input type="checkbox" name="leaderboard_enabled" {{ $config['leaderboard_enabled'] ? 'checked' : '' }}>
            <label>Enable Leaderboards</label>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Settings</button>
    </form>
</div>
@endsection
