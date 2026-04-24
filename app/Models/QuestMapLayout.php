<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestMapLayout extends Model
{
    protected $fillable = [
        'map_key',
        'pins',
    ];

    protected $casts = [
        'pins' => 'array',
    ];

    public static function mapKeyForImage(?string $mapImage): string
    {
        $m = $mapImage ?? '';
        if ($m === '' || $m === 'quest_map_bg.png') {
            return 'default';
        }

        return $m;
    }

    /**
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function fallbackPins(): array
    {
        return [
            ['left' => 50, 'top' => 86, 'name' => 'Gate of Entry', 'icon' => 'fa-mountain'],
            ['left' => 25, 'top' => 55, 'name' => 'Whispering Falls', 'icon' => 'fa-water'],
            ['left' => 15, 'top' => 66, 'name' => 'Compass Grove', 'icon' => 'fa-compass'],
            ['left' => 40, 'top' => 40, 'name' => 'Floating Reaches', 'icon' => 'fa-cloud'],
            ['left' => 55, 'top' => 60, 'name' => 'Sky-Isle Steps', 'icon' => 'fa-shoe-prints'],
            ['left' => 75, 'top' => 45, 'name' => 'Mystery Landmark', 'icon' => 'fa-question'],
            ['left' => 75, 'top' => 80, 'name' => 'Trivia Chamber', 'icon' => 'fa-brain'],
            ['left' => 85, 'top' => 65, 'name' => 'Library of Wisdom', 'icon' => 'fa-book'],
            ['left' => 80, 'top' => 20, 'name' => 'The Observatory', 'icon' => 'fa-crown'],
        ];
    }

    /**
     * @param  array<int, mixed>  $pins
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function normalizePinArray(array $pins): array
    {
        $out = [];
        foreach ($pins as $p) {
            if (! is_array($p)) {
                continue;
            }
            $out[] = [
                'left' => (float) ($p['left'] ?? 50),
                'top' => (float) ($p['top'] ?? 50),
                'name' => isset($p['name']) ? (string) $p['name'] : '',
                'icon' => isset($p['icon']) ? (string) $p['icon'] : 'fa-map-marker-alt',
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function basePinsForImage(?string $mapImage): array
    {
        $key = self::mapKeyForImage($mapImage);
        $layout = self::query()->where('map_key', $key)->first();
        if ($layout && is_array($layout->pins) && count($layout->pins) > 0) {
            return self::normalizePinArray($layout->pins);
        }
        if ($key !== 'default') {
            $defaultLayout = self::query()->where('map_key', 'default')->first();
            if ($defaultLayout && is_array($defaultLayout->pins) && count($defaultLayout->pins) > 0) {
                return self::normalizePinArray($defaultLayout->pins);
            }
        }

        return self::fallbackPins();
    }

    /**
     * Expand a base pin list to match quest level count (1 .. levelCount).
     *
     * @param  array<int, array{left: float, top: float, name: string, icon: string}>  $base
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function expandBasePinsToLevels(array $base, int $levelCount): array
    {
        $levelCount = max(1, $levelCount);
        if (count($base) === 0) {
            $base = self::fallbackPins();
        }
        $out = [];
        $n = count($base);
        for ($i = 1; $i <= $levelCount; $i++) {
            $idx = $i - 1;
            if ($idx < $n) {
                $raw = $base[$idx];
                if ($raw['name'] === '') {
                    $raw['name'] = 'Level '.$i;
                }
                $out[] = $raw;

                continue;
            }
            $pLast = $base[$n - 1];
            $pPrev = $n >= 2 ? $base[$n - 2] : $base[0];
            $steps = $idx - $n + 1;
            $dl = ($pLast['left'] - $pPrev['left']) * 0.12 * $steps;
            $dt = ($pLast['top'] - $pPrev['top']) * 0.12 * $steps;
            $out[] = [
                'left' => min(95, max(5, $pLast['left'] + $dl)),
                'top' => min(95, max(5, $pLast['top'] + $dt)),
                'name' => 'Level '.$i,
                'icon' => $pLast['icon'] ?? 'fa-map-marker-alt',
            ];
        }

        return $out;
    }

    /**
     * Pins for a quest: optional per-quest layout, else school default for this map image.
     *
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function pinsForQuest(Quest $quest): array
    {
        $levelCount = max(1, (int) $quest->level);
        $questPins = $quest->map_pins;
        if (is_array($questPins) && count($questPins) > 0) {
            $base = self::normalizePinArray($questPins);
        } else {
            $base = self::basePinsForImage($quest->map_image);
        }

        return self::expandBasePinsToLevels($base, $levelCount);
    }

    /**
     * @deprecated Use pinsForQuest() or expandBasePinsToLevels(basePinsForImage(...), $n) instead.
     *
     * @return array<int, array{left: float, top: float, name: string, icon: string}>
     */
    public static function pinsForQuestLevels(?string $mapImage, int $levelCount): array
    {
        $base = self::basePinsForImage($mapImage);

        return self::expandBasePinsToLevels($base, $levelCount);
    }

    /**
     * SVG path d= for viewBox 0 0 1000 600; coordinates match %-of-map left/top.
     */
    public static function svgPathD(array $pinsResolved): string
    {
        if (count($pinsResolved) < 2) {
            return '';
        }
        $chunks = [];
        foreach ($pinsResolved as $i => $p) {
            $x = round((($p['left'] ?? 50) / 100) * 1000, 2);
            $y = round((($p['top'] ?? 50) / 100) * 600, 2);
            $chunks[] = ($i === 0 ? 'M' : 'L')." {$x} {$y}";
        }

        return implode(' ', $chunks);
    }

    public static function svgPathLengthApprox(array $pinsResolved): float
    {
        if (count($pinsResolved) < 2) {
            return 1000.0;
        }
        $len = 0.0;
        for ($i = 1, $c = count($pinsResolved); $i < $c; $i++) {
            $x1 = (($pinsResolved[$i - 1]['left'] ?? 50) / 100) * 1000;
            $y1 = (($pinsResolved[$i - 1]['top'] ?? 50) / 100) * 600;
            $x2 = (($pinsResolved[$i]['left'] ?? 50) / 100) * 1000;
            $y2 = (($pinsResolved[$i]['top'] ?? 50) / 100) * 600;
            $len += hypot($x2 - $x1, $y2 - $y1);
        }

        return max(1.0, $len);
    }
}
