@extends('teacher.layouts.app')

@section('title', 'Edit Challenge')
@section('page-title', 'Edit Challenge')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Challenge</h2>
        <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.gamification.update', $challenge->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label"><i class="fas fa-trophy" style="color: var(--accent);"></i> Challenge Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $challenge->title) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-coins" style="color: var(--accent);"></i> Points</label>
                <input type="number" name="points" class="form-control" value="{{ old('points', $challenge->points) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $challenge->description) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Challenge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
