<x-layouts.app :title="__('Konfigurasi Kursus')" :breadcrumbs="['dashboard.courses.prepare', $course]">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-10 border border-indigo-100 dark:border-gray-700">
            <h1 class="text-3xl font-extrabold mb-8 text-center text-indigo-700 dark:text-indigo-300 tracking-tight">📅 Konfigurasi Jadwal Kursus</h1>
            <div class="mb-8 text-center text-gray-600 dark:text-gray-300">
                <p class="mb-2">📝 Silakan atur hari dan jam kursus sesuai kebutuhan Anda.<br>
                    ⏰ Pastikan jadwal tidak bertabrakan dengan aktivitas lain.<br>
                    📢 <span class="font-semibold">Catatan:</span> Anda dapat memilih lebih dari satu hari.</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 dark:bg-yellow-900 dark:border-yellow-400 dark:text-yellow-200 p-4 mb-4 rounded-lg shadow-sm"
                    role="alert">
                    <strong class="font-bold">⚠️ Peringatan:</strong> Setelah jadwal kursus ditentukan, Anda <span class="font-semibold">tidak
                        dapat mengubahnya</span> kecuali dengan menghapus progres kursus saat ini.
                </div>
                <p class="mt-4 text-sm text-indigo-700 dark:text-indigo-200">
                    ⏳ <span class="font-semibold">Catatan tambahan:</span> Jika Anda tidak menentukan jam pada hari yang dipilih, maka secara default sistem akan mengatur jadwal pada hari tersebut pukul <span class="font-bold">06:00 WIB</span>.
                </p>
                <div class="mt-4 text-sm text-green-700 dark:text-green-200">
                    📲 Pada setiap waktu yang Anda masukkan, Anda akan menerima pesan WhatsApp sebagai pengingat untuk belajar.<br>
                    📝 Setelah itu, Anda akan diminta melakukan absensi.<br>
                    ⏱️ Jika absensi dilakukan lebih dari <span class="font-bold">15 menit</span> dari waktu yang ditentukan, maka akan tercatat sebagai bolos.
                </div>
            </div>
            <form action="{{ route('course.start', $course) }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="block font-semibold mb-4 text-lg text-indigo-700 dark:text-indigo-200">🗓️ Pilih Hari & Jam Kursus</label>
                    @if ($errors->any())
                        <div class="mb-6">
                            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 dark:bg-red-900 dark:border-red-400 dark:text-red-200 p-4 rounded-lg shadow-sm" role="alert">
                                <strong class="font-bold">Terjadi kesalahan:</strong>
                                <ul class="mt-2 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-3">
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                            <div class="flex items-center justify-between bg-indigo-50 dark:bg-gray-700 rounded-lg px-4 py-3 shadow-sm border border-indigo-100 dark:border-gray-600 transition hover:bg-indigo-100 dark:hover:bg-gray-600 group">
                                <label class="flex items-center space-x-2 min-w-[100px] font-medium text-indigo-800 dark:text-indigo-100">
                                    <input type="checkbox" name="days[{{ $day }}][active]" value="1"
                                        class="form-checkbox text-indigo-600 day-checkbox accent-indigo-600 focus:ring-indigo-500 transition"
                                        data-day="{{ $day }}">
                                    <span class="flex items-center space-x-1">
                                        @switch($day)
                                            @case('Senin') <span>🌞</span> @break
                                            @case('Selasa') <span>🌤️</span> @break
                                            @case('Rabu') <span>☀️</span> @break
                                            @case('Kamis') <span>🌥️</span> @break
                                            @case('Jumat') <span>🌈</span> @break
                                            @case('Sabtu') <span>🎉</span> @break
                                            @case('Ahad') <span>🛌</span> @break
                                        @endswitch
                                        <span>{{ $day }}</span>
                                    </span>
                                </label>
                                <div class="flex space-x-2 items-center overflow-x-auto">
                                    <div class="flex flex-col items-center">
                                        <input type="time" name="days[{{ $day }}][sesi_1]"
                                            class="form-input rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 day-time-{{ $day }} px-3 py-2 w-28 text-indigo-700 dark:text-indigo-200 bg-white dark:bg-gray-800 shadow-inner transition focus:ring-2 focus:ring-indigo-400"
                                            disabled>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sesi 1</span>
                                    </div>
                                    <span class="text-gray-400 dark:text-gray-500">,</span>
                                    <div class="flex flex-col items-center">
                                        <input type="time" name="days[{{ $day }}][sesi_2]"
                                            class="form-input rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 day-time-{{ $day }} px-3 py-2 w-28 text-indigo-700 dark:text-indigo-200 bg-white dark:bg-gray-800 shadow-inner transition focus:ring-2 focus:ring-indigo-400"
                                            disabled>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sesi 2</span>
                                    </div>
                                    <span class="text-gray-400 dark:text-gray-500">,</span>
                                    <div class="flex flex-col items-center">
                                        <input type="time" name="days[{{ $day }}][sesi_3]"
                                            class="form-input rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 day-time-{{ $day }} px-3 py-2 w-28 text-indigo-700 dark:text-indigo-200 bg-white dark:bg-gray-800 shadow-inner transition focus:ring-2 focus:ring-indigo-400"
                                            disabled>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sesi 3</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition flex items-center space-x-2 shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                        </svg>
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </form>

            <script>
                document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const day = this.getAttribute('data-day');
                        document.querySelectorAll('.day-time-' + day).forEach(function(input) {
                            input.disabled = !checkbox.checked;
                            if (checkbox.checked) {
                                input.classList.add('ring-2', 'ring-indigo-300');
                            } else {
                                input.classList.remove('ring-2', 'ring-indigo-300');
                            }
                        });
                    });
                });
            </script>

            <div class="mt-10 text-sm text-gray-500 dark:text-gray-400 text-center">
                <p>❓ Jika Anda mengalami kendala, silakan hubungi admin atau cek <a href="#"
                        class="underline text-indigo-600 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-100 transition">Pusat Bantuan</a>.</p>
            </div>
        </div>
</x-layouts.app>
