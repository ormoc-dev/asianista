<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RandomEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'effect',
        'xp_reward',
        'xp_penalty',
        'target_type',
        'event_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get events by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Get a random event from active events
     */
    public static function getRandomEvent()
    {
        return self::active()->inRandomOrder()->first();
    }
}
