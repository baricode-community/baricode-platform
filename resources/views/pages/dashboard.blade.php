<x-layouts.app :title="__('Dashboard')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-gray-900 text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-lg md:text-xl text-gray-400 mb-8">
                Pantau progres belajarmu di sini.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">0</h2>
                    <p class="mt-2 text-gray-400">Kursus Sedang Berjalan</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">0</h2>
                    <p class="mt-2 text-gray-400">Kursus Selesai</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-5xl font-extrabold text-indigo-400">0</h2>
                    <p class="mt-2 text-gray-400">Poin XP</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-6">Kursus yang Sedang Kamu Ikuti</h2>
            <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
                <p class="text-center text-gray-400">Konten kursus akan tampil di sini.</p>
                </div>
            
        </div>
    </div>
</x-layouts.app>