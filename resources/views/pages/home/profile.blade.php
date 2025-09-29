@extends('layouts.base')

@section('title', 'Profil Saya')

@section('content')
<section class="py-20 md:py-32 px-4 bg-gray-900 text-white">
    <div class="max-w-3xl mx-auto bg-gray-800/70 rounded-2xl shadow-xl p-8">
        {{-- Profile Header --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-10">
            <div>
                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="Foto Profil" class="w-32 h-32 rounded-full border-4 border-indigo-500 shadow-lg object-cover">
            </div>
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ Auth::user()->name }}</h1>
                <p class="text-lg text-indigo-300 mb-1">{{ Auth::user()->email }}</p>
                @if(Auth::user()->bio)
                    <p class="text-gray-300 mb-2">{{ Auth::user()->bio }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-block bg-indigo-600/80 px-3 py-1 rounded-full text-xs font-semibold">Bergabung sejak {{ Auth::user()->created_at->format('M Y') }}</span>
                    @if(Auth::user()->role)
                        <span class="inline-block bg-purple-600/80 px-3 py-1 rounded-full text-xs font-semibold">{{ ucfirst(Auth::user()->role) }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Details --}}
        <div class="mb-10">
            <h2 class="text-xl font-semibold mb-4 text-indigo-400">Detail Profil</h2>
            <div class="grid md:grid-cols-2 gap-6 text-gray-200">
                <div>
                    <div class="mb-2"><span class="font-semibold">Nama Lengkap:</span> {{ Auth::user()->name }}</div>
                    <div class="mb-2"><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</div>
                    @if(Auth::user()->phone)
                        <div class="mb-2"><span class="font-semibold">No. HP:</span> {{ Auth::user()->phone }}</div>
                    @endif
                    @if(Auth::user()->city)
                        <div class="mb-2"><span class="font-semibold">Kota:</span> {{ Auth::user()->city }}</div>
                    @endif
                </div>
                <div>
                    @if(Auth::user()->institution)
                        <div class="mb-2"><span class="font-semibold">Institusi:</span> {{ Auth::user()->institution }}</div>
                    @endif
                    @if(Auth::user()->occupation)
                        <div class="mb-2"><span class="font-semibold">Pekerjaan:</span> {{ Auth::user()->occupation }}</div>
                    @endif
                    @if(Auth::user()->website)
                        <div class="mb-2"><span class="font-semibold">Website:</span> <a href="{{ Auth::user()->website }}" class="text-indigo-300 underline" target="_blank">{{ Auth::user()->website }}</a></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Meetings Joined --}}
        <div>
            <h2 class="text-xl font-semibold mb-4 text-indigo-400">Meet yang Diikuti</h2>
            @php
                $meets = $user->meets; // Assuming a 'meets' relationship exists on the User model
            @endphp
            @if(isset($meets) && count($meets))
                <div class="space-y-6">
                    @foreach($meets as $meet)
                        <div class="bg-gray-700/60 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <div class="text-lg font-bold text-indigo-200">{{ $meet->title }}</div>
                                <div class="text-sm text-gray-300 mb-1">{{ $meet->description }}</div>
                                <div class="text-xs text-gray-400">
                                    Tanggal: {{ \Carbon\Carbon::parse($meet->date)->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('meets.show', $meet->id) }}" class="inline-block px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">Lihat Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-400 italic">Belum ada meet yang diikuti.</div>
            @endif
        </div>
    </div>
</section>
@endsection
