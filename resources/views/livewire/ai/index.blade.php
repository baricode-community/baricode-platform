<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    // Properti untuk mengontrol modal
    public bool $showAiModelModal = false;

    // Fungsi untuk membuka modal
    public function openAiModelModal(): void
    {
        $this->showAiModelModal = true;
    }

    // Fungsi untuk menutup modal
    public function closeAiModelModal(): void
    {
        $this->showAiModelModal = false;
    }
}; ?>

<div class="">
    <header
        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 mb-10 border-t-4 border-indigo-600 dark:border-indigo-500 transition duration-300">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-start gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=80"
                    alt="Avatar"
                    class="rounded-full shadow-lg border-4 border-indigo-200 dark:border-indigo-400 bg-white w-20 h-20 object-cover">
                <div>
                    <h1
                        class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                        <x-heroicon-o-light-bulb class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                        Dashboard Belajar Ngoding
                    </h1>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        <span class="block mb-1">Halo, {{ auth()->user()->name }}! Selamat belajar.</span>
                        <span class="flex items-center gap-1 text-xs"><x-heroicon-o-calendar
                                class="w-4 h-4 text-gray-500 dark:text-gray-400" /> Bergabung sejak:
                            {{ auth()->user()->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
            </div>
        </div>
    </header>

    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
        <x-heroicon-o-chart-bar class="w-6 h-6 text-indigo-600 dark:text-indigo-400" /> Pencapaian & Statistik
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div
            class="p-6 lg:p-8 rounded-xl shadow-xl border bg-white dark:bg-gray-800 dark:border-gray-700 transition transform hover:scale-[1.02] duration-300 ease-in-out border-b-4 border-green-500">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Mentor AI Tersedia</h3>
            </div>
            <p class="mt-2 text-4xl font-extrabold text-green-600 dark:text-green-400">x</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Model AI siap membantu.</p>
        </div>
    </div>


    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
        <x-heroicon-o-academic-cap class="w-6 h-6 text-indigo-600 dark:text-indigo-400" /> Menu Utama
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <a href="/dashboard"
            class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900/40 transition duration-300 group">
            <div class="flex items-center gap-4 mb-2">
                <x-heroicon-o-book-open
                    class="w-8 h-8 text-indigo-600 group-hover:text-indigo-700 dark:text-indigo-400 dark:group-hover:text-indigo-300" />
                <span class="font-bold text-xl dark:text-white">Kursus Pemrograman</span>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Akses ke semua materi dan kursus pemrograman gratis yang
                tersedia di platform.</p>
        </a>
        <button type="button" wire:click="openAiModelModal"
            class="block w-full text-left p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-2xl hover:bg-green-50 dark:hover:bg-green-900/40 transition duration-300 group relative">
            <div class="flex items-center gap-4 mb-2">
            <x-heroicon-o-cpu-chip
                class="w-8 h-8 text-green-600 group-hover:text-green-700 dark:text-green-400 dark:group-hover:text-green-300" />
            <span class="font-bold text-xl dark:text-white">Mentor Gratis Via AI</span>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Dapatkan bimbingan alur belajar secara instan dari model AI
            terkemuka.</p>
            <span class="absolute top-4 right-4 bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 text-xs font-semibold px-3 py-1 rounded-full shadow">Segera Hadir</span>
        </button>
        <a href=""
            class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:shadow-2xl hover:bg-purple-50 dark:hover:bg-purple-900/40 transition duration-300 group relative">
            <div class="flex items-center gap-4 mb-2">
            <x-heroicon-o-wrench-screwdriver
                class="w-8 h-8 text-purple-600 group-hover:text-purple-700 dark:text-purple-400 dark:group-hover:text-purple-300" />
            <span class="font-bold text-xl dark:text-white">Coding Tools</span>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Akses ke berbagai *tools* dan sumber daya pendukung untuk
            proses ngoding Anda.</p>
            <span class="absolute top-4 right-4 bg-purple-100 text-purple-700 dark:bg-purple-800 dark:text-purple-200 text-xs font-semibold px-3 py-1 rounded-full shadow">Segera Hadir</span>
        </a>
    </div>

    <div x-data="{ show: @entangle('showAiModelModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm"
        style="display: none;">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.away="$wire.closeAiModelModal()"
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 sm:p-8 max-w-lg w-full m-4 relative border-t-4 border-green-500">
            <button type="button" wire:click="closeAiModelModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>

            <div class="mb-6 pb-4 border-b dark:border-gray-700">
                <h3 class="text-2xl font-bold dark:text-white flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-7 h-7 text-green-500" /> Pilih Mentor AI
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Klik salah satu model AI untuk langsung memulai sesi belajar.
                </p>
            </div>

            <ul class="space-y-4">
                <li>
                    <a href="/ai/gpt4-turbo"
                        class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 transition duration-200 hover:shadow-md hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg text-green-700 dark:text-green-300">GPT-4 Turbo</span>
                            <span
                                class="text-xs font-semibold px-3 py-1 bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100 rounded-full">GRATIS</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Kecerdasan tertinggi, cocok untuk pemrograman tingkat lanjut dan *debugging* yang kompleks.
                        </p>
                    </a>
                </li>
                <li>
                    <a href="/ai/gemini-pro"
                        class="block p-4 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 transition duration-200 hover:shadow-md hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg text-indigo-700 dark:text-indigo-300">Gemini Pro</span>
                            <span
                                class="text-xs font-semibold px-3 py-1 bg-indigo-200 text-indigo-800 dark:bg-indigo-700 dark:text-indigo-100 rounded-full">REKOMENDASI</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Model serbaguna dari Google, ideal untuk pembelajaran umum dan pertanyaan dasar hingga menengah.
                        </p>
                    </a>
                </li>
                <li>
                    <a href="/ai/claude-haiku"
                        class="block p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 transition duration-200 hover:shadow-md hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg text-blue-700 dark:text-blue-300">Claude 3 Haiku</span>
                            <span
                                class="text-xs font-semibold px-3 py-1 bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-100 rounded-full">CEPAT</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Fokus pada penjelasan yang detail, jernih, dan cepat. Cocok untuk memahami konsep baru.
                        </p>
                    </a>
                </li>
            </ul>
        </div>
    </div>


    <div
        class="mt-12 bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-700 dark:to-purple-800 p-8 rounded-2xl shadow-2xl text-center relative overflow-hidden transform hover:scale-[1.01] transition duration-300">
        <x-heroicon-o-light-bulb
            class="w-14 h-14 text-yellow-300 dark:text-yellow-200 mx-auto mb-3 opacity-90 animate-pulse" />
        <h2 class="text-2xl font-extrabold text-white leading-snug">🚀 Jangan Berhenti Belajar!</h2>
        <p class="mt-2 text-indigo-100 dark:text-indigo-200 text-lg italic max-w-2xl mx-auto opacity-90">
            "Setiap baris kode adalah langkah maju. Manfaatkan kecerdasan buatan untuk mempercepat proses belajar Anda."
        </p>
        <div class="absolute -bottom-10 -right-10 opacity-10">
            <x-heroicon-o-sparkles class="w-40 h-40 text-white" />
        </div>
        <div class="absolute -top-10 -left-10 opacity-10 transform rotate-12">
            <x-heroicon-o-academic-cap class="w-40 h-40 text-white" />
        </div>
    </div>
</div>