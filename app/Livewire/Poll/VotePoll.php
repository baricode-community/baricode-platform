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
            session()->flash('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
            return;
        }

        if ($this->poll->isOpen()) {
            $this->poll->close();
            session()->flash('message', 'Jajak pendapat telah ditutup.');
        } else {
            $this->poll->open();
            session()->flash('message', 'Jajak pendapat telah dibuka.');
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

    public function cancelVote()
    {
        if (!$this->hasVoted) {
            return;
        }

        PollVote::where('user_id', auth()->id())
            ->whereIn('poll_option_id', $this->poll->options->pluck('id'))
            ->delete();

        $this->hasVoted = false;
        $this->selectedOption = null;
        $this->poll->refresh();

        session()->flash('message', 'Pilihan Anda telah dibatalkan.');
    }

    public function copyResults()
    {
        $results = $this->results;
        $totalVotes = $results->sum('votes');
        
        $copyText = "HASIL POLLING: {$this->poll->question}\n";
        $copyText .= "==========\n";
        
        foreach ($results as $result) {
            $copyText .= "📊 {$result['text']}\n";
            $copyText .= "   Suara: {$result['votes']} ({$result['percentage']}%)\n";
            
            if (count($result['participants']) > 0) {
                $copyText .= "   Pemilih:\n";
                foreach ($result['participants'] as $participant) {
                    $copyText .= "   - {$participant['name']} ({$participant['voted_at']})\n";
                }
            }
            $copyText .= "\n";
        }
        
        $copyText .= "=====\n";
        $copyText .= "Total Suara: {$totalVotes}\n";
        $copyText .= "Dibuat oleh: {$this->poll->user->name}\n";
        $copyText .= "Tanggal Dibuat: {$this->poll->created_at->format('d M Y, H:i')}\n";
        $copyText .= "Alamat Poll: " . route('polls.show', $this->poll->id) . "\n";
        $copyText .= "Status: " . ($this->poll->isOpen() ? "AKTIF" : "DITUTUP");
        
        $this->dispatch('copy-to-clipboard', text: $copyText);
        session()->flash('message', 'Hasil polling telah disalin ke clipboard!');
    }

    public function render()
    {
        return view('livewire.poll.vote-poll', [
            'results' => $this->results
        ]);
    }
}
