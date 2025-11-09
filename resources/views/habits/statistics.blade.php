@extends('components.layouts.app')

@section('title', 'Statistik - ' . $habit->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Statistik Habit</h1>
                <p class="text-gray-600 mt-1">{{ $habit->name }}</p>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-lg">📅</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-lg">⏱️</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Hari Berlalu</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['elapsed_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 text-lg">⏰</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sisa Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['remaining_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 text-lg">👥</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Peserta</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $habit->approvedParticipants->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Overall Progress -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Progress Keseluruhan</h2>
            
            @php
                $totalLogs = $stats['total_logs'];
                $presentPercentage = $totalLogs > 0 ? round(($stats['present_logs'] / $totalLogs) * 100) : 0;
                $latePercentage = $totalLogs > 0 ? round(($stats['late_logs'] / $totalLogs) * 100) : 0;
                $absentPercentage = $totalLogs > 0 ? round(($stats['absent_logs'] / $totalLogs) * 100) : 0;
            @endphp

            <div class="space-y-6">
                <!-- Progress Bar -->
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Kehadiran</span>
                        <span class="font-medium">{{ $presentPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $presentPercentage }}%"></div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['present_logs'] }}</div>
                        <div class="text-sm text-gray-600">Hadir</div>
                        <div class="text-xs text-green-600">{{ $presentPercentage }}%</div>
                    </div>
                    
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['late_logs'] }}</div>
                        <div class="text-sm text-gray-600">Terlambat</div>
                        <div class="text-xs text-yellow-600">{{ $latePercentage }}%</div>
                    </div>
                    
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['absent_logs'] }}</div>
                        <div class="text-sm text-gray-600">Tidak Hadir</div>
                        <div class="text-xs text-red-600">{{ $absentPercentage }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participant Progress -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Progress per Peserta</h2>
            
            <div class="space-y-4">
                @foreach($habit->approvedParticipants as $participant)
                    @php
                        $userLogs = $habit->logs->where('user_id', $participant->user_id);
                        $userTotal = $userLogs->count();
                        $userPresent = $userLogs->where('status', 'present')->count();
                        $userLate = $userLogs->where('status', 'late')->count();
                        $userAbsent = $userLogs->where('status', 'absent')->count();
                        $userPercentage = $userTotal > 0 ? round((($userPresent + $userLate * 0.5) / $userTotal) * 100) : 0;
                    @endphp
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">
                                        {{ $participant->user->initials() }}
                                    </span>
                                </div>
                                <span class="font-medium text-gray-900">{{ $participant->user->name }}</span>
                                @if($participant->user_id === $habit->user_id)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">Creator</span>
                                @endif
                            </div>
                            <span class="text-sm font-medium text-gray-600">{{ $userPercentage }}%</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $userPercentage }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>✅ {{ $userPresent }}</span>
                            <span>⏰ {{ $userLate }}</span>
                            <span>❌ {{ $userAbsent }}</span>
                            <span>📊 {{ $userTotal }} total</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Aktivitas Terbaru</h2>
        
        @if($habit->logs->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500">Belum ada aktivitas yang dicatat</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($habit->logs->sortByDesc('log_date')->take(20) as $log)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">
                                    {{ $log->user->initials() }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $log->log_date->format('d M Y') }} - {{ $log->log_time->format('H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            @if($log->status === 'present')
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                                    ✅ Hadir
                                </span>
                            @elseif($log->status === 'late')
                                <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                                    ⏰ Terlambat
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                                    ❌ Tidak Hadir
                                </span>
                            @endif
                            
                            @if($log->notes)
                                <div class="relative group">
                                    <div class="text-gray-400 cursor-help">💬</div>
                                    <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block bg-gray-800 text-white text-sm rounded-lg p-2 whitespace-nowrap max-w-xs">
                                        {{ $log->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection