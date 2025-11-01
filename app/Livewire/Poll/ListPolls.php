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

    public function render()
    {
        return view('livewire.poll.list-polls');
    }
}
