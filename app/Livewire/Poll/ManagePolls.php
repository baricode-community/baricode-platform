<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
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

    #[Rule('required|array|min:2')]
    public $options = [];

    public function mount()
    {
        $this->loadPolls();
    }

    public function loadPolls()
    {
        $this->polls = Poll::where('user_id', auth()->id())
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
            'status' => 'open'
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

    public function editPoll($pollId)
    {
        $this->selectedPoll = Poll::with('options')->find($pollId);
        
        if (!$this->selectedPoll->isOpen()) {
            session()->flash('error', 'Poll yang sudah ditutup tidak dapat diedit. Buka kembali poll untuk mengedit.');
            return;
        }

        $this->title = $this->selectedPoll->title;
        $this->description = $this->selectedPoll->description;
        $this->options = $this->selectedPoll->options->pluck('option_text')->toArray();
        $this->showCreateModal = true;
    }

    public function update()
    {
        $this->validate();

        $this->selectedPoll->update([
            'title' => $this->title,
            'description' => $this->description
        ]);

        // Delete existing options and create new ones
        $this->selectedPoll->options()->delete();
        foreach ($this->options as $optionText) {
            if (!empty($optionText)) {
                $this->selectedPoll->options()->create(['option_text' => $optionText]);
            }
        }

        $this->showCreateModal = false;
        $this->loadPolls();
        session()->flash('message', 'Poll updated successfully!');
    }

    public function toggleStatus($pollId)
    {
        $poll = Poll::find($pollId);
        if ($poll->isOpen()) {
            $poll->close();
            $message = 'Poll closed successfully!';
        } else {
            $poll->open();
            $message = 'Poll opened successfully!';
        }
        
        $this->loadPolls();
        session()->flash('message', $message);
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
