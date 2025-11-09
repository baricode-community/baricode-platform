@extends('components.layouts.app')

@section('title', $habit->name . ' - Daily Habit Tracker')

@section('content')
<div class="">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            {{-- Kembali --}}
            <a href="{{ route('satu-tapak.index') }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                ← Kembali
            </a>
            <div class="flex space-x-3">
                {{-- Tombol Aksi --}}
                @if($habit->user_id === Auth::id() && !$habit->is_locked)
                    <a href="{{ route('satu-tapak.invite', $habit) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Undang Teman
                    </a>
                    <form action="{{ route('satu-tapak.lock', $habit) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin mengunci habit ini? Setelah dikunci, habit tidak dapat diubah lagi.')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            🔒 Kunci Habit
                        </button>
                    </form>
                @endif
                <a href="{{ route('satu-tapak.statistics', $habit) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    📊 Statistik
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- Background Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Habit</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            {{-- Label dan Teks --}}
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Creator</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->creator->name }}</p>
                        </div>
                        
                        @if($habit->description)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->description }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <div class="flex items-center space-x-2">
                                {{-- Badge Terkunci/Dapat Diubah --}}
                                @if($habit->is_locked)
                                    <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-red-900/50 dark:text-red-300">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-green-900/50 dark:text-green-300">
                                        ✏️ Dapat Diubah
                                    </span>
                                @endif
                                
                                {{-- Badge Aktif/Tidak Aktif --}}
                                @if($habit->isActive())
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-blue-900/50 dark:text-blue-300">
                                        🟢 Aktif
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                        ⏸️ Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }} - {{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Sisa Hari</label>
                            <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $habit->remainingDays() }} hari</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Jadwal</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Jadwal Card --}}
                    @foreach($habit->schedules as $schedule)
                        <div class="bg-blue-50 dark:bg-blue-900/50 p-4 rounded-lg">
                            <div class="text-lg font-semibold text-blue-900 dark:text-blue-300">{{ $schedule->day_name }}</div>
                            <div class="text-blue-700 dark:text-blue-400">{{ $schedule->formatted_time }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($userIsParticipant)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Log Hari Ini</h2>
                    
                    @if($todayLog)
                        {{-- Logged Card --}}
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 dark:bg-green-900/50 dark:border-green-700">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-green-600 dark:text-green-300 font-semibold">✅ Sudah Log</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $todayLog->logged_at->format('H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Status:</strong> {{ $todayLog->formatted_status }}</p>
                            @if($todayLog->notes)
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1"><strong>Catatan:</strong> {{ $todayLog->notes }}</p>
                            @endif
                        </div>
                    @else
                        {{-- Log Form --}}
                        <form action="{{ route('satu-tapak.log', $habit) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Kehadiran</label>
                                <div class="space-y-2">
                                    {{-- Radio buttons tidak banyak berubah, tapi labelnya disesuaikan --}}
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="present" class="text-blue-600 dark:bg-gray-600 dark:border-gray-500 mr-3" required>
                                        <span class="text-green-600 dark:text-green-400 font-medium">✅ Hadir</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="late" class="text-blue-600 dark:bg-gray-600 dark:border-gray-500 mr-3" required>
                                        <span class="text-yellow-600 dark:text-yellow-400 font-medium">⏰ Terlambat</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="absent" class="text-blue-600 dark:bg-gray-600 dark:border-gray-500 mr-3" required>
                                        <span class="text-red-600 dark:text-red-400 font-medium">❌ Tidak Hadir</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                                {{-- Textarea --}}
                                <textarea name="notes" 
                                          id="notes" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                          placeholder="Tambahkan catatan tentang aktivitas hari ini..."></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-200">
                                Simpan Log
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Log Aktivitas Terbaru</h2>
                
                @if($habit->logs->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada log aktivitas</p>
                @else
                    <div class="space-y-3">
                        @foreach($habit->logs->sortByDesc('log_date')->take(10) as $log)
                            {{-- Recent Log Item --}}
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $log->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $log->log_date->format('d M Y') }}</div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($log->status === 'present')
                                        <span class="text-green-600 dark:text-green-400 font-medium">✅ Hadir</span>
                                    @elseif($log->status === 'late')
                                        <span class="text-yellow-600 dark:text-yellow-400 font-medium">⏰ Terlambat</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400 font-medium">❌ Tidak Hadir</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Peserta ({{ $habit->approvedParticipants->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($habit->approvedParticipants as $participant)
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                    {{ $participant->user->initials() }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $participant->user->name }}</div>
                                @if($participant->user_id === $habit->user_id)
                                    <div class="text-xs text-blue-600 dark:text-blue-400">Creator</div>
                                @else
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Bergabung {{ $participant->joined_at->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Progress</h2>
                
                @php
                    $totalLogs = $habit->logs->count();
                    $presentLogs = $habit->logs->where('status', 'present')->count();
                    $lateLogs = $habit->logs->where('status', 'late')->count();
                    $absentLogs = $habit->logs->where('status', 'absent')->count();
                    $presentPercentage = $totalLogs > 0 ? round(($presentLogs / $totalLogs) * 100) : 0;
                @endphp
                
                <div class="space-y-4">
                    <div>
                        {{-- Progress Bar --}}
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Kehadiran</span>
                            <span class="font-medium dark:text-gray-100">{{ $presentPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $presentPercentage }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3 text-center">
                        {{-- Summary Statistik --}}
                        <div class="bg-green-50 p-2 rounded dark:bg-green-900/50">
                            <div class="text-lg font-bold text-green-600 dark:text-green-300">{{ $presentLogs }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Hadir</div>
                        </div>
                        <div class="bg-yellow-50 p-2 rounded dark:bg-yellow-900/50">
                            <div class="text-lg font-bold text-yellow-600 dark:text-yellow-300">{{ $lateLogs }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Terlambat</div>
                        </div>
                        <div class="bg-red-50 p-2 rounded dark:bg-red-900/50">
                            <div class="text-lg font-bold text-red-600 dark:text-red-300">{{ $absentLogs }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Tidak Hadir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection