<?php

namespace App\Livewire\TimeTracker;

use Livewire\Component;
use App\Models\TimeTrackerProject;
use Livewire\Attributes\Validate;

class ProjectManager extends Component
{
    public $showModal = false;
    public $editMode = false;
    public $projectId = null;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    public function openCreateModal()
    {
        $this->reset(['title', 'description', 'projectId', 'editMode']);
        $this->showModal = true;
    }

    public function openEditModal($projectId)
    {
        $project = TimeTrackerProject::findOrFail($projectId);
        
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $this->projectId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $project = TimeTrackerProject::findOrFail($this->projectId);
            
            if ($project->user_id !== auth()->id()) {
                abort(403);
            }

            $project->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);

            $this->dispatch('project-updated');
        } else {
            TimeTrackerProject::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
            ]);

            $this->dispatch('project-created');
        }

        $this->closeModal();
    }

    public function delete($projectId)
    {
        $project = TimeTrackerProject::findOrFail($projectId);
        
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->delete();
        $this->dispatch('project-deleted');
    }

    public function toggleCompletion($projectId)
    {
        $project = TimeTrackerProject::findOrFail($projectId);
        
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $result = $project->toggleCompletion();
        
        if (!$result['success']) {
            session()->flash('error', $result['message']);
        } else {
            session()->flash('success', $result['message']);
        }

        $this->dispatch('project-updated');
    }

    public function selectProject($projectId)
    {
        $this->dispatch('project-selected', projectId: $projectId);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'description', 'projectId', 'editMode']);
    }

    public function render()
    {
        $projects = TimeTrackerProject::where('user_id', auth()->id())
            ->withCount('tasks')
            ->latest()
            ->get();

        return view('livewire.time-tracker.project-manager', [
            'projects' => $projects,
        ]);
    }
}
