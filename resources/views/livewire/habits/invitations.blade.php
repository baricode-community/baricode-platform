<?php

use Livewire\Volt\Component;
use App\Models\HabitInvitation;
use App\Models\HabitParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public $invitations;

    public function mount()
    {
        $this->loadInvitations();
    }

    public function loadInvitations()
    {
        $this->invitations = HabitInvitation::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with(['habit', 'inviter'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function respondInvitation($invitationId, $response)
    {
        try {
            $invitation = HabitInvitation::where('id', $invitationId)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            DB::transaction(function () use ($invitation, $response) {
                if ($response === 'accept') {
                    // Accept invitation - create participant record
                    HabitParticipant::create([
                        'habit_id' => $invitation->habit_id,
                        'user_id' => Auth::id(),
                        'status' => 'approved',
                        'joined_at' => now(),
                    ]);

                    $invitation->update(['status' => 'accepted']);
                    session()->flash('success', 'Undangan diterima! Anda sekarang menjadi peserta habit.');
                } else {
                    // Decline invitation
                    $invitation->update(['status' => 'declined']);
                    session()->flash('success', 'Undangan ditolak.');
                }
            });

            $this->loadInvitations(); // Refresh data
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memproses undangan.');
        }
    }

    public function title()
    {
        return 'Undangan Habit - Daily Habit Tracker';
    }
}; ?>

<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('satu-tapak.index') }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    ← Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Undangan Habit</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Kelola undangan habit yang Anda terima dari teman</p>
        </div>

        @if($invitations->isEmpty())
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-gray-400 text-6xl mb-4">📧</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Undangan</h3>
                <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki undangan habit yang perlu ditanggapi.</p>
                <div class="mt-4">
                    <a href="{{ route('satu-tapak.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-6">
                @foreach($invitations as $invitation)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl dark:hover:shadow-gray-700/50 transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 dark:text-blue-300 font-semibold">
                                            {{ strtoupper(substr($invitation->inviter->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $invitation->inviter->name }} mengundang Anda
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $invitation->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $invitation->habit->name }}
                                    </h3>
                                    
                                    @if($invitation->habit->description)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                            {{ $invitation->habit->description }}
                                        </p>
                                    @endif

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Durasi:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ $invitation->habit->duration_days }} hari</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Mulai:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ $invitation->habit->start_date->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Berakhir:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ $invitation->habit->end_date->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Peserta:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100 ml-1">{{ $invitation->habit->approvedParticipants->count() }} orang</span>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Jadwal:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($invitation->habit->schedules as $schedule)
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300">
                                                    {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @if($invitation->message)
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-3 mb-4">
                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                            <strong>Pesan:</strong> {{ $invitation->message }}
                                        </p>
                                    </div>
                                @endif

                                <div class="flex space-x-3">
                                    <button wire:click="respondInvitation({{ $invitation->id }}, 'accept')"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                        <span wire:loading.remove wire:target="respondInvitation({{ $invitation->id }}, 'accept')">✅ Terima</span>
                                        <span wire:loading wire:target="respondInvitation({{ $invitation->id }}, 'accept')">Menerima...</span>
                                    </button>
                                    
                                    <button wire:click="respondInvitation({{ $invitation->id }}, 'decline')"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                        <span wire:loading.remove wire:target="respondInvitation({{ $invitation->id }}, 'decline')">❌ Tolak</span>
                                        <span wire:loading wire:target="respondInvitation({{ $invitation->id }}, 'decline')">Menolak...</span>
                                    </button>
                                    
                                    <a href="{{ route('satu-tapak.show', $invitation->habit->id) }}" 
                                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                        👁️ Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>