<x-layouts.app :title="__('Dashboard')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-gray-900 text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    👋 Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p class="text-gray-400">
                    Email: {{ auth()->user()->email }}
                </p>
                <p class="text-gray-400">
                    Bergabung sejak: {{ auth()->user()->created_at->format('d M Y') }}
                </p>
            </div>

            <!-- Statistik -->
            <h2 class="text-2xl font-bold mb-6">📈 Statistik</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">3</h2>
                    <p class="mt-2 text-gray-400">📚 Kursus Sedang Berjalan</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">5</h2>
                    <p class="mt-2 text-gray-400">✅ Kursus Selesai</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">Sekian</h2>
                    <p class="mt-2 text-gray-400">📦 Jumlah Modul Diselesaikan</p>
                </div>
            </div>

            <!-- Kursus yang Sedang Diikuti -->
            <h2 class="text-2xl font-bold mb-6">🚀 Kursus yang Sedang Kamu Ikuti (Maksimal 3 Secara Bersamaan)</h2>
            <div class="bg-gray-700 p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside text-gray-400">
                    <li class="mb-2">Bila kamu ingin menambah kursus, silakan selesaikan salah satu kursus yang sedang diikuti.</li>
                    <li>Bila merasa salah memilih kursus, kamu dapat menghapus progres saat ini dan memilih kursus lain.</li>
                </ul>
            </div>
            <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
                <ul class="space-y-4">
                    <li class="flex justify-between items-center">
                        <span class="text-gray-300">Belajar Laravel</span>
                        <span class="text-sm text-gray-400">Progres: 50%</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-gray-300">Dasar-dasar JavaScript</span>
                        <span class="text-sm text-gray-400">Progres: 30%</span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-gray-300">UI/UX Design</span>
                        <span class="text-sm text-gray-400">Progres: 70%</span>
                    </li>
                </ul>
            </div>

            <!-- Motivasi -->
            <div class="mt-12 bg-indigo-500 p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-2xl font-bold text-white">💡 Tetap Semangat!</h2>
                <p class="mt-2 text-gray-200">"Belajar adalah investasi terbaik yang bisa kamu lakukan untuk masa depanmu."</p>
            </div>
        </div>
    </div>
</x-layouts.app>
