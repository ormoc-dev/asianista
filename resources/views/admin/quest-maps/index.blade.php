@extends('admin.layouts.app')

@section('title', 'Quest Map Management')
@section('page-title', 'Quest Map Management')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-map" style="color: var(--primary);"></i> Default map</h2>
    </div>
    <div class="card-body" style="display: flex; gap: 24px; flex-wrap: wrap; align-items: flex-start;">
        <div style="flex: 0 0 200px; max-width: 100%; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border);">
            <img src="{{ $defaultMapUrl }}" alt="Default quest map" style="width: 100%; height: auto; display: block;">
        </div>
        <div style="flex: 1; min-width: 220px;">
            <p style="color: var(--text-secondary); margin-bottom: 12px;">
                Quests without a custom background use the built-in image at <code style="font-size: 0.85em;">public/images/quest_map_bg.png</code>.
                Replace that file on the server if you want to change the default for everyone.
            </p>
            <p style="margin: 0 0 12px;">
                <span class="badge badge-info">In use: {{ $defaultUsage }} quest(s)</span>
            </p>
            <a href="{{ route('admin.quest-maps.layout', ['key' => 'default']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-map-pin"></i> Edit level positions on map
            </a>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 10px; margin-bottom: 0;">
                Place points in order (Level 1, 2, 3…). Student markers and the path line follow these spots.
            </p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Upload to library</h2>
    </div>
    <div class="card-body">
        <p style="color: var(--text-muted); margin-bottom: 16px;">
            Uploaded images are stored in public storage (<code style="font-size: 0.85em;">quest_maps/</code>). Teachers can attach them when creating or editing quests.
        </p>
        <form action="{{ route('admin.quest-maps.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            @csrf
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label class="form-label" for="map_image">Map image (PNG, JPG, WebP, GIF — max 5 MB)</label>
                <input type="file" name="map_image" id="map_image" class="form-control" accept="image/png,image/jpeg,image/webp,image/gif" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-upload"></i> Upload
            </button>
        </form>
        @error('map_image')
            <p style="color: var(--danger); margin-top: 12px; margin-bottom: 0;">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Custom maps in storage</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($maps->isEmpty())
            <p style="padding: 24px; color: var(--text-muted); margin: 0;">No files in <code>quest_maps/</code> yet. Upload an image above or add quests with custom maps from the teacher portal.</p>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Preview</th>
                            <th>Path</th>
                            <th>Size</th>
                            <th>Used by</th>
                            <th style="min-width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maps as $row)
                        <tr>
                            <td>
                                <a href="{{ $row['url'] }}" target="_blank" rel="noopener noreferrer" style="display: block; width: 72px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border);">
                                    <img src="{{ $row['url'] }}" alt="" style="width: 72px; height: 48px; object-fit: cover; display: block;">
                                </a>
                            </td>
                            <td>
                                <code style="font-size: 0.8rem;">{{ $row['path'] }}</code>
                            </td>
                            <td>{{ number_format($row['size'] / 1024, 1) }} KB</td>
                            <td>
                                @if($row['usage'] > 0)
                                    <span class="badge badge-warning">{{ $row['usage'] }} quest(s)</span>
                                @else
                                    <span class="badge badge-success">Unused</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                                    <a href="{{ route('admin.quest-maps.layout', ['key' => $row['path']]) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-map-pin"></i> Level positions
                                    </a>
                                    @if($row['usage'] === 0)
                                        <form action="{{ route('admin.quest-maps.destroy', $row['basename']) }}" method="POST" onsubmit="return confirm('Delete this map file permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
