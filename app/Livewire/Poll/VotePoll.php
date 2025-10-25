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
        $this->loadUserVote();
        $this->showResults = $this->poll->isClosed();
    }

    public function loadUserVote()
    {
        $vote = PollVote::where('user_id', auth()->id())
            ->whereIn('poll_option_id', $this->poll->options->pluck('id'))
            ->first();

        $this->hasVoted = !is_null($vote);
        if ($this->hasVoted) {
            $this->selectedOption = $vote->poll_option_id;
        }
    }

    public function vote()
    {
        if ($this->poll->isClosed()) {
            session()->flash('error', 'Jajak pendapat ini telah ditutup.');
            return;
        }

        $this->validate();

        if ($this->hasVoted) {
            // Update pilihan yang ada
            PollVote::where('user_id', auth()->id())
                ->whereIn('poll_option_id', $this->poll->options->pluck('id'))
                ->delete();
                
            session()->flash('message', 'Pilihan Anda telah diperbarui!');
        } else {
            session()->flash('message', 'Terima kasih atas partisipasi Anda!');
        }

        PollVote::create([
            'poll_option_id' => $this->selectedOption,
            'user_id' => auth()->id()
        ]);

        $this->hasVoted = true;
        $this->poll->refresh();
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
