<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quest_id', 'question', 'type', 'points', 'level', 'options', 'answer'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
