@extends('layouts.base')

@section('title', 'Daily Habit Tracker - Satu Tapak')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daily Habit Tracker</h1>
            <p class="text-gray-600 mt-2">Kelola dan pantau habit harian Anda bersama komunitas</p>
        </div>
        <a href="{{ route('satu-tapak.habits.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            Buat Habit Baru
        </a>
    </div>

    <!-- Navigation tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <a href="#my-habits" 
               class="habit-tab py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm">
                Habit Saya
            </a>
            <a href="#participating-habits" 
               class="habit-tab py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Ikut Serta
            </a>
            <a href="{{ route('satu-tapak.invitations.index') }}" 
               class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Undangan ({{ Auth::user()->receivedHabitInvitations()->where('status', 'pending')->count() }})
            </a>
        </nav>
    </div>

    <!-- My Habits Section -->
    <div id="my-habits" class="habit-content">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Habit yang Saya Buat</h2>
            
            @if($myHabits->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-gray-400 text-6xl mb-4">🎯</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Habit</h3>
                    <p class="text-gray-500 mb-4">Mulai perjalanan kebiasaan baik Anda dengan membuat habit pertama</p>
                    <a href="{{ route('satu-tapak.habits.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Buat Habit Sekarang
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myHabits as $habit)
                        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $habit->name }}</h3>
                                @if($habit->is_locked)
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                        ✏️ Dapat Diubah
                                    </span>
                                @endif
                            </div>
                            
                            @if($habit->description)
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Durasi:</span>
                                    <span class="font-medium">{{ $habit->duration_days }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Mulai:</span>
                                    <span class="font-medium">{{ $habit->start_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Sisa:</span>
                                    <span class="font-medium">{{ $habit->remainingDays() }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Peserta:</span>
                                    <span class="font-medium">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 mb-2">Jadwal:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($habit->schedules as $schedule)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                    Detail
                                </a>
                                @if(!$habit->is_locked)
                                    <a href="{{ route('satu-tapak.habits.invite', $habit) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                        Undang
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Participating Habits Section -->
    <div id="participating-habits" class="habit-content hidden">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Habit yang Saya Ikuti</h2>
            
            @if($participatingHabits->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-gray-400 text-6xl mb-4">👥</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ikut Habit</h3>
                    <p class="text-gray-500">Anda belum mengikuti habit apapun. Tunggu undangan dari teman atau buat habit sendiri.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($participatingHabits as $habit)
                        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $habit->name }}</h3>
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">
                                    👤 Peserta
                                </span>
                            </div>
                            
                            @if($habit->description)
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Creator:</span>
                                    <span class="font-medium">{{ $habit->creator->name }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Sisa:</span>
                                    <span class="font-medium">{{ $habit->remainingDays() }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Peserta:</span>
                                    <span class="font-medium">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 mb-2">Jadwal:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($habit->schedules as $schedule)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                Detail & Log Aktivitas
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabs = document.querySelectorAll('.habit-tab');
    const contents = document.querySelectorAll('.habit-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            // Hide all content
            contents.forEach(content => content.classList.add('hidden'));
            
            // Show target content
            const target = this.getAttribute('href').substring(1);
            document.getElementById(target).classList.remove('hidden');
        });
    });
});
</script>
@endsection