<x-layouts.app :title="__('Pengguna') . ' - ' . $user->name" :breadcrumbs="['Users' => route('users'), $user->name]">

    {{-- Kartu Profil Utama (Neumorphism/Glassmorphism Ringan) --}}
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-2xl rounded-3xl overflow-hidden transition-all duration-300 transform hover:shadow-indigo-500/20 border border-white/50 dark:border-gray-700/50">
        
        <div class="p-8 md:p-10 relative">
            {{-- Decorative Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-blue-500/5 opacity-80 rounded-3xl -m-0.5"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center">
                
                {{-- Avatar dengan Border Animasi --}}
                <div class="flex-shrink-0 mb-6 md:mb-0 md:mr-8 relative">
                    <img class="w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover border-4 border-indigo-500 shadow-xl transition-transform duration-300 transform hover:scale-105"
                        src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                        alt="{{ $user->name }}">
                    {{-- Status Badge --}}
                    <span class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 ring-4 ring-white dark:ring-gray-800 rounded-full animate-pulse"></span>
                </div>
                
                {{-- Detail Utama --}}
                <div>
                    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        {{ $user->name }}
                    </h2>
                    <p class="text-lg text-indigo-600 dark:text-indigo-400 font-medium mb-4">
                        <i class="fas fa-at mr-2"></i> {{ $user->email ?? 'Email Tidak Tersedia' }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 italic max-w-xl mb-4">
                        "{{ $user->about ?? 'Deskripsi diri belum diisi.' }}"
                    </p>
                    
                    {{-- Badge Bergabung Sejak --}}
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bergabung Sejak: 
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $user->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Divider dan Statistik Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        {{-- Total Meet Diikuti --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/50 flex items-center justify-between transition-shadow duration-300 hover:shadow-indigo-400/30">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Meet</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->meets->count() }}</p>
            </div>
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full text-indigo-600 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>
        
        {{-- Meet yang Sudah Selesai (Contoh Data Tambahan) --}}
        @php
            $finished_meets = $user->meets->where('is_finished', true)->count();
            $ongoing_meets = $user->meets->where('is_finished', false)->count();
        @endphp
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/50 flex items-center justify-between transition-shadow duration-300 hover:shadow-green-400/30">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Meet Selesai</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $finished_meets }}</p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full text-green-600 dark:text-green-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        {{-- Meet yang Sedang Berjalan --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/50 flex items-center justify-between transition-shadow duration-300 hover:shadow-yellow-400/30">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Meet Aktif</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $ongoing_meets }}</p>
            </div>
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-full text-yellow-600 dark:text-yellow-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        
        {{-- Slot Kosong/Placeholder --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/50 flex items-center justify-center transition-shadow duration-300 hover:shadow-gray-400/30">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statistik Tambahan...</p>
        </div>

    </div>
    
    {{-- Daftar Meet yang Telah Diikuti (Tampilan Card) --}}
    <div class="mt-8">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Riwayat Partisipasi Meet
        </h3>
        
        @if($user->meets && $user->meets->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($user->meets as $meet)
                    @php
                        $is_finished = $meet->is_finished;
                        $status_class = $is_finished 
                            ? 'bg-green-500/10 text-green-600 border-green-500/50' 
                            : 'bg-yellow-500/10 text-yellow-600 border-yellow-500/50';
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-all duration-300 transform hover:shadow-lg hover:translate-y-[-2px]">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('meets.show', $meet) }}" class="text-xl font-semibold text-gray-900 dark:text-white hover:text-indigo-600 transition-colors duration-200">
                                    {{ $meet->title }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3-.895-3-2 1.343-2 3-2zM12 18a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                    {{ $meet->location ?? 'Lokasi Tidak Diketahui' }}
                                </p>
                            </div>
                            
                            {{-- Status Badge Dinamis --}}
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $status_class }}">
                                @if($is_finished)
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Selesai
                                @else
                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V7z" clip-rule="evenodd"></path></svg>
                                    Aktif
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl text-center border border-dashed border-gray-300 dark:border-gray-700">
                <svg class="w-10 h-10 text-indigo-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19v-2a3 3 0 013-3h12a3 3 0 013 3v2M4 7h16M4 7V5a2 2 0 012-2h12a2 2 0 012 2v2M8 11h8M8 15h2"></path></svg>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Pengguna ini belum pernah berpartisipasi dalam meet apapun.</p>
            </div>
        @endif
    </div>
</x-layouts.app>