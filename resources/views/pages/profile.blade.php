<x-layouts.app :title="__('Profile')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
                Profil Pengguna
            </h1>
            <p class="text-lg md:text-xl text-gray-400 dark:text-gray-400 mb-8">
                Berikut adalah informasi lengkap tentang profilmu.
            </p>
            
            <div class="bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white p-8 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Pribadi -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">Informasi Pribadi</h2>
                        <p><strong>Nama:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Nomor Telepon:</strong> {{ $user->whatsapp }}</p>
                    </div>

                    <!-- Informasi Akun -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">Informasi Akun</h2>
                        <p><strong>Tanggal Bergabung:</strong> {{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4">Tentang Saya</h2>
                    <p class="text-gray-400 dark:text-gray-400">
                        {{ $user->about }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>