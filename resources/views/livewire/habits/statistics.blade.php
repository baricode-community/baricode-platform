<?php

use Livewire\Volt\Component;
use App\Models\Habit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public Habit $habit;
    public $statistics = [];

    public function mount($habitId)
    {
        $this->habit = Habit::with(['logs.user', 'approvedParticipants.user'])
                           ->findOrFail($habitId);
        
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $totalDays = $this->habit->duration_days;
        $daysPassed = max(0, now()->diffInDays($this->habit->start_date, false));
        $daysPassed = min($daysPassed, $totalDays);

        $participants = $this->habit->approvedParticipants;
        $logs = $this->habit->logs;

        $this->statistics = [
            'total_days' => $totalDays,
            'days_passed' => $daysPassed,
            'days_remaining' => max(0, $totalDays - $daysPassed),
            'progress_percentage' => $totalDays > 0 ? round(($daysPassed / $totalDays) * 100) : 0,
            'total_participants' => $participants->count(),
            'total_logs' => $logs->count(),
            'average_participation' => $daysPassed > 0 && $participants->count() > 0 
                ? round(($logs->count() / ($daysPassed * $participants->count())) * 100) 
                : 0,
            'participant_stats' => $this->calculateParticipantStats($participants, $logs, $daysPassed),
            'daily_logs' => $this->calculateDailyLogs($logs),
            'weekly_summary' => $this->calculateWeeklySummary($logs)
        ];
    }

    private function calculateParticipantStats($participants, $logs, $daysPassed)
    {
        return $participants->map(function ($participant) use ($logs, $daysPassed) {
            $userLogs = $logs->where('user_id', $participant->user_id);
            $userLogCount = $userLogs->count();
            
            return [
                'user' => $participant->user,
                'logs_count' => $userLogCount,
                'participation_rate' => $daysPassed > 0 ? round(($userLogCount / $daysPassed) * 100) : 0,
                'recent_activity' => $userLogs->sortByDesc('log_date')->first()
            ];
        })->sortByDesc('participation_rate');
    }

    private function calculateDailyLogs($logs)
    {
        return $logs->groupBy(function ($log) {
            return $log->log_date->format('Y-m-d');
        })->map(function ($dayLogs) {
            return [
                'date' => $dayLogs->first()->log_date,
                'count' => $dayLogs->count(),
                'logs' => $dayLogs
            ];
        })->sortByDesc('date')->take(14);
    }

    private function calculateWeeklySummary($logs)
    {
        return $logs->groupBy(function ($log) {
            return $log->log_date->format('Y-W');
        })->map(function ($weekLogs) {
            return [
                'week' => $weekLogs->first()->log_date->format('W'),
                'year' => $weekLogs->first()->log_date->format('Y'),
                'count' => $weekLogs->count(),
                'unique_users' => $weekLogs->pluck('user_id')->unique()->count()
            ];
        })->sortByDesc(function ($item) {
            return $item['year'] . $item['week'];
        })->take(8);
    }

    public function title()
    {
        return 'Statistik - ' . $this->habit->name;
    }
}; ?>

<div>
    <div class="">
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('satu-tapak.show', $habit->id) }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    ← Kembali
                </a>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <span class="text-blue-600 dark:text-blue-400 text-2xl">📅</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hari Berlalu</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statistics['days_passed'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">dari {{ $statistics['total_days'] }} hari</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <span class="text-green-600 dark:text-green-400 text-2xl">✅</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Aktivitas</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statistics['total_logs'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">log dicatat</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                        <span class="text-purple-600 dark:text-purple-400 text-2xl">👥</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Peserta</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statistics['total_participants'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">orang</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                        <span class="text-yellow-600 dark:text-yellow-400 text-2xl">📊</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Partisipasi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statistics['average_participation'] }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">dari semua peserta</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Progress Habit</h2>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <span>{{ $statistics['days_passed'] }} hari</span>
                        <span>{{ $statistics['total_days'] }} hari</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" 
                             style="width: {{ $statistics['progress_percentage'] }}%"></div>
                    </div>
                    <div class="text-center mt-2">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistics['progress_percentage'] }}%</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Participant Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Statistik Peserta</h2>
                
                @if($statistics['participant_stats']->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada data peserta.</p>
                @else
                    <div class="space-y-4">
                        @foreach($statistics['participant_stats'] as $stat)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                            {{ strtoupper(substr($stat['user']->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $stat['user']->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $stat['logs_count'] }} aktivitas
                                            @if($stat['recent_activity'])
                                                • Terakhir: {{ $stat['recent_activity']->log_date->format('d M') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $stat['participation_rate'] }}%</p>
                                    <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stat['participation_rate'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Daily Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aktivitas Harian (14 hari terakhir)</h2>
                
                @if($statistics['daily_logs']->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada aktivitas yang dicatat.</p>
                @else
                    <div class="space-y-2">
                        @foreach($statistics['daily_logs'] as $day)
                            <div class="flex items-center justify-between py-2">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $day['date']->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $day['date']->format('l') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-sm font-medium">
                                        {{ $day['count'] }} aktivitas
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Weekly Summary -->
        @if($statistics['weekly_summary']->isNotEmpty())
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ringkasan Mingguan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($statistics['weekly_summary'] as $week)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Minggu {{ $week['week'] }}, {{ $week['year'] }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $week['count'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $week['unique_users'] }} peserta aktif</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>