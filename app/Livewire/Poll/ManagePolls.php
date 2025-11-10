<?php

namespace App\Livewire\Poll;

use App\Models\Content\Poll;
use Livewire\Component;
use Livewire\Attributes\Rule;

class ManagePolls extends Component
{
    public $polls;
    public $showCreateModal = false;
    public $selectedPoll = null;

    #[Rule('required|min:3|max:255')]
    public $title = '';

    #[Rule('required|min:3')]
    public $description = '';
    #[Rule('boolean')]
    public $is_public = true;

    #[Rule('required|array|min:2')]
    public $options = [];

    public function mount()
    {
        $this->loadPolls();
    }

    public function loadPolls()
    {
        $this->polls = Poll::where([
            'user_id' => auth()->id(),
            'is_public' => true
        ])
            ->with('options')
            ->latest()
            ->get();
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'description', 'options', 'selectedPoll']);
        $this->options = ['', '']; // Start with 2 empty options
        $this->showCreateModal = true;
    }

    public function addOption()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function create()
    {
        $this->validate();

        $poll = Poll::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => auth()->id(),
            'status' => 'open',
            'is_public' => $this->is_public,
        ]);

        foreach ($this->options as $optionText) {
            if (!empty($optionText)) {
                $poll->options()->create(['option_text' => $optionText]);
            }
        }

        $this->showCreateModal = false;
        $this->loadPolls();
        session()->flash('message', 'Poll created successfully!');
    }

    public function deletePoll($pollId)
    {
        $poll = Poll::find($pollId);
        if (!$poll || $poll->user_id !== auth()->id()) {
            return;
        }

        if (!$poll->isOpen()) {
            session()->flash('error', 'Poll yang sudah ditutup tidak dapat dihapus. Buka kembali poll untuk menghapus.');
            return;
        }

        $poll->options()->delete();
        $poll->delete();
        session()->flash('message', 'Poll deleted successfully!');
        $this->loadPolls();
    }

    public function render()
    {
        return view('livewire.poll.manage-polls');
    }
}
