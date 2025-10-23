<?php

namespace App\Livewire\TimeTracker;

use Livewire\Component;
use App\Models\TimeTrackerProject;
use App\Models\TimeTrackerTask;
use Livewire\Attributes\On;

class TimeTrackerMain extends Component
{
    public $selectedProjectId = null;

    #[On('project-created')]
    #[On('project-updated')]
    #[On('project-deleted')]
    public function refreshProjects()
    {
        // Trigger re-render
    }

    #[On('project-selected')]
    public function selectProject($projectId)
    {
        $this->selectedProjectId = $projectId;
    }

    #[On('scroll-to-task')]
    public function scrollToTask($taskId)
    {
        $task = TimeTrackerTask::find($taskId);
        if ($task) {
            $this->selectedProjectId = $task->project_id;
        }
    }

    public function render()
    {
        $projects = TimeTrackerProject::where('user_id', auth()->id())
            ->with(['tasks.entries'])
            ->latest()
            ->get();

        return view('livewire.time-tracker.time-tracker-main', [
            'projects' => $projects,
        ]);
    }
}
