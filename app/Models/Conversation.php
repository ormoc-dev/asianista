<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'is_group',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class)
        ->withPivot('last_read_at', 'deleted_at')
        ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Helper to get last message quickly
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    
public function unreadCountFor(User $user): int
{
    $pivot = $this->participants()
        ->where('user_id', $user->id)
        ->first()
        ?->pivot;

    $query = $this->messages()
        ->where('user_id', '!=', $user->id); // don’t count own messages

    if ($pivot && $pivot->last_read_at) {
        $query->where('created_at', '>', $pivot->last_read_at);
    }

    return $query->count();
}
}
