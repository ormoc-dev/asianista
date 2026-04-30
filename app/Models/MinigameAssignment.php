<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinigameAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'minigame_setting_id',
        'teacher_id',
        'grade_id',
        'section_id',
        'paragraph',
        'is_active',
        'starts_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(MinigameSetting::class, 'minigame_setting_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
