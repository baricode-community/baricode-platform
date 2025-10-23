<?php

namespace App\Livewire\TimeTracker;

use Livewire\Component;
use App\Models\TimeTrackerTask;
use App\Models\TimeTrackerEntry;
use Livewire\Attributes\On;

class TimeEntryTracker extends Component
{
    public $taskId;
    public $currentEntry = null;
    public $currentDuration = 0;

    public function mount($taskId)
    {
        $this->taskId = $taskId;
        $this->loadCurrentEntry();
    }

    public function loadCurrentEntry()
    {
        $this->currentEntry = TimeTrackerEntry::where('task_id', $this->taskId)
            ->where('user_id', auth()->id())
            ->where('is_running', true)
            ->first();

        if ($this->currentEntry) {
            $this->currentDuration = $this->currentEntry->getCurrentDuration();
        }
    }

    public function start()
    {
        // Stop any other running entries for this user
        TimeTrackerEntry::where('user_id', auth()->id())
            ->where('is_running', true)
            ->get()
            ->each(function ($entry) {
                $entry->stop();
            });

        // Create new entry
        $this->currentEntry = TimeTrackerEntry::create([
            'task_id' => $this->taskId,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'is_running' => true,
        ]);

        $this->dispatch('timer-started');
    }

    public function stop()
    {
        if ($this->currentEntry) {
            $this->currentEntry->stop();
            $this->currentEntry = null;
            $this->currentDuration = 0;
            $this->dispatch('timer-stopped');
            $this->dispatch('time-entry-saved');
        }
    }

    public function discard()
    {
        if ($this->currentEntry && $this->currentEntry->is_running) {
            $this->currentEntry->delete();
            $this->currentEntry = null;
            $this->currentDuration = 0;
            $this->dispatch('timer-discarded');
        }
    }

    #[On('refresh-timer')]
    public function refreshTimer()
    {
        if ($this->currentEntry && $this->currentEntry->is_running) {
            $this->currentDuration = $this->currentEntry->getCurrentDuration();
        }
    }

    public function render()
    {
        $task = TimeTrackerTask::with(['entries', 'activeEntry'])
            ->findOrFail($this->taskId);

        $isOverEstimate = $task->isOverEstimate();

        return view('livewire.time-tracker.time-entry-tracker', [
            'task' => $task,
            'isOverEstimate' => $isOverEstimate,
            'isRunning' => $this->currentEntry && $this->currentEntry->is_running,
        ]);
    }
}
