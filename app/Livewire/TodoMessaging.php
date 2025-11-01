<?php

namespace App\Livewire;

use App\Models\KanboardTodo;
use App\Models\TodoMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\On;

class TodoMessaging extends Component
{
    use AuthorizesRequests;

    public KanboardTodo $todo;
    public string $newMessage = '';
    public $messages = [];
    public bool $showMessaging = false;
    public int $lastMessageId = 0;

    public function mount(KanboardTodo $todo)
    {
        $this->todo = $todo;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        // Ensure user can view messages
        if (!$this->todo->canViewMessages(auth()->user())) {
            return;
        }

        $messagesQuery = TodoMessage::with('user')
            ->forTodo($this->todo)
            ->orderBy('created_at', 'asc');

        $this->messages = $messagesQuery->get()->toArray();
        
        // Update last message ID for polling
        $lastMessage = $messagesQuery->latest()->first();
        if ($lastMessage) {
            $this->lastMessageId = $lastMessage->id;
        }
    }

    public function sendMessage()
    {
        // Check if user can send messages
        if (!$this->todo->canSendMessage(auth()->user())) {
            $this->addError('newMessage', 'Anda tidak memiliki izin untuk mengirim pesan.');
            return;
        }

        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        $message = TodoMessage::create([
            'kanboard_todo_id' => $this->todo->id,
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();

        // Dispatch event to other components on the same page
        $this->dispatch('todo-message-sent', todoId: $this->todo->id);

        session()->flash('message-success', 'Pesan berhasil dikirim!');
    }

    public function checkForNewMessages()
    {
        // Poll for new messages
        if (!$this->todo->canViewMessages(auth()->user())) {
            return;
        }

        $newMessages = TodoMessage::where('kanboard_todo_id', $this->todo->id)
            ->where('id', '>', $this->lastMessageId)
            ->exists();

        if ($newMessages) {
            $this->loadMessages();
        }
    }

    public function toggleMessaging()
    {
        $this->showMessaging = !$this->showMessaging;
        
        if ($this->showMessaging) {
            $this->loadMessages();
        }
    }

    #[On('todo-message-sent')]
    public function onMessageSent($todoId)
    {
        // Refresh messages when a message is sent from any component
        if ($todoId == $this->todo->id) {
            $this->loadMessages();
        }
    }

    public function render()
    {
        return view('livewire.todo-messaging', [
            'canSendMessage' => $this->todo->canSendMessage(auth()->user()),
            'canViewMessage' => $this->todo->canViewMessages(auth()->user()),
        ]);
    }
}
