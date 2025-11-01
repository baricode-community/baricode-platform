<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use Livewire\Component;

class EditPoll extends Component
{
    public $poll;
    public $title;
    public $description;
    public $is_public;
    public $options = [];

    public function mount(Poll $poll)
    {
        $this->poll = $poll;
        $this->title = $poll->title;
        $this->description = $poll->description;
        $this->is_public = $poll->is_public;
        $this->options = $poll->options->pluck('text')->toArray();
    }

    public function render()
    {
        return view('livewire.poll.edit-poll');
    }
}
