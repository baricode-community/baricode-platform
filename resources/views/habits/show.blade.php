@extends('components.layouts.app')

@section('title', $habit->name . ' - Daily Habit Tracker')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900">{{ $habit->name }}</h1>
                <p class="text-gray-600 mt-1">ID: {{ $habit->id }}</p>
            </div>
            <div class="flex space-x-3">
                @if($habit->user_id === Auth::id() && !$habit->is_locked)
                    <a href="{{ route('satu-tapak.habits.invite', $habit) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Undang Teman
                    </a>
                    <form action="{{ route('satu-tapak.habits.lock', $habit) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin mengunci habit ini? Setelah dikunci, habit tidak dapat diubah lagi.')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            🔒 Kunci Habit
                        </button>
                    </form>
                @endif
                <a href="{{ route('satu-tapak.habits.statistics', $habit) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    📊 Statistik
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Habit Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Habit</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Creator</label>
                            <p class="text-gray-900">{{ $habit->creator->name }}</p>
                        </div>
                        
                        @if($habit->description)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                            <p class="text-gray-900">{{ $habit->description }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="flex items-center space-x-2">
                                @if($habit->is_locked)
                                    <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                                        ✏️ Dapat Diubah
                                    </span>
                                @endif
                                
                                @if($habit->isActive())
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                        🟢 Aktif
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                                        ⏸️ Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Durasi</label>
                            <p class="text-gray-900">{{ $habit->duration_days }} hari</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Periode</label>
                            <p class="text-gray-900">{{ $habit->start_date->format('d M Y') }} - {{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Sisa Hari</label>
                            <p class="text-gray-900 font-semibold">{{ $habit->remainingDays() }} hari</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($habit->schedules as $schedule)
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-lg font-semibold text-blue-900">{{ $schedule->day_name }}</div>
                            <div class="text-blue-700">{{ $schedule->formatted_time }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Today's Log (if participant) -->
            @if($userIsParticipant)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Log Hari Ini</h2>
                    
                    @if($todayLog)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-green-600 font-semibold">✅ Sudah Log</span>
                                <span class="text-sm text-gray-500">{{ $todayLog->logged_at->format('H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700"><strong>Status:</strong> {{ $todayLog->formatted_status }}</p>
                            @if($todayLog->notes)
                                <p class="text-sm text-gray-700 mt-1"><strong>Catatan:</strong> {{ $todayLog->notes }}</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('satu-tapak.habits.log', $habit) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Kehadiran</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="present" class="text-blue-600 mr-3" required>
                                        <span class="text-green-600 font-medium">✅ Hadir</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="late" class="text-blue-600 mr-3" required>
                                        <span class="text-yellow-600 font-medium">⏰ Terlambat</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="absent" class="text-blue-600 mr-3" required>
                                        <span class="text-red-600 font-medium">❌ Tidak Hadir</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                <textarea name="notes" 
                                          id="notes" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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

            <!-- Recent Logs -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Log Aktivitas Terbaru</h2>
                
                @if($habit->logs->isEmpty())
                    <p class="text-gray-500 text-center py-8">Belum ada log aktivitas</p>
                @else
                    <div class="space-y-3">
                        @foreach($habit->logs->sortByDesc('log_date')->take(10) as $log)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->log_date->format('d M Y') }}</div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($log->status === 'present')
                                        <span class="text-green-600 font-medium">✅ Hadir</span>
                                    @elseif($log->status === 'late')
                                        <span class="text-yellow-600 font-medium">⏰ Terlambat</span>
                                    @else
                                        <span class="text-red-600 font-medium">❌ Tidak Hadir</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Participants -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Peserta ({{ $habit->approvedParticipants->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($habit->approvedParticipants as $participant)
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">
                                    {{ $participant->user->initials() }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $participant->user->name }}</div>
                                @if($participant->user_id === $habit->user_id)
                                    <div class="text-xs text-blue-600">Creator</div>
                                @else
                                    <div class="text-xs text-gray-500">Bergabung {{ $participant->joined_at->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Progress Summary -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Progress</h2>
                
                @php
                    $totalLogs = $habit->logs->count();
                    $presentLogs = $habit->logs->where('status', 'present')->count();
                    $lateLogs = $habit->logs->where('status', 'late')->count();
                    $absentLogs = $habit->logs->where('status', 'absent')->count();
                    $presentPercentage = $totalLogs > 0 ? round(($presentLogs / $totalLogs) * 100) : 0;
                @endphp
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Kehadiran</span>
                            <span class="font-medium">{{ $presentPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $presentPercentage }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-green-50 p-2 rounded">
                            <div class="text-lg font-bold text-green-600">{{ $presentLogs }}</div>
                            <div class="text-xs text-gray-600">Hadir</div>
                        </div>
                        <div class="bg-yellow-50 p-2 rounded">
                            <div class="text-lg font-bold text-yellow-600">{{ $lateLogs }}</div>
                            <div class="text-xs text-gray-600">Terlambat</div>
                        </div>
                        <div class="bg-red-50 p-2 rounded">
                            <div class="text-lg font-bold text-red-600">{{ $absentLogs }}</div>
                            <div class="text-xs text-gray-600">Tidak Hadir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection