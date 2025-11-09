<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitSchedule;
use App\Models\HabitInvitation;
use App\Models\HabitParticipant;
use App\Models\HabitLog;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HabitController extends Controller
{
    /**
     * Display a listing of habits
     */
    public function index()
    {
        $user = Auth::user();
        
        $myHabits = $user->habits()
            ->with(['schedules', 'participants'])
            ->orderBy('created_at', 'desc')
            ->get();

        $participatingHabits = $user->habitParticipations()
            ->with(['habit.schedules', 'habit.creator'])
            ->where('status', 'approved')
            ->get()
            ->pluck('habit');

        return view('habits.index', compact('myHabits', 'participatingHabits'));
    }

    /**
     * Show the form for creating a new habit
     */
    public function create()
    {
        return view('habits.create');
    }

    /**
     * Store a newly created habit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date|after_or_equal:today',
            'schedules' => 'required|array|min:1',
            'schedules.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.time' => 'required|date_format:H:i',
        ]);

        $habit = Habit::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
            'duration_days' => $validated['duration_days'],
            'start_date' => $validated['start_date'],
            'is_active' => true,
        ]);

        // Create schedules
        foreach ($validated['schedules'] as $schedule) {
            HabitSchedule::create([
                'habit_id' => $habit->id,
                'day_of_week' => $schedule['day'],
                'scheduled_time' => $schedule['time'],
                'is_active' => true,
            ]);
        }

        // Add creator as first participant
        HabitParticipant::create([
            'habit_id' => $habit->id,
            'user_id' => Auth::id(),
            'status' => 'approved',
            'joined_at' => now(),
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()
            ->route('satu-tapak.habits.show', $habit)
            ->with('success', 'Habit berhasil dibuat!');
    }

    /**
     * Display the specified habit
     */
    public function show(Habit $habit)
    {
        $habit->load(['creator', 'schedules', 'approvedParticipants.user', 'logs.user']);
        
        $userIsParticipant = $habit->hasParticipant(Auth::id());
        $todayLog = null;
        
        if ($userIsParticipant) {
            $todayLog = HabitLog::where([
                'habit_id' => $habit->id,
                'user_id' => Auth::id(),
                'log_date' => today(),
            ])->first();
        }

        return view('habits.show', compact('habit', 'userIsParticipant', 'todayLog'));
    }

    /**
     * Show the form for editing the specified habit
     */
    public function edit(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($habit->is_locked) {
            return redirect()
                ->route('satu-tapak.habits.show', $habit)
                ->with('error', 'Habit ini sudah terkunci dan tidak bisa diubah.');
        }

        $habit->load('schedules');
        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified habit
     */
    public function update(Request $request, Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($habit->is_locked) {
            return redirect()
                ->route('satu-tapak.habits.show', $habit)
                ->with('error', 'Habit ini sudah terkunci dan tidak bisa diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $habit->update($validated);

        return redirect()
            ->route('satu-tapak.habits.show', $habit)
            ->with('success', 'Habit berhasil diperbarui!');
    }

    /**
     * Remove the specified habit
     */
    public function destroy(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $habit->delete();

        return redirect()
            ->route('satu-tapak.habits.index')
            ->with('success', 'Habit berhasil dihapus!');
    }

    /**
     * Lock the habit
     */
    public function lock(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $habit->lock();

        return redirect()
            ->route('satu-tapak.habits.show', $habit)
            ->with('success', 'Habit berhasil dikunci. Sekarang tidak dapat diubah lagi.');
    }

    /**
     * Show form to invite users
     */
    public function invite(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where('id', '!=', Auth::id())
            ->whereNotIn('id', function($query) use ($habit) {
                $query->select('user_id')
                      ->from('habit_participants')
                      ->where('habit_id', $habit->id);
            })
            ->get();

        return view('habits.invite', compact('habit', 'users'));
    }

    /**
     * Send invitation to users
     */
    public function sendInvitation(Request $request, Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'message' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            // Check if invitation already exists
            $existingInvitation = HabitInvitation::where([
                'habit_id' => $habit->id,
                'invitee_id' => $userId,
                'status' => 'pending'
            ])->exists();

            if (!$existingInvitation) {
                HabitInvitation::create([
                    'habit_id' => $habit->id,
                    'inviter_id' => Auth::id(),
                    'invitee_id' => $userId,
                    'message' => $validated['message'],
                ]);
            }
        }

        return redirect()
            ->route('satu-tapak.habits.show', $habit)
            ->with('success', 'Undangan berhasil dikirim!');
    }

    /**
     * Show pending invitations
     */
    public function invitations()
    {
        $invitations = HabitInvitation::where('invitee_id', Auth::id())
            ->where('status', 'pending')
            ->with(['habit', 'inviter'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('habits.invitations', compact('invitations'));
    }

    /**
     * Respond to invitation
     */
    public function respondInvitation(Request $request, HabitInvitation $invitation)
    {
        if ($invitation->invitee_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'response' => 'required|in:accept,reject',
        ]);

        if ($validated['response'] === 'accept') {
            $invitation->accept();
            $message = 'Undangan berhasil diterima! Anda sekarang menjadi peserta habit ini.';
        } else {
            $invitation->reject();
            $message = 'Undangan berhasil ditolak.';
        }

        return redirect()
            ->route('satu-tapak.habits.invitations')
            ->with('success', $message);
    }

    /**
     * Log habit activity
     */
    public function log(Request $request, Habit $habit)
    {
        if (!$habit->hasParticipant(Auth::id())) {
            abort(403, 'Anda bukan peserta habit ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if already logged for today
        $existingLog = HabitLog::where([
            'habit_id' => $habit->id,
            'user_id' => Auth::id(),
            'log_date' => today(),
        ])->first();

        if ($existingLog) {
            return redirect()
                ->route('satu-tapak.habits.show', $habit)
                ->with('error', 'Anda sudah melakukan log untuk hari ini.');
        }

        HabitLog::create([
            'habit_id' => $habit->id,
            'user_id' => Auth::id(),
            'log_date' => today(),
            'log_time' => now(),
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'logged_at' => now(),
        ]);

        return redirect()
            ->route('satu-tapak.habits.show', $habit)
            ->with('success', 'Log aktivitas berhasil disimpan!');
    }

    /**
     * Show habit statistics
     */
    public function statistics(Habit $habit)
    {
        if (!$habit->hasParticipant(Auth::id()) && $habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $habit->load(['logs.user', 'approvedParticipants.user']);
        
        $stats = [
            'total_days' => $habit->duration_days,
            'elapsed_days' => now()->diffInDays($habit->start_date),
            'remaining_days' => $habit->remainingDays(),
            'total_logs' => $habit->logs->count(),
            'present_logs' => $habit->logs->where('status', 'present')->count(),
            'absent_logs' => $habit->logs->where('status', 'absent')->count(),
            'late_logs' => $habit->logs->where('status', 'late')->count(),
        ];

        return view('habits.statistics', compact('habit', 'stats'));
    }
}
