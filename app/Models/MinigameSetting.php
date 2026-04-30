<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinigameSetting extends Model
{
    use HasFactory;

    protected $table = 'minigames_settings';

    protected $fillable = [
        'slug',
        'name',
        'type',
        'mechanics',
        'gamification',
        'best_for',
        'image',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(MinigameAssignment::class, 'minigame_setting_id');
    }
}
