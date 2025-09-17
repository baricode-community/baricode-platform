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
                        <p><strong>Nama:</strong> John Doe</p>
                        <p><strong>Email:</strong> john.doe@example.com</p>
                        <p><strong>Nomor Telepon:</strong> +62 812-3456-7890</p>
                        <p><strong>Alamat:</strong> Jl. Contoh No. 123, Jakarta</p>
                    </div>

                    <!-- Informasi Akun -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">Informasi Akun</h2>
                        <p><strong>Username:</strong> johndoe</p>
                        <p><strong>Tanggal Bergabung:</strong> 1 Januari 2023</p>
                        <p><strong>Status Akun:</strong> Aktif</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4">Tentang Saya</h2>
                    <p class="text-gray-400 dark:text-gray-400">
                        Halo, saya John Doe. Saya seorang pengembang web dengan pengalaman lebih dari 5 tahun. Saya suka belajar teknologi baru dan berkontribusi pada proyek open-source.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>