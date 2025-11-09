@extends('components.layouts.app')

@section('title', 'Daily Habit Tracker - Satu Tapak')

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-8">
        <div>
            {{-- Mengubah teks header --}}
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Daily Habit Tracker</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Kelola dan pantau habit harian Anda bersama komunitas</p>
        </div>
        <a href="{{ route('satu-tapak.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            Buat Habit Baru
        </a>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8">
            {{-- Mengubah tab untuk dark mode --}}
            <a href="#my-habits" 
               class="habit-tab py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm dark:text-blue-400 dark:border-blue-400">
                Habit Saya
            </a>
            <a href="#participating-habits" 
               class="habit-tab py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-200">
                Ikut Serta
            </a>
            <a href="{{ route('satu-tapak.invitations.index') }}" 
               class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-200">
                Undangan ({{ Auth::user()->receivedHabitInvitations()->where('status', 'pending')->count() }})
            </a>
        </nav>
    </div>

    <div id="my-habits" class="habit-content">
        <div class="mb-6">
            {{-- Mengubah sub-header --}}
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Habit yang Saya Buat</h2>
            
            @if($myHabits->isEmpty())
                {{-- Mengubah background div kosong --}}
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-400 text-6xl mb-4">🎯</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum Ada Habit</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai perjalanan kebiasaan baik Anda dengan membuat habit pertama</p>
                    <a href="{{ route('satu-tapak.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Buat Habit Sekarang
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myHabits as $habit)
                        {{-- Mengubah background card habit --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl dark:hover:shadow-gray-700/50 transition duration-200">
                            <div class="flex justify-between items-start mb-4">
                                {{-- Mengubah judul habit --}}
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $habit->name }}</h3>
                                @if($habit->is_locked)
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-red-900/50 dark:text-red-300">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-green-900/50 dark:text-green-300">
                                        ✏️ Dapat Diubah
                                    </span>
                                @endif
                            </div>
                            
                            @if($habit->description)
                                {{-- Mengubah deskripsi --}}
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                {{-- Mengubah detail habit --}}
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Durasi:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Mulai:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Sisa:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->remainingDays() }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Peserta:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Jadwal:</p>
                                <div class="flex flex-wrap gap-1">
                                    {{-- Mengubah badge jadwal --}}
                                    @foreach($habit->schedules as $schedule)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300">
                                            {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('satu-tapak.show', $habit) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                    Detail
                                </a>
                                @if(!$habit->is_locked)
                                    <a href="{{ route('satu-tapak.invite', $habit) }}" 
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

    <div id="participating-habits" class="habit-content hidden">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Habit yang Saya Ikuti</h2>
            
            @if($participatingHabits->isEmpty())
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-gray-400 text-6xl mb-4">👥</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum Ikut Habit</h3>
                    <p class="text-gray-500 dark:text-gray-400">Anda belum mengikuti habit apapun. Tunggu undangan dari teman atau buat habit sendiri.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($participatingHabits as $habit)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl dark:hover:shadow-gray-700/50 transition duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $habit->name }}</h3>
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-purple-900/50 dark:text-purple-300">
                                    👤 Peserta
                                </span>
                            </div>
                            
                            @if($habit->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                            @endif

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Creator:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->creator->name }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Sisa:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->remainingDays() }} hari</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Peserta:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Jadwal:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($habit->schedules as $schedule)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300">
                                            {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <a href="{{ route('satu-tapak.show', $habit) }}" 
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
    // Tab functionality (Tidak diubah, karena tidak ada perubahan tampilan yang memerlukan class dark: tertentu)
    const tabs = document.querySelectorAll('.habit-tab');
    const contents = document.querySelectorAll('.habit-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Logika untuk mengubah kelas aktif/tidak aktif pada tab
            tabs.forEach(t => {
                // Hapus kelas aktif Light Mode
                t.classList.remove('border-blue-500', 'text-blue-600');
                // Tambahkan kelas tidak aktif Light Mode
                t.classList.add('border-transparent', 'text-gray-500');
                // Hapus kelas aktif Dark Mode
                t.classList.remove('dark:text-blue-400', 'dark:border-blue-400');
                // Tambahkan kelas tidak aktif Dark Mode
                t.classList.add('dark:text-gray-400', 'dark:hover:text-gray-200');
            });
            
            // Tambahkan kelas aktif ke tab yang diklik
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            this.classList.remove('dark:text-gray-400', 'dark:hover:text-gray-200');
            this.classList.add('dark:text-blue-400', 'dark:border-blue-400');
            
            // Hide all content
            contents.forEach(content => content.classList.add('hidden'));
            
            // Show target content
            const target = this.getAttribute('href').substring(1);
            document.getElementById(target).classList.remove('hidden');
        });
    });

    // Tambahkan logika untuk mengatur tab default saat DOMContentLoaded
    // Pastikan tab pertama ("Habit Saya") diatur sebagai aktif
    const myHabitsTab = document.querySelector('a[href="#my-habits"]');
    if (myHabitsTab) {
        myHabitsTab.classList.remove('border-transparent', 'text-gray-500');
        myHabitsTab.classList.add('border-blue-500', 'text-blue-600');
        
        myHabitsTab.classList.remove('dark:text-gray-400', 'dark:hover:text-gray-200');
        myHabitsTab.classList.add('dark:text-blue-400', 'dark:border-blue-400');
    }
});
</script>
@endsection