<?php

namespace App\Observers;

use App\Models\Friendship;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        $friendship = Friendship::where(function ($query) use ($message) {
            $query->where('sender_id', $message->sender_id)->where('receiver_id', $message->receiver_id);
        })->orWhere(function ($query) use ($message) {
            $query->where('sender_id', $message->receiver_id)->where('receiver_id', $message->sender_id);
        })->first();

        if ($friendship) {
            $friendship->last_message = $message->content;
            $friendship->sender_id_last_message = $message->sender_id;
            $friendship->number_of_unread_messages += 1;
            $friendship->save();
        }

        Log::info('đã chạy');
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        if ($message->read_at != null) {

            $friendship = Friendship::where(function ($query) use ($message) {
                $query->where('sender_id', $message->sender_id)->where('receiver_id', $message->receiver_id);
            })->orWhere(function ($query) use ($message) {
                $query->where('sender_id', $message->receiver_id)->where('receiver_id', $message->sender_id);
            })->first();

            $listMessage = Message::where(function ($query) use ($message) {
                $query->where('sender_id', $message->sender_id)->where('receiver_id', $message->receiver_id)
                    ->where('created_at', '<', $message->created_at)
                    ->whereNull('read_at');
            })->orWhere(function ($query) use ($message) {
                $query->where('sender_id', $message->receiver_id)->where('receiver_id', $message->sender_id)
                    ->where('created_at', '<', $message->created_at)
                    ->whereNull('read_at');
            })->get();

            $listMessage->each(function ($message) {
                $message->read_at = now();
                $message->save();
            });

            if ($friendship) {
                $friendship->number_of_unread_messages = 0;
                $friendship->save();
            }

            Log::info('đã chạy');
        }
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
