<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function getAvatarPathAttribute()
    {
        if (! $this->avatar) {
            return 'https://hanhtrinhdelta.edu.vn/wp-content/uploads/2025/08/avatar-mac-dinh-don-gian-tren-cac-nen-tang.jpg';
        }

        return $this->avatar;
    }

    public function sendAndAcceptedFriendships()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')->wherePivot('status', 'accepted');
    }

    public function receivedAndAcceptedFriendships()
    {
        return $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')->wherePivot('status', 'accepted');
    }

    public function getFriendsAttribute()
    {
        return $this->sendAndAcceptedFriendships()->get()->merge($this->receivedAndAcceptedFriendships()->get());
    }
}
