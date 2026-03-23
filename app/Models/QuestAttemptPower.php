<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestAttemptPower extends Model
{
    use HasFactory;

    protected $fillable = [
        'quest_attempt_id',
        'power_name',
        'level',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuestAttempt::class, 'quest_attempt_id');
    }
}
