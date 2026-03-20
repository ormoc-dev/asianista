<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'character',  // Add 'character' here
        'gender',
        'profile_pic',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // 🔥 Messaging relationships
    public function conversations()
    {
        return $this->belongsToMany(\App\Models\Conversation::class)
                    ->withPivot('last_read_at', 'deleted_at')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class);
    }

    public function questAttempts()
    {
        return $this->hasMany(\App\Models\QuestAttempt::class);
    }
    public function isOnline(): bool
{
    if (!$this->last_seen_at) {
        return false;
    }

    // online if active in last 5 minutes
    return $this->last_seen_at->gt(now()->subMinutes(5));
}
}
