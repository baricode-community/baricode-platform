<?php

namespace App\Livewire\Poll;

use App\Models\Poll;
use App\Models\PollOption;
use Livewire\Component;

class EditPoll extends Component
{
    public $poll;
    public $title;
    public $description;
    public $is_public;
    public $is_active;
    public $options = []; // Will store ['id' => 'text'] for existing, [null => 'text'] for new

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $listeners = ['refreshOptions' => '$refresh'];

    public function mount(Poll $poll)
    {
        $this->poll = $poll;
        $this->title = $poll->title;
        $this->description = $poll->description;
        $this->is_public = $poll->is_public;
        $this->is_active = $poll->status === 'open';
        
        // Load existing options with their IDs
        $this->options = [];
        foreach ($poll->options as $option) {
            $this->options[$option->id] = $option->option_text;
        }
        
        // Pastikan minimal ada 2 opsi (buat di database jika kurang)
        if (count($this->options) < 2) {
            $needed = 2 - count($this->options);
            for ($i = 0; $i < $needed; $i++) {
                $newOption = PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => '',
                ]);
                $this->options[$newOption->id] = '';
            }
        }
    }

    public function addOption()
    {
        // Buat option baru langsung ke database
        $newOption = PollOption::create([
            'poll_id' => $this->poll->id,
            'option_text' => '',
        ]);
        
        // Tambahkan ke array options untuk UI
        $this->options[$newOption->id] = '';
        
        session()->flash('success', 'Opsi baru ditambahkan.');
    }

    public function removeOption($optionId)
    {
        if (count($this->options) > 2) {
            // Hapus dari database
            PollOption::where('id', $optionId)->delete();
            
            // Hapus dari array untuk update UI
            unset($this->options[$optionId]);
            
            session()->flash('success', 'Opsi berhasil dihapus.');
        } else {
            session()->flash('error', 'Minimal harus ada 2 opsi.');
        }
    }

    public function updatePoll()
    {
        // Validasi input utama
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Pastikan ada minimal 2 opsi yang tidak kosong
        $nonEmptyOptions = array_filter($this->options, function($option) {
            return !empty(trim($option));
        });

        if (count($nonEmptyOptions) < 2) {
            $this->addError('options', 'Minimal harus ada 2 opsi yang tidak kosong.');
            return;
        }

        // Update poll data
        $this->poll->update([
            'title' => $this->title,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'status' => $this->is_active ? 'open' : 'closed',
        ]);

        // Update teks options yang sudah ada di database
        foreach ($this->options as $optionId => $optionText) {
            $option = PollOption::find($optionId);
            if ($option) {
                $option->update(['option_text' => trim($optionText)]);
            }
        }

        session()->flash('success', 'Poll berhasil diupdate!');
        
        return redirect()->route('polls.show', $this->poll->id);
    }

    public function render()
    {
        return view('livewire.poll.edit-poll');
    }
}
