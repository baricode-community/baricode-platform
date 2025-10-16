<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display tasks assigned to the authenticated user
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get assignments for the authenticated user
        $assignments = TaskAssignment::with(['task', 'submissions'])
            ->where('user_id', $user->id)
            ->whereHas('task', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('assigned_at', 'desc')
            ->paginate(10);
        
        return view('pages.tasks.index', compact('assignments'));
    }

    /**
     * Show a specific task detail
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Find assignment for this user
        $assignment = TaskAssignment::with(['task', 'submissions.reviewer'])
            ->where('task_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $task = $assignment->task;
        
        // Check if user can still submit
        $canSubmit = $task->userCanSubmit($user);
        $submissionsCount = $task->userSubmissions($user)->count();
        
        return view('pages.tasks.show', compact('assignment', 'task', 'canSubmit', 'submissionsCount'));
    }

    /**
     * Submit a task
     */
    public function submit(Request $request, $id)
    {
        $user = auth()->user();
        
        // Validate the task assignment
        $assignment = TaskAssignment::where('task_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $task = $assignment->task;
        
        // Check if user can submit
        if (!$task->userCanSubmit($user)) {
            return redirect()->back()->with('error', 'Anda sudah mencapai batas maksimal submission untuk tugas ini.');
        }
        
        // Validate request
        $validated = $request->validate([
            'submission_content' => 'required|string',
            'files.*' => 'nullable|file|max:20480', // 20MB max per file
        ]);
        
        // Handle file uploads
        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('task-submissions', 'public');
                $filePaths[] = $path;
            }
        }
        
        // Create submission
        TaskSubmission::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'assignment_id' => $assignment->id,
            'submission_content' => $validated['submission_content'],
            'files' => $filePaths,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        
        return redirect()->route('tasks.show', $task->id)
            ->with('success', 'Submission berhasil dikirim! Menunggu review dari admin.');
    }

    /**
     * Display user's submissions history
     */
    public function submissions()
    {
        $user = auth()->user();
        
        $submissions = TaskSubmission::with(['task', 'reviewer'])
            ->where('user_id', $user->id)
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);
        
        return view('pages.tasks.submissions', compact('submissions'));
    }

    /**
     * View a specific submission
     */
    public function viewSubmission($id)
    {
        $user = auth()->user();
        
        $submission = TaskSubmission::with(['task', 'reviewer', 'assignment'])
            ->where('user_id', $user->id)
            ->findOrFail($id);
        
        return view('pages.tasks.submission-detail', compact('submission'));
    }
}
