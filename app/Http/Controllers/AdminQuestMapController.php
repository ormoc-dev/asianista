<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestMapLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminQuestMapController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('public');

        $files = collect($disk->exists('quest_maps') ? $disk->files('quest_maps') : [])
            ->filter(fn (string $path) => preg_match('#^quest_maps/[a-zA-Z0-9._-]+$#', $path) === 1)
            ->sort()
            ->values();

        $usageByPath = Quest::query()
            ->whereNotNull('map_image')
            ->where('map_image', 'like', 'quest_maps/%')
            ->selectRaw('map_image, COUNT(*) as c')
            ->groupBy('map_image')
            ->pluck('c', 'map_image');

        $maps = $files->map(function (string $path) use ($usageByPath, $disk) {
            return [
                'path' => $path,
                'url' => $disk->url($path),
                'basename' => basename($path),
                'usage' => (int) ($usageByPath[$path] ?? 0),
                'size' => $disk->size($path),
            ];
        });

        $defaultUsage = Quest::query()
            ->where(function ($q) {
                $q->whereNull('map_image')
                    ->orWhere('map_image', 'quest_map_bg.png')
                    ->orWhere('map_image', '');
            })
            ->count();

        return view('admin.quest-maps.index', [
            'maps' => $maps,
            'defaultUsage' => $defaultUsage,
            'defaultMapUrl' => asset('images/quest_map_bg.png'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'map_image' => 'required|image|mimes:jpeg,png,webp,gif|max:5120',
        ]);

        $file = $request->file('map_image');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'png';
        }

        $name = uniqid('admin_', true).'.'.$ext;
        $file->storeAs('quest_maps', $name, 'public');
        $path = 'quest_maps/'.$name;

        return redirect()->route('admin.quest-maps.index')
            ->with('success', 'Quest map uploaded as '.$path.'. Teachers can assign this file in the quest editor.');
    }

    public function destroy(string $file)
    {
        if (preg_match('/^[a-zA-Z0-9._-]+$/', $file) !== 1) {
            return redirect()->route('admin.quest-maps.index')
                ->with('error', 'Invalid map file name.');
        }

        $path = 'quest_maps/'.$file;

        if (! Storage::disk('public')->exists($path)) {
            return redirect()->route('admin.quest-maps.index')
                ->with('error', 'That map file was not found.');
        }

        if (Quest::query()->where('map_image', $path)->exists()) {
            return redirect()->route('admin.quest-maps.index')
                ->with('error', 'Cannot delete: one or more quests still use this map. Reassign those quests first.');
        }

        Storage::disk('public')->delete($path);
        QuestMapLayout::query()->where('map_key', $path)->delete();

        return redirect()->route('admin.quest-maps.index')
            ->with('success', 'Quest map removed from the library.');
    }

    public function editLayout(Request $request)
    {
        $mapKey = $request->query('key', 'default');
        if (! $this->isAllowedMapKey($mapKey)) {
            abort(404);
        }

        $layout = QuestMapLayout::query()->where('map_key', $mapKey)->first();
        $pins = $layout && is_array($layout->pins) && count($layout->pins) > 0
            ? QuestMapLayout::normalizePinArray($layout->pins)
            : QuestMapLayout::fallbackPins();

        $imageUrl = $mapKey === 'default'
            ? asset('images/quest_map_bg.png')
            : Storage::disk('public')->url($mapKey);

        return view('admin.quest-maps.layout', [
            'mapKey' => $mapKey,
            'pins' => $pins,
            'imageUrl' => $imageUrl,
        ]);
    }

    public function updateLayout(Request $request)
    {
        $validated = $request->validate([
            'map_key' => 'required|string|max:255',
            'pins' => 'required|array|min:1',
            'pins.*.left' => 'required|numeric|between:0,100',
            'pins.*.top' => 'required|numeric|between:0,100',
            'pins.*.name' => 'nullable|string|max:120',
            'pins.*.icon' => 'nullable|string|max:80',
        ]);

        if (! $this->isAllowedMapKey($validated['map_key'])) {
            abort(404);
        }

        $pins = QuestMapLayout::normalizePinArray($validated['pins']);

        QuestMapLayout::query()->updateOrCreate(
            ['map_key' => $validated['map_key']],
            ['pins' => $pins]
        );

        return redirect()
            ->route('admin.quest-maps.layout', ['key' => $validated['map_key']])
            ->with('success', 'Level positions saved. Quest markers follow these points in order (Level 1 = first pin).');
    }

    private function isAllowedMapKey(string $key): bool
    {
        if ($key === 'default') {
            return true;
        }

        return preg_match('#^quest_maps/[a-zA-Z0-9._-]+$#', $key) === 1
            && Storage::disk('public')->exists($key);
    }
}
