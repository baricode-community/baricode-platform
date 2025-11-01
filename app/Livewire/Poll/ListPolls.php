<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;

class ListPolls extends Component
{
    public $polls;
    public $own_polls;

    public function mount()
    {
        $this->polls = Poll::where([
            'is_public' => true,
            'user_id' => '!= ' . auth()->id()
        ])->get();
        $this->own_polls = Poll::where([
            'user_id' => auth()->id()
        ])->get();
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
        $this->is_public = $this->selectedPoll->is_public;
        $this->options = $this->selectedPoll->options->pluck('option_text')->toArray();
        $this->showCreateModal = true;
    }

    public function update()
    {
        $this->validate();

        $this->selectedPoll->update([
            'title' => $this->title,
            'description' => $this->description,
            'is_public' => $this->is_public
        ]);

        // Delete existing options and create new ones
        $this->selectedPoll->options()->delete();
        foreach ($this->options as $optionText) {
            if (!empty($optionText)) {
                $this->selectedPoll->options()->create(['option_text' => $optionText]);
            }
        }

        $this->showCreateModal = false;
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
        
        session()->flash('message', $message);
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.poll.list-polls');
    }
}
