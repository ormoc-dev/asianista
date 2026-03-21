@extends('teacher.layouts.app')

@section('title', 'Create Challenge')
@section('page-title', 'Create Challenge')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create New Challenge</h2>
        <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.gamification.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label"><i class="fas fa-trophy" style="color: var(--accent);"></i> Challenge Title</label>
                <input type="text" name="title" class="form-control" placeholder="Ex: Complete 5 Lessons" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-coins" style="color: var(--accent);"></i> Points</label>
                <input type="number" name="points" class="form-control" placeholder="Ex: 200" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-align-left"></i> Description (Optional)</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe what students need to achieve..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('teacher.gamification.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Challenge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
