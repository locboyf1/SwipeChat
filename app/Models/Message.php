<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    protected $fillable = [
        'content',
        'sender_id',
        'receiver_id',
        'read_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'content' => 'encrypted',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getIsMeAttribute()
    {
        if (! Auth::check()) {
            return false;
        }
        $me = Auth::user();
        $isMe = $this->sender_id === $me->id;

        return $isMe;
    }
}
