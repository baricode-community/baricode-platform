<x-layouts.app :title="__('Dashboard')">
    <div class="py-12 px-4 md:px-6 lg:px-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    👋 Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p>
                    Email: {{ auth()->user()->email }}
                </p>
                <p>
                    Bergabung sejak: {{ auth()->user()->created_at->format('d M Y') }}
                </p>
            </div>

            <!-- Statistik -->
            <h2 class="text-2xl font-bold mb-6">📈 Statistik</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="p-6 rounded-lg shadow-lg text-center border">
                    <h2 class="text-5xl font-extrabold text-indigo-600">3</h2>
                    <p class="mt-2">📚 Kursus Sedang Berjalan</p>
                </div>
                <div class="p-6 rounded-lg shadow-lg text-center border">
                    <h2 class="text-5xl font-extrabold text-indigo-600">5</h2>
                    <p class="mt-2">✅ Kursus Selesai</p>
                </div>
                <div class="p-6 rounded-lg shadow-lg text-center border">
                    <h2 class="text-5xl font-extrabold text-indigo-600">Sekian</h2>
                    <p class="mt-2">📦 Jumlah Modul Diselesaikan</p>
                </div>
            </div>

            <!-- Kursus yang Sedang Diikuti -->
            <h2 class="text-2xl font-bold mb-6">🚀 Kursus yang Sedang Kamu Ikuti (Maksimal 3 Secara Bersamaan)</h2>
            <div class="p-4 rounded-lg mb-6 border">
                <ul class="list-disc list-inside">
                    <li class="mb-2">Bila kamu ingin menambah kursus, silakan selesaikan salah satu kursus yang sedang diikuti.</li>
                    <li>Bila merasa salah memilih kursus, kamu dapat menghapus progres saat ini dan memilih kursus lain.</li>
                </ul>
            </div>
            <div class="p-8 rounded-lg shadow-lg border">
                @if($courseRecords->isNotEmpty())
                    <ul class="space-y-4">
                        @foreach ($courseRecords as $record)
                            <li class="flex justify-between items-center">
                                <span>{{ $record->course->title }}</span>
                                <span class="text-sm">Progres: {{ $record->progress }}%</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-gray-500">
                        Kamu belum mengikuti kursus apapun saat ini.
                    </div>
                @endif
                <div class="mt-8 text-center">
                    @if(auth()->user()->level === 'pemula')
                        <a href="{{ route('courses.pemula') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded transition">
                            Lihat Kursus Pemula
                        </a>
                    @elseif(auth()->user()->level === 'menengah')
                        <a href="{{ route('courses.menengah') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded transition">
                            Lihat Kursus Menengah
                        </a>
                    @elseif(auth()->user()->level === 'lanjut')
                        <a href="{{ route('courses.lanjut') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded transition">
                            Lihat Kursus Lanjutan
                        </a>
                    @else
                        <a href="{{ route('courses') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded transition">
                            Lihat Semua Kursus
                        </a>
                    @endif
                </div>
            </div>

            <!-- Motivasi -->
            <div class="mt-12 bg-indigo-500 p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-2xl font-bold text-white">💡 Tetap Semangat!</h2>
                <p class="mt-2 text-gray-200">"Belajar adalah investasi terbaik yang bisa kamu lakukan untuk masa depanmu."</p>
            </div>
        </div>
    </div>
</x-layouts.app>
