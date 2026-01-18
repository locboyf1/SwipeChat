<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Events\SendMessage;
use Illuminate\Support\Facades\Log;

new class extends Component {
    public $content = [];
    public $message;

    public function sendAMessage()
    {
        SendMessage::dispatch($this->message, 'user' . session()->getId(), now());
        $this->message = '';
    }

    #[On('echo:chat-channel,SendMessage')]
    public function onMessageReceived($event)
    {
        $this->content[] = [
            'message' => $event['message'],
            'user' => $event['user'],
            'time' => $event['time'],
        ];
    }
};
?>

<div>
    @foreach ($content as $item)
        <p>{{ $item['message'] }}</p>
        <p>{{ $item['user'] }}</p>
        <p>{{ $item['time'] }}</p>
    @endforeach
    <form wire:submit="sendAMessage">
        <input type="text" wire:model="message">
        <div wire:loading>loading...</div>
        <button type="submit">Send</button>
    </form>
</div>
