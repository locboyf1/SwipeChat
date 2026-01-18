<?php

use App\Events\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

new class extends Component {
    public ?User $receiver = null;

    public Collection $messages;

    public string $content = '';

    public bool $hasMore = true;

    public $authId;

    public function mount($receiverId = null)
    {
        $this->authId = auth()->id();
        $this->messages = new Collection();
        if ($receiverId) {
            $this->loadConversation($receiverId);
            $this->receiver = User::findOrFail($receiverId);
        }
    }

    public function loadMore()
    {
        if ($this->receiver === null || $this->messages->isEmpty()) {
            return;
        }

        $oldMessages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())->where('receiver_id', $this->receiver->id);
        })
            ->where('created_at', '<', $this->messages->first()->created_at)
            ->orWhere(function ($query) {
                $query->where('sender_id', $this->receiver->id)->where('receiver_id', auth()->id());
            })
            ->where('created_at', '<', $this->messages->first()->created_at)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $oldMessages = $oldMessages->reverse();

        $this->messages = $oldMessages->merge($this->messages);

        if ($oldMessages->count() < 10) {
            $this->hasMore = false;
        }
    }

    #[On('loadConversation')]
    public function loadConversation($receiverId)
    {
        $this->receiver = User::findOrFail($receiverId);
        $messages = Message::where(function ($query) use ($receiverId) {
            $query->where('sender_id', auth()->id())->where('receiver_id', $receiverId);
        })
            ->orWhere(function ($query) use ($receiverId) {
                $query->where('sender_id', $receiverId)->where('receiver_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $this->messages = $messages->reverse();

        if ($messages->count() < 10) {
            $this->hasMore = false;
        } else {
            $this->hasMore = true;
        }
    }

    public function sendMessage()
    {
        if (!$this->content || !$this->receiver) {
            return;
        }

        $message = Message::create([
            'content' => $this->content,
            'sender_id' => Auth::user()->id,
            'receiver_id' => $this->receiver->id,
        ]);

        $this->content = '';

        $this->messages->push($message);

        SendMessage::dispatch($message);

        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:chat.{authId},SendMessage')]
    public function receiveMessage($event)
    {
        $data = $event['message'];
        Log::info($data);
        if ($this->receiver) {
            if ($this->receiver && $this->receiver->id == $data['sender_id']) {
                $message = Message::find($data['id']);
                $this->messages = $this->messages->push($message);

                $this->dispatch('scroll-to-bottom');
            }
        }
    }
};
?>

<div style="height: 100%">
    @if ($receiver)
        <div class="babble tab-pane fade active show" id="list-chat" role="tabpanel" aria-labelledby="list-chat-list"
            style="height: 100%">
            <div class="chat" id="chat1" style="display: flex; flex-direction: column; height: 100%">
                <div class="top">
                    <div class="container">
                        <div class="col-md-12">
                            <div class="inside">
                                <a href="#"><img class="avatar-md" src="{{ $receiver->avatar_path }}"
                                        data-toggle="tooltip" data-placement="top" title="{{ $receiver->name }}"
                                        alt="Ảnh đại diện"></a>
                                <div class="status">
                                    <i class="material-icons online">fiber_manual_record</i>
                                </div>
                                <div class="data">
                                    <h5><a href="#">{{ $receiver->name }}</a></h5>
                                    <span>Active now</span>
                                </div>
                                <button class="btn connect d-md-block d-none" name="1"><i
                                        class="material-icons md-30">phone_in_talk</i></button>
                                <button class="btn connect d-md-block d-none" name="1"><i
                                        class="material-icons md-36">videocam</i></button>
                                <button class="btn d-md-block d-none"><i class="material-icons md-30">info</i></button>
                                <div class="dropdown">
                                    <button class="btn" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"><i class="material-icons md-30">more_vert</i></button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <button class="dropdown-item connect" name="1"><i
                                                class="material-icons">phone_in_talk</i>Voice Call</button>
                                        <button class="dropdown-item connect" name="1"><i
                                                class="material-icons">videocam</i>Video Call</button>
                                        <hr>
                                        <button class="dropdown-item"><i class="material-icons">clear</i>Clear
                                            History</button>
                                        <button class="dropdown-item"><i class="material-icons">block</i>Block
                                            Contact</button>
                                        <button class="dropdown-item"><i class="material-icons">delete</i>Delete
                                            Contact</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content" id="chat-content" wire:key="chat-content-{{ $receiver->id }}"
                    style="flex: 1; overflow-y: auto; height: auto !important" x-data="{
                        isLoading: false,
                        init() {
                            this.scrollToBottom();
                        },
                        scrollToBottom() {
                            this.$nextTick(() => {
                                this.$el.scrollTop = this.$el.scrollHeight;
                            });
                        },
                        handleScroll() {
                            if (this.$el.scrollTop <= 30 && !this.isLoading) {
                                if (!$wire.hasMore) {
                                    return;
                                }
                                this.isLoading = true;
                    
                                let oldHeight = this.$el.scrollHeight;
                                let oldScrollTop = this.$el.scrollTop;
                                $wire.loadMore().then(() => {
                                    this.$nextTick(() => {
                                        let newHeight = this.$el.scrollHeight;
                                        let diff = newHeight - oldHeight;
                                        this.$el.scrollTop = newHeight - oldHeight + oldScrollTop;
                                    });
                                }).finally(() => {
                                    this.isLoading = false;
                                });
                            }
                        }
                    }"
                    x-on:scroll="handleScroll" x-init="init()" x-on:scroll-to-bottom.window="scrollToBottom()">

                    <div class="container">
                        <div class="col-md-12">
                            {{-- <div class="date">
                                <hr>
                                <span>Yesterday</span>
                                <hr>
                            </div> --}}
                            @foreach ($messages as $message)
                                @if ($message->is_me)
                                    <div class="message me">
                                        <div class="text-main">
                                            <div class="text-group me">
                                                <div class="text me">
                                                    <p>{{ $message->content }}</p>
                                                </div>
                                            </div>
                                            <span>{{ $message->created_at->format('h:i A - d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="message">
                                        <img class="avatar-md" src="{{ $receiver->avatar_path }}" data-toggle="tooltip"
                                            data-placement="top" title="Keith" alt="Ảnh đại diện">
                                        <div class="text-main">
                                            <div class="text-group">
                                                <div class="text">
                                                    <p>{{ $message->content }}</p>
                                                </div>
                                            </div>
                                            <span>{{ $message->created_at->format('h:i A - d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="col-md-12">
                        <div class="bottom">
                            <form class="position-relative w-100" wire:submit.prevent="sendMessage">
                                @csrf
                                <textarea class="form-control" wire:model="content" placeholder="Nhập nội dung tin nhắn" rows="1"></textarea>
                                <button class="btn emoticons"><i class="material-icons">insert_emoticon</i></button>
                                <button type="submit" class="btn send"><i class="material-icons">send</i></button>
                            </form>
                            <label>
                                <input type="file">
                                <span class="btn attach d-sm-block d-none"><i
                                        class="material-icons">attach_file</i></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Chat -->
            <!-- Start of Call -->
            <div class="call" id="call1">
                <div class="content">
                    <div class="container">
                        <div class="col-md-12">
                            <div class="inside">
                                <div class="panel">
                                    <div class="participant">
                                        <img class="avatar-xxl" src="dist/img/avatars/avatar-female-5.jpg"
                                            alt="avatar">
                                        <span>Connecting</span>
                                    </div>
                                    <div class="options">
                                        <button class="btn option"><i class="material-icons md-30">mic</i></button>
                                        <button class="btn option"><i
                                                class="material-icons md-30">videocam</i></button>
                                        <button class="btn option call-end"><i
                                                class="material-icons md-30">call_end</i></button>
                                        <button class="btn option"><i
                                                class="material-icons md-30">person_add</i></button>
                                        <button class="btn option"><i
                                                class="material-icons md-30">volume_up</i></button>
                                    </div>
                                    <button class="btn back" name="1"><i
                                            class="material-icons md-24">chat</i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Call -->
        </div>
    @endif
</div>
