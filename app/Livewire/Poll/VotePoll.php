<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Rule;

class VotePoll extends Component
{
    public Poll $poll;
    
    #[Rule('required')]
    public $selectedOption = null;
    
    public $hasVoted = false;
    public $showResults = false;

    public function mount(Poll $poll)
    {
        $this->poll = $poll->load(['options.votes.user', 'user']);
        $this->checkIfVoted();
        $this->showResults = $this->hasVoted || $this->poll->isClosed();
    }

    public function checkIfVoted()
    {
        $this->hasVoted = PollVote::where('user_id', auth()->id())
            ->whereIn('poll_option_id', $this->poll->options->pluck('id'))
            ->exists();
    }

    public function vote()
    {
        if ($this->poll->isClosed()) {
            session()->flash('error', 'This poll is closed.');
            return;
        }

        if ($this->hasVoted) {
            session()->flash('error', 'You have already voted on this poll.');
            return;
        }

        $this->validate();

        PollVote::create([
            'poll_option_id' => $this->selectedOption,
            'user_id' => auth()->id()
        ]);

        $this->hasVoted = true;
        $this->showResults = true;
        $this->poll->refresh();
        
        session()->flash('message', 'Your vote has been recorded!');
    }

    public function toggleStatus()
    {
        if (!$this->poll->user_id === auth()->id()) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        if ($this->poll->isOpen()) {
            $this->poll->close();
            session()->flash('message', 'Poll has been closed.');
        } else {
            $this->poll->open();
            session()->flash('message', 'Poll has been opened.');
        }

        $this->poll->refresh();
        $this->showResults = $this->hasVoted || $this->poll->isClosed();
    }

    public function getResultsProperty()
    {
        $totalVotes = $this->poll->options->sum(fn($option) => $option->votes->count());
        
        return $this->poll->options->map(function($option) use ($totalVotes) {
            $votes = $option->votes->count();
            return [
                'id' => $option->id,
                'text' => $option->option_text,
                'votes' => $votes,
                'percentage' => $totalVotes > 0 ? round(($votes / $totalVotes) * 100, 1) : 0,
                'participants' => $option->votes->map(fn($vote) => [
                    'name' => $vote->user->name,
                    'voted_at' => $vote->created_at->format('d M Y, H:i')
                ])
            ];
        });
    }

    public function render()
    {
        return view('livewire.poll.vote-poll', [
            'results' => $this->results
        ]);
    }
}
