<?php

use Livewire\Component;

new class extends Component {
    public $friends;

    public function mount()
    {
        $this->friends = auth()->user()->friends;
    }
};
?>

<div>
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
            <button class="btn filterDiscussionsBtn active show" data-toggle="list" data-filter="all">All</button>
            <button class="btn filterDiscussionsBtn" data-toggle="list" data-filter="read">Read</button>
            <button class="btn filterDiscussionsBtn" data-toggle="list" data-filter="unread">Unread</button>
        </div>
        <div class="discussions">
            <h1>Discussions</h1>
            <div class="list-group" id="chats" role="tablist">
                @foreach ($friends as $friend)
                    <a href="javascript:void(0)" class="filterDiscussions all unread single" id="list-chat-list"
                        role="tab"
                        wire:click.prevent="$dispatch('loadConversation',{ receiverId: {{ $friend->id }}})">
                        <img class="avatar-md" src="{{ $friend->avatar_path }}" data-toggle="tooltip"
                            data-placement="top" title="{{ $friend->name }}" alt="Ảnh đại diện">
                        <div class="status">
                            <i class="material-icons online">fiber_manual_record</i>
                        </div>
                        <div class="new bg-yellow">
                            <span>+7</span>
                        </div>
                        <div class="data">
                            <h5>{{ $friend->name }}</h5>
                            <span>Mon</span>
                            <p>A new feature has been updated to your account. Check it out...
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
