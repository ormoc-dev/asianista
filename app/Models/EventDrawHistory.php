<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDrawHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'random_event_id',
        'teacher_id',
        'event_title',
        'event_description',
        'event_type',
        'xp_reward',
        'xp_penalty',
        'target_type',
        'effect',
    ];

    public function randomEvent()
    {
        return $this->belongsTo(RandomEvent::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
