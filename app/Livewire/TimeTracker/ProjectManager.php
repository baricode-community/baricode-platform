<?php

namespace App\Livewire\TimeTracker;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TimeTrackerProject;
use Livewire\Attributes\Validate;

class ProjectManager extends Component
{
    use WithPagination;
    public $showModal = false;
    public $editMode = false;
    public $projectId = null;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    // Search, Filter, and Sort
    public $search = '';
    public $filterStatus = 'all'; // all, completed, active
    public $sortBy = 'created_desc'; // created_desc, created_asc, updated_desc, updated_asc, title_asc, title_desc

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

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'description', 'projectId', 'editMode']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = TimeTrackerProject::where('user_id', auth()->id())
            ->withCount('tasks');

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by completion status
        if ($this->filterStatus === 'completed') {
            $query->where('is_completed', true);
        } elseif ($this->filterStatus === 'active') {
            $query->where('is_completed', false);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'created_asc':
                $query->oldest();
                break;
            case 'updated_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'updated_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'created_desc':
            default:
                $query->latest();
                break;
        }

        $projects = $query->paginate(5);

        return view('livewire.time-tracker.project-manager', [
            'projects' => $projects,
        ]);
    }
}
