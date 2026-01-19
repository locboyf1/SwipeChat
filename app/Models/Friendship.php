<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Friendship extends Pivot
{
    protected $table = 'friendships';

    public $incrementing = true;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'last_message',
        'sender_id_last_message',
        'number_of_unread_messages',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    protected $casts = [
        'last_message' => 'encrypted',
    ];
}
