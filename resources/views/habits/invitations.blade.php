@extends('layouts.base')

@section('title', 'Undangan Habit - Daily Habit Tracker')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Undangan Habit</h1>
                <p class="text-gray-600 mt-1">Undangan yang Anda terima dari teman-teman</p>
            </div>
        </div>
    </div>

    @if($invitations->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
            <div class="text-gray-400 text-6xl mb-4">✉️</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Undangan</h3>
            <p class="text-gray-500 mb-6">Anda belum memiliki undangan habit yang pending.</p>
            <a href="{{ route('satu-tapak.habits.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($invitations as $invitation)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Invitation Info -->
                        <div class="lg:col-span-2">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">
                                        {{ $invitation->inviter->initials() }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="text-xl font-semibold text-gray-900">{{ $invitation->habit->name }}</h3>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                            ID: {{ $invitation->habit->id }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">
                                        <strong>{{ $invitation->inviter->name }}</strong> mengundang Anda untuk bergabung dalam habit ini
                                    </p>

                                    @if($invitation->habit->description)
                                        <div class="mb-4">
                                            <label class="text-sm font-medium text-gray-500">Deskripsi Habit:</label>
                                            <p class="text-gray-700">{{ $invitation->habit->description }}</p>
                                        </div>
                                    @endif

                                    @if($invitation->message)
                                        <div class="mb-4 bg-gray-50 p-4 rounded-lg">
                                            <label class="text-sm font-medium text-gray-500">Pesan dari {{ $invitation->inviter->name }}:</label>
                                            <p class="text-gray-700 mt-1">{{ $invitation->message }}</p>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <label class="font-medium text-gray-500">Durasi</label>
                                            <p class="text-gray-900">{{ $invitation->habit->duration_days }} hari</p>
                                        </div>
                                        <div>
                                            <label class="font-medium text-gray-500">Mulai</label>
                                            <p class="text-gray-900">{{ $invitation->habit->start_date->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <label class="font-medium text-gray-500">Selesai</label>
                                            <p class="text-gray-900">{{ $invitation->habit->end_date->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <label class="font-medium text-gray-500">Peserta</label>
                                            <p class="text-gray-900">{{ $invitation->habit->approvedParticipants->count() }} orang</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="text-sm font-medium text-gray-500 mb-3 block">Jadwal Habit:</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($invitation->habit->schedules as $schedule)
                                        <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-lg">
                                            {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-4">
                            <div class="text-sm text-gray-500">
                                <p>Diterima: {{ $invitation->created_at->diffForHumans() }}</p>
                                <p>Berakhir: {{ $invitation->expires_at->diffForHumans() }}</p>
                            </div>

                            @if($invitation->isPending())
                                <div class="space-y-3">
                                    <form action="{{ route('satu-tapak.invitations.respond', $invitation) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="response" value="accept">
                                        <button type="submit" 
                                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition duration-200">
                                            ✅ Terima Undangan
                                        </button>
                                    </form>

                                    <form action="{{ route('satu-tapak.invitations.respond', $invitation) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="response" value="reject">
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin menolak undangan ini?')"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition duration-200">
                                            ❌ Tolak Undangan
                                        </button>
                                    </form>

                                    <a href="{{ route('satu-tapak.habits.show', $invitation->habit) }}" 
                                       class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium transition duration-200">
                                        👀 Lihat Detail Habit
                                    </a>
                                </div>
                            @else
                                <div class="text-center">
                                    @if($invitation->status === 'accepted')
                                        <span class="bg-green-100 text-green-800 text-sm font-medium px-4 py-2 rounded-full">
                                            ✅ Sudah Diterima
                                        </span>
                                        <p class="text-xs text-gray-500 mt-2">{{ $invitation->responded_at->diffForHumans() }}</p>
                                    @elseif($invitation->status === 'rejected')
                                        <span class="bg-red-100 text-red-800 text-sm font-medium px-4 py-2 rounded-full">
                                            ❌ Ditolak
                                        </span>
                                        <p class="text-xs text-gray-500 mt-2">{{ $invitation->responded_at->diffForHumans() }}</p>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-4 py-2 rounded-full">
                                            ⏰ Kedaluwarsa
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection