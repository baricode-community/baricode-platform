@extends('layouts.base')

@section('title', 'Cara Belajar Efektif')

@section('content')
<section class="py-20 md:py-32 px-4 bg-gray-900 text-white">
    {{-- Hero Section --}}
    <div class="max-w-4xl mx-auto text-center mb-20">
        <div class="flex justify-center mb-8">
            <span class="inline-flex items-center px-6 py-2.5 rounded-full bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white font-bold text-base shadow-lg tracking-wide animate-pulse">
                🌟 Panduan Self-Learning Efektif
            </span>
        </div>
        <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent drop-shadow-lg">
            Belajar Mandiri, <span class="text-white underline decoration-indigo-400 decoration-4 underline-offset-4">Jadi Developer Andal</span>
        </h1>
        <p class="text-2xl md:text-3xl text-gray-200 mb-10 font-light drop-shadow">
            Temukan cara belajar <span class="font-semibold text-indigo-300">fleksibel</span>, <span class="font-semibold text-purple-300">terstruktur</span>, dan <span class="font-semibold text-pink-300">didukung komunitas</span>. Mulai dari <span class="font-semibold text-indigo-200">computational thinking</span> hingga membangun portofolio nyata!
        </p>
        <div class="flex flex-col md:flex-row justify-center gap-6">
            <a href="{{ route('login') }}" wire:navigate class="inline-block px-10 py-4 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white font-bold text-lg shadow-xl hover:scale-105 hover:from-indigo-600 hover:to-pink-600 transition-all duration-200">
                Mulai Belajar Sekarang 🚀
            </a>
            <a href="#tips" class="inline-block px-10 py-4 rounded-xl border-2 border-indigo-400 text-indigo-200 font-bold text-lg hover:bg-indigo-800/30 hover:text-white transition-all duration-200 relative">
                Lihat Tips Sukses 💡
                <span class="absolute -top-3 -right-3 bg-yellow-400 text-gray-900 text-xs font-semibold px-2 py-0.5 rounded-full shadow-md animate-pulse">Segera Hadir</span>
            </a>
        </div>
    </div>
    
    <div class="max-w-5xl mx-auto text-center mb-20">
        <div class="relative mb-10">
            <svg class="absolute left-0 top-1/2 -translate-y-1/2 w-16 h-16 text-indigo-700 opacity-30 -z-10" fill="none" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4" />
            </svg>
            <p class="text-2xl md:text-3xl text-gray-200 italic font-medium drop-shadow">
                Kami hadir sebagai solusi <span class="text-indigo-300 font-bold">brain root</span>, terlalu banyak tutorial dan course apalagi semenjak adanya AI sehingga tidak jarang kita merasa bingung mulai dari mana.
            </p>
            <svg class="absolute right-0 top-1/2 -translate-y-1/2 w-16 h-16 text-pink-700 opacity-30 -z-10" fill="none" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4" />
            </svg>
        </div>
        <div class="flex justify-center mb-8">
            <span class="inline-flex items-center px-6 py-2.5 rounded-full bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 text-white font-bold text-base shadow-lg tracking-wide animate-bounce">
                ✨ Temukan Konsistensi Belajarmu Bersama Kami! ✨
            </span>
        </div>
        <p class="text-lg md:text-xl text-gray-300 font-light">
            Dengan <span class="font-semibold text-indigo-300">kurikulum terstruktur</span> dan <span class="font-semibold text-purple-300">dukungan komunitas</span>, kami bantu kamu fokus dan mencapai tujuan belajarmu.
        </p>
    </div>

    <!-- Stats Section -->
    <div class="max-w-4xl mx-auto text-center">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="text-2xl font-bold text-indigo-400">100%</div>
                <div class="text-sm text-gray-300">📈 Tingkat Keberhasilan</div>
            </div>
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="text-2xl font-bold text-green-400">Mandiri</div>
                <div class="text-sm text-gray-300">🎯 Pembelajaran Fleksibel</div>
            </div>
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-400">Banyak</div>
                <div class="text-sm text-gray-300">💼 Teman Belajar Dari 0</div>
            </div>
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-400">Selalu</div>
                <div class="text-sm text-gray-300">🤝 Support Komunitas</div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto space-y-16">
        {{-- Motivasi Mengapa Mempelajari Dunia Koding --}}
        <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700">
            <div class="text-center mb-8">
                <span class="text-yellow-400 text-sm font-semibold uppercase tracking-wider">🔥 Motivasi</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Kenapa Harus Belajar Koding?</h2>
                <p class="text-gray-300 text-lg">
                    Dunia digital berkembang pesat. Koding bukan hanya untuk jadi programmer, tapi juga untuk melatih <span class="font-semibold text-yellow-300">problem solving</span>, <span class="font-semibold text-yellow-300">berpikir logis</span>, dan membuka banyak peluang karier masa depan.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-4xl mb-3">💼</div>
                    <h4 class="font-semibold mb-2">Peluang Karier Luas</h4>
                    <p class="text-sm text-gray-300">Skill koding dibutuhkan di berbagai industri: teknologi, keuangan, kesehatan, hingga kreatif.</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-3">🧠</div>
                    <h4 class="font-semibold mb-2">Melatih Pola Pikir</h4>
                    <p class="text-sm text-gray-300">Belajar koding membantu kamu berpikir sistematis, analitis, dan kreatif dalam memecahkan masalah.</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-3">🌍</div>
                    <h4 class="font-semibold mb-2">Bisa Belajar dari Mana Saja</h4>
                    <p class="text-sm text-gray-300">Dengan internet, kamu bisa belajar koding secara mandiri tanpa batasan waktu dan tempat.</p>
                </div>
            </div>
        </div>

        <!-- Langkah 0: Computational Thinking (NEW) -->
        <div class="bg-gradient-to-r from-blue-900/50 to-teal-900/50 rounded-2xl p-8 border border-blue-500/30">
            <div class="text-center mb-8">
                <span class="text-blue-400 text-sm font-semibold uppercase tracking-wider">🧠 Langkah 0 (Opsional)</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-6">Mulai dengan Computational Thinking</h2>
                <p class="text-gray-300 text-lg">
                    Sebelum terjun ke coding, pelajari dulu cara berpikir seperti programmer. Computational thinking akan membantu kamu memecah masalah kompleks menjadi langkah-langkah sederhana.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h4 class="font-semibold text-blue-300 mb-3">🎯 Apa itu Computational Thinking?</h4>
                    <ul class="text-gray-300 space-y-2">
                        <li>🔍 <strong>Decomposition:</strong> Memecah masalah besar jadi bagian kecil</li>
                        <li>🔍 <strong>Pattern Recognition:</strong> Mencari pola dalam masalah</li>
                        <li>🔍 <strong>Abstraction:</strong> Fokus pada hal penting, abaikan detail</li>
                        <li>🔍 <strong>Algorithm:</strong> Membuat langkah-langkah solusi</li>
                    </ul>
                </div>
                
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h4 class="font-semibold text-blue-300 mb-3">📚 Sumber Belajar yang Kami Sediakan:</h4>
                    <ul class="text-gray-300 space-y-2">
                        <li>🧩 Studi kasus masalah sehari-hari</li>
                        <li>▶️ Materi video dari YouTube yang bisa kamu akses gratis</li>
                        <li>📝 Artikel pilihan yang mudah dipahami untuk belajar mandiri</li>
                    </ul>
                    <p class="text-sm text-gray-400 mt-3">
                        Semua materi bisa kamu pelajari sendiri sesuai kecepatanmu.
                    </p>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-teal-900/30 border border-teal-500/30 rounded-lg text-center">
                <p class="text-teal-200">
                    💡 <strong>Waktu Ideal:</strong> 1-2 minggu untuk memahami dasar-dasar sebelum mulai coding
                </p>
            </div>
        </div>

        <!-- Pembelajaran Mandiri Section (NEW) -->
        <div class="bg-gradient-to-r from-green-900/50 to-emerald-900/50 rounded-2xl p-8 border border-green-500/30">
            <div class="text-center mb-8">
                <span class="text-green-400 text-sm font-semibold uppercase tracking-wider">🎓 Metode Pembelajaran</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-6">Belajar Mandiri dengan Dukungan Penuh</h2>
                <p class="text-gray-300 text-lg">
                    Kami percaya setiap orang punya gaya belajar yang berbeda. Makanya kami menyediakan berbagai sumber belajar yang mudah dipahami untuk mendukung pembelajaran mandirimu.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800/50 rounded-lg p-6 text-center">
                    <div class="text-4xl mb-4">📚</div>
                    <h4 class="font-semibold text-green-300 mb-3">Materi Terstruktur</h4>
                    <ul class="text-sm text-gray-300 space-y-1">
                        <li>• Step-by-step tutorial</li>
                        <li>• Flowchart konsep</li>
                        <li>• Mind map materi</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6">
                <h4 class="font-semibold text-green-300 mb-4 text-center">🌟 Fitur Khusus untuk Pembelajaran Mandiri:</h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <ul class="text-gray-300 space-y-2">
                            <li>⏰ <strong>Flexible Schedule:</strong> Belajar kapan saja, di mana saja</li>
                            <li>📊 <strong>Progress Tracking:</strong> Dashboard untuk monitor kemajuan</li>
                        </ul>
                    </div>
                    <div>
                        <ul class="text-gray-300 space-y-2">
                            <li>
                                🔄 <strong>Repeat System:</strong> Ulangi materi sebanyak yang kamu mau
                                <span class="ml-2 inline-block bg-yellow-400 text-gray-900 text-xs font-semibold px-2 py-0.5 rounded-full shadow-md">Segera Hadir</span>
                            </li>
                            <li>📝 <strong>Note Taking:</strong> Catat hal penting langsung di platform</li>
                            <li>
                                🎯 <strong>Self Assessment:</strong> Quiz untuk mengukur pemahaman sendiri
                                <span class="ml-2 inline-block bg-yellow-400 text-gray-900 text-xs font-semibold px-2 py-0.5 rounded-full shadow-md">Segera Hadir</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:flex md:items-center md:space-x-8">
            <div class="md:w-1/2">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80" alt="Langkah 1: Pilih Kursus" class="rounded-lg shadow-xl mb-6 md:mb-0">
            </div>
            <div class="md:w-1/2 md:text-left">
            <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">🎯 Langkah 1</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Pilih Jalur Belajarmu</h2>
            <p class="text-gray-300 text-lg mb-4">
                Kami menyediakan berbagai kursus yang terstruktur dari dasar hingga tingkat mahir. Mulai dengan kursus "Computational Thinking" atau "Pondasi Ngoding" jika kamu benar-benar pemula, atau langsung pilih topik yang kamu minati seperti Python, JavaScript, atau Laravel.
            </p>
            
            <!-- Jalur Belajar yang Tersedia -->
            <div class="bg-gray-800 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-indigo-300 mb-2">📚 Jalur Belajar yang Tersedia:</h4>
                <ul class="text-sm text-gray-300 space-y-1">
                <li>🧠 <strong>Pre-Coding:</strong> Computational Thinking, Logika Dasar</li>
                <li>🟢 <strong>Pemula:</strong> HTML, CSS, JavaScript Dasar <span class="italic text-gray-400">(segera hadir)</span></li>
                <li>🟡 <strong>Menengah:</strong> React, Node.js, Database <span class="italic text-gray-400">(segera hadir)</span></li>
                <li>🔴 <strong>Mahir:</strong> DevOps, Microservices, Cloud <span class="italic text-gray-400">(segera hadir)</span></li>
                </ul>
            </div>
            
            <div class="mt-4 p-3 bg-blue-900/30 border border-blue-500/30 rounded-lg">
                <p class="text-sm text-blue-200">
                💡 <strong>Tips:</strong> Ikuti assessment awal untuk mendapatkan rekomendasi jalur yang tepat untukmu!
                </p>
            </div>
            </div>
        </div>

        <div class="md:flex md:flex-row-reverse md:items-center md:space-x-8">
            <div class="md:w-1/2 md:ml-8">
            <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=600&q=80" alt="Langkah 2: Selesaikan Setiap Modul" class="rounded-lg shadow-xl mb-6 md:mb-0">
            </div>
            <div class="md:w-1/2 md:text-right">
            <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">✅ Langkah 2</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Selesaikan Setiap Modul Secara Mandiri</h2>
            <p class="text-gray-300 text-lg mb-4">
                Setiap kursus terbagi menjadi modul-modul kecil yang mudah dicerna. Pelajari setiap video dan latihan di satu modul sebelum lanjut ke modul berikutnya. Gunakan tombol "Tandai Selesai" untuk melacak progres belajarmu di dasbor.
            </p>
            
            <!-- Fitur Modul -->
            <div class="bg-gray-800 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-indigo-300 mb-2">🎬 Setiap Modul Berisi:</h4>
                <ul class="text-sm text-gray-300 space-y-1">
                <li>🏆 Badge setelah menyelesaikan modul</li>
                <li>📝 Area untuk membuat catatan personal</li>
                </ul>
            </div>
            
            <div class="mt-4 p-3 bg-green-900/30 border border-green-500/30 rounded-lg">
                <p class="text-sm text-green-200">
                ⏱️ <strong>Belajar Fleksibel:</strong> Atur waktu belajarmu sendiri - 30 menit atau 3 jam, sesuai kenyamananmu
                </p>
            </div>
            </div>
        </div>

        <div class="md:flex md:items-center md:space-x-8">
            <div class="md:w-1/2">
            <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=600&q=80" alt="Langkah 3: Terapkan dengan Proyek Mandiri" class="rounded-lg shadow-xl mb-6 md:mb-0">
            </div>
            <div class="md:w-1/2 md:text-left">
            <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">🛠️ Langkah 3</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4 flex items-center gap-3">
                Terapkan dengan Proyek Mandiri
                <span class="inline-block bg-yellow-400 text-gray-900 text-xs font-semibold px-2 py-0.5 rounded-full shadow-md animate-pulse">Segera Hadir</span>
            </h2>
            <p class="text-gray-300 text-lg mb-4">
                Teori saja tidak cukup. Di akhir setiap kursus, kamu akan menemukan proyek-proyek praktis dengan panduan lengkap. Kerjakan proyek tersebut secara mandiri untuk menguji pemahamanmu dan bangun portofolio yang bisa kamu pamerkan.
            </p>
            
            <!-- Jenis Proyek -->
            <div class="bg-gray-800 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-indigo-300 mb-2">🚀 Jenis Proyek yang Akan Kamu Buat:</h4>
                <ul class="text-sm text-gray-300 space-y-1">
                <li>🧩 Puzzle game untuk computational thinking</li>
                <li>🌐 Website portofolio personal</li>
                <li>🛒 E-commerce mini dengan keranjang</li>
                <li>📱 Aplikasi To-Do List dengan database</li>
                <li>🎮 Game sederhana dengan JavaScript</li>
                <li>📊 Dashboard analytics dengan chart</li>
                </ul>
            </div>
            
            <div class="mt-4 p-3 bg-purple-900/30 border border-purple-500/30 rounded-lg">
                <p class="text-sm text-purple-200">
                🎯 <strong>Bonus:</strong> Proyek terbaik akan ditampilkan di galeri showcase kami dengan credit ke pembuatnya!
                </p>
            </div>
            </div>
        </div>

        <div class="md:flex md:flex-row-reverse md:items-center md:space-x-8">
            <div class="md:w-1/2 md:ml-8">
            <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=600&q=80" alt="Langkah 4: Berinteraksi di Komunitas" class="rounded-lg shadow-xl mb-6 md:mb-0">
            </div>
            <div class="md:w-1/2 md:text-right">
            <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">👥 Langkah 4</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Berinteraksi di Komunitas</h2>
            <p class="text-gray-300 text-lg mb-4">
                Meski belajarnya mandiri, kamu tidak sendirian! Bergabunglah di komunitas untuk bertanya, berbagi pengalaman, dan membantu sesama. Belajar mandiri akan lebih menyenangkan dengan dukungan komunitas.
            </p>
            
            <!-- Aktivitas Komunitas -->
            <div class="bg-gray-800 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-indigo-300 mb-2">🌟 Aktivitas Komunitas:</h4>
                <ul class="text-sm text-gray-300 space-y-1">
                    <li>💬 Forum diskusi bersama teman-teman di grub WhatsApp</li>
                </ul>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-900/30 border border-yellow-500/30 rounded-lg">
                <p class="text-sm text-yellow-200">
                🤝 <strong>Fun Fact:</strong> Self-learner yang aktif di komunitas lebih sukses menyelesaikan projectnya!
                </p>
            </div>
            </div>
        </div>
        
        <!-- Langkah Bonus -->
        <div class="bg-gradient-to-r from-indigo-900/50 to-purple-900/50 rounded-2xl p-8 border border-indigo-500/30">
            <div class="text-center">
                <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">🎁 Langkah Bonus</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-6">Dapatkan Sertifikat & Karier</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-semibold text-indigo-300 mb-3">🏆 Sertifikat Resmi:</h4>
                    <ul class="text-gray-300 space-y-2">
                        <li>✅ Sertifikat completion untuk setiap kursus</li>
                        <li>✅ Sertifikat khusus "Self-Directed Learner"</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Tips Sukses -->
        <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700">
            <h3 class="text-2xl font-bold text-center mb-8">💡 Tips Sukses untuk Self-Learner</h3>
            
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="text-center">
                    <div class="text-4xl mb-3">⏰</div>
                    <h4 class="font-semibold mb-2">Konsisten itu Kunci</h4>
                    <p class="text-sm text-gray-300">Buat jadwal belajar rutin. 30 menit setiap hari lebih efektif daripada 5 jam sekali seminggu</p>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl mb-3">🎯</div>
                    <h4 class="font-semibold mb-2">Set Goal yang Realistis</h4>
                    <p class="text-sm text-gray-300">Tentukan target mingguan dan bulanan. Rayakan pencapaian kecil untuk motivasi</p>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl mb-3">📝</div>
                    <h4 class="font-semibold mb-2">Dokumentasikan Progress</h4>
                    <p class="text-sm text-gray-300">Buat jurnal belajar, catat insight, dan track kemajuan coding skill mu</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="text-center">
                    <div class="text-4xl mb-3">🤔</div>
                    <h4 class="font-semibold mb-2">Jangan Takut Stuck</h4>
                    <p class="text-sm text-gray-300">Stuck itu normal dalam self-learning. Manfaatkan forum, dokumentasi, dan resource yang disediakan</p>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl mb-3">🔄</div>
                    <h4 class="font-semibold mb-2">Practice Makes Perfect</h4>
                    <p class="text-sm text-gray-300">Koding setiap hari, build side project, dan jangan cuma nonton tutorial</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="text-center bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8">
            <h3 class="text-2xl md:text-3xl font-bold mb-4">🚀 Siap Memulai Perjalanan Self-Learning?</h3>
            <p class="text-lg mb-6 opacity-90">Bergabunglah dengan ribuan self-learner yang sudah memulai kariernya dari sini!</p>
            <div class="space-x-4">
                <a href="{{ route('courses') }}" wire:navigate class="inline-block border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/10 transition-colors">
                    Lihat Semua Kursus 📚
                </a>
            </div>
        </div>
    </div>
</section>
@endsection