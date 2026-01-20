<?php

use App\Models\Message;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $friends = [];

    public $receiverId;

    public function mount()
    {
        $this->friends = auth()->user()->friends->sortByDesc('number_of_unread_messages')->toArray();
        $this->receiverId = 0;
    }

    #[On('loadConversation')]
    public function loadConversation($receiverId)
    {
        $this->receiverId = $receiverId;
        $this->friends = array_map(function ($friend) use ($receiverId) {
            if ($friend['id'] == $receiverId) {
                $friend['number_of_unread_messages'] = 0;
                return $friend;
            } else {
                return $friend;
            }
        }, $this->friends);
        $this->friends = collect($this->friends)->sortByDesc('number_of_unread_messages')->toArray();
    }

    #[On('updateFriendship')]
    public function updateFriendship(Message $message)
    {
        $this->friends = array_map(function ($friend) use ($message) {
            if (Auth::user()->id == $message->sender_id) {
                if ($this->receiverId == $friend['id']) {
                    $friend['last_message'] = $message->content;
                    $friend['sender_id_last_message'] = $message->sender_id;
                    $friend['number_of_unread_messages'] = 0;

                    return $friend;
                }

                return $friend;
            } elseif ($friend['id'] == $message->sender_id) {
                if ($friend['id'] == $this->receiverId) {
                    $friend['last_message'] = $message->content;
                    $friend['sender_id_last_message'] = $message->sender_id;
                    $friend['number_of_unread_messages'] = 0;
                    return $friend;
                } else {
                    $friend['last_message'] = $message->content;
                    $friend['sender_id_last_message'] = $message->sender_id;
                    $friend['number_of_unread_messages']++;
                    return $friend;
                }
            } else {
                return $friend;
            }
        }, $this->friends);

        $this->friends = collect($this->friends)->sortByDesc('number_of_unread_messages')->toArray();
    }
};
?>

<div id="discussions" class="tab-pane fade active show">
    <div class="search">
        <form class="form-inline position-relative">
            <input type="search" class="form-control" id="conversations" placeholder="Search for conversations...">
            <button type="button" class="btn btn-link loop"><i class="material-icons">search</i></button>
        </form>
        <button class="btn create" data-toggle="modal" data-target="#startnewchat"><i
                class="material-icons">create</i></button>
    </div>
    <div class="list-group sort">
        <button class="btn filterDiscussionsBtn active show" data-toggle="list" data-filter="all">Tất cả</button>
        <button class="btn filterDiscussionsBtn" data-toggle="list" data-filter="read">Đã đọc</button>
        <button class="btn filterDiscussionsBtn" data-toggle="list" data-filter="unread">Chưa đọc</button>
    </div>
    <div class="discussions">
        <h1>Bạn bè</h1>
        <div class="list-group" id="chats" role="tablist">
            @foreach ($friends as $friend)
                <a href="javascript:void(0)"
                    class="filterDiscussions all single {{ $friend['number_of_unread_messages'] > 0 ? 'unread' : 'read' }}"
                    id="list-chat-list" role="tab"
                    wire:click.prevent="$dispatch('loadConversation',{ receiverId: {{ $friend['id'] }}})">
                    <img class="avatar-md" src="{{ $friend['avatar_path'] }}" data-toggle="tooltip" data-placement="top"
                        title="{{ $friend['name'] }}" alt="Ảnh đại diện">
                    <div class="status">
                        <i class="material-icons online">fiber_manual_record</i>
                    </div>
                    @if ($friend['number_of_unread_messages'] > 0)
                        <div class="new bg-yellow">
                            <span>{{ $friend['number_of_unread_messages'] }}</span>
                        </div>
                    @endif
                    <div class="data">
                        <h5>{{ $friend['name'] }}</h5>
                        <span></span>
                        <p>{{ ($friend['sender_id_last_message'] == Auth::user()->id ? 'Bạn: ' : '') . $friend['last_message'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
