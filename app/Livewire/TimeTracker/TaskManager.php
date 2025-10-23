<?php

namespace App\Livewire\TimeTracker;

use Livewire\Component;
use App\Models\TimeTrackerTask;
use App\Models\TimeTrackerProject;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class TaskManager extends Component
{
    public $projectId = null;
    public $showModal = false;
    public $editMode = false;
    public $taskId = null;
    public $isProjectCompleted = false;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    public $estimatedHours = null;
    public $estimatedMinutes = null;
    public $estimatedSeconds = null;

    public function mount($projectId = null)
    {
        $this->projectId = $projectId;
        $this->checkProjectStatus();
    }

    #[On('project-selected')]
    public function setProject($projectId)
    {
        $this->projectId = $projectId;
        $this->checkProjectStatus();
    }

    public function checkProjectStatus()
    {
        if ($this->projectId) {
            $project = TimeTrackerProject::find($this->projectId);
            $this->isProjectCompleted = $project ? $project->is_completed : false;
        }
    }

    public function openCreateModal()
    {
        if (!$this->projectId) {
            session()->flash('error', 'Please select a project first.');
            return;
        }

        // Check if project is completed
        $project = TimeTrackerProject::find($this->projectId);
        if ($project && $project->is_completed) {
            session()->flash('error', 'Cannot create tasks in a completed project. Please mark the project as incomplete first.');
            return;
        }

        $this->reset(['title', 'description', 'taskId', 'editMode', 'estimatedHours', 'estimatedMinutes', 'estimatedSeconds']);
        $this->showModal = true;
    }

    public function openEditModal($taskId)
    {
        $task = TimeTrackerTask::findOrFail($taskId);
        
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        // Prevent editing completed tasks
        if ($task->is_completed) {
            session()->flash('error', 'Cannot edit a completed task. Please mark it as incomplete first.');
            return;
        }

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        
        if ($task->estimated_duration) {
            $this->estimatedHours = floor($task->estimated_duration / 3600);
            $this->estimatedMinutes = floor(($task->estimated_duration % 3600) / 60);
            $this->estimatedSeconds = $task->estimated_duration % 60;
        }

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $estimatedDuration = null;
        if ($this->estimatedHours || $this->estimatedMinutes || $this->estimatedSeconds) {
            $estimatedDuration = 
                ($this->estimatedHours ?? 0) * 3600 + 
                ($this->estimatedMinutes ?? 0) * 60 + 
                ($this->estimatedSeconds ?? 0);
        }

        if ($this->editMode) {
            $task = TimeTrackerTask::findOrFail($this->taskId);
            
            if ($task->user_id !== auth()->id()) {
                abort(403);
            }

            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'estimated_duration' => $estimatedDuration,
            ]);

            $this->dispatch('task-updated');
        } else {
            // Check if project is completed before creating new task
            $project = TimeTrackerProject::find($this->projectId);
            if ($project && $project->is_completed) {
                session()->flash('error', 'Cannot create tasks in a completed project.');
                $this->closeModal();
                return;
            }

            TimeTrackerTask::create([
                'project_id' => $this->projectId,
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'estimated_duration' => $estimatedDuration,
            ]);

            $this->dispatch('task-created');
        }

        $this->closeModal();
    }

    public function delete($taskId)
    {
        $task = TimeTrackerTask::findOrFail($taskId);
        
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        // Prevent deleting completed tasks
        if ($task->is_completed) {
            session()->flash('error', 'Cannot delete a completed task. Please mark it as incomplete first.');
            return;
        }

        // Check if task has saved entries
        if ($task->entries()->count() > 0) {
            session()->flash('error', 'Cannot delete task with time entries.');
            return;
        }

        $task->delete();
        $this->dispatch('task-deleted');
    }

    public function toggleCompletion($taskId)
    {
        $task = TimeTrackerTask::findOrFail($taskId);
        
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->toggleCompletion();
        $this->dispatch('task-updated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'description', 'taskId', 'editMode', 'estimatedHours', 'estimatedMinutes', 'estimatedSeconds']);
    }

    #[On('task-created')]
    #[On('task-updated')]
    #[On('task-deleted')]
    #[On('time-entry-saved')]
    #[On('project-updated')]
    public function refreshTasks()
    {
        $this->checkProjectStatus();
        // Trigger re-render
    }

    public function render()
    {
        $tasks = [];
        $project = null;
        
        if ($this->projectId) {
            $project = TimeTrackerProject::find($this->projectId);
            
            $tasks = TimeTrackerTask::where('project_id', $this->projectId)
                ->where('user_id', auth()->id())
                ->with(['entries', 'activeEntry'])
                ->latest()
                ->get();
        }

        return view('livewire.time-tracker.task-manager', [
            'tasks' => $tasks,
            'project' => $project,
        ]);
    }
}
