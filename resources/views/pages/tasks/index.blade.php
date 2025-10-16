<x-layouts.app :title="__('Tugas Menyenangkan')">
    <div class="">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold mb-2 dark:text-slate-200">
                        Daftar Tugas Menyenangkan
                    </h1>
                    <p class="text-gray-600 dark:text-slate-400">
                        Semua tugas yang tersedia untuk Anda selesaikan. Untuk mendapatkan tugas baru, silakan berkomunikasi dengan kita dan periksa kembali secara berkala.
                    </p>
                    <div class="mt-6">
                        <a href="https://chat.whatsapp.com/Fb2ZFMIKDz7JJZyBVpzXws" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-semibold rounded-lg shadow transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"></svg>
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.198.297-.767.967-.94 1.166-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.447-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.205-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.075-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.099 3.205 5.077 4.372.711.306 1.263.489 1.695.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.288.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            </svg>
                            Minta tugas baru di komunitas WhatsApp
                        </a>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Klik tombol di atas untuk bergabung dan meminta tugas yang bisa Anda kerjakan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Daftar Tugas -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Judul Tugas
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Dibuat Pada
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @if($tasks->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                                        Tidak ada tugas tersedia saat ini.
                                    </td>
                                </tr>
                            @else
                                @foreach($tasks as $task)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $task->title }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ Str::limit($task->description, 30) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $task->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
