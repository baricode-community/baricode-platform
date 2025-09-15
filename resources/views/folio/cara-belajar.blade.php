@extends('layouts.base')

@section('title', 'Cara Belajar Efektif')

@section('content')
<section class="py-20 md:py-32 px-4 bg-gray-900 text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">🚀 Cara Belajar Efektif di Platform Kami</h1>
        <p class="text-lg md:text-xl text-gray-400 mb-12">
            Ikuti panduan ini untuk memaksimalkan pengalaman belajarmu dan menjadi pengembang yang andal. 
            💡 <strong>Kita baru launching nih!</strong>
        </p>
        
        <!-- Stats Section -->
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
                <div class="text-sm text-gray-300">💼 Proyek Portofolio</div>
            </div>
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-400">Hampir Selalu</div>
                <div class="text-sm text-gray-300">🤝 Support Komunitas</div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto space-y-16">
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
                        <li>📖 Panduan visual dengan ilustrasi menarik</li>
                        <li>🎮 Game puzzle untuk latihan logika</li>
                        <li>🧩 Studi kasus masalah sehari-hari</li>
                    </ul>
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
                        <li>• Cheatsheet downloadable</li>
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
                            <li>📅 <strong>Personal Learning Path:</strong> Jalur belajar yang disesuaikan dengan goalmu</li>
                            <li>⏰ <strong>Flexible Schedule:</strong> Belajar kapan saja, di mana saja</li>
                            <li>📊 <strong>Progress Tracking:</strong> Dashboard untuk monitor kemajuan</li>
                        </ul>
                    </div>
                    <div>
                        <ul class="text-gray-300 space-y-2">
                            <li>🔄 <strong>Repeat System:</strong> Ulangi materi sebanyak yang kamu mau</li>
                            <li>📝 <strong>Note Taking:</strong> Catat hal penting langsung di platform</li>
                            <li>🎯 <strong>Self Assessment:</strong> Quiz untuk mengukur pemahaman sendiri</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:flex md:items-center md:space-x-8">
            <div class="md:w-1/2">
                <img src="https://via.placeholder.com/600x400.png?text=Langkah+1" alt="Langkah 1: Pilih Kursus" class="rounded-lg shadow-xl mb-6 md:mb-0">
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
                        <li>🟢 <strong>Pemula:</strong> HTML, CSS, JavaScript Dasar</li>
                        <li>🟡 <strong>Menengah:</strong> React, Node.js, Database</li>
                        <li>🔴 <strong>Mahir:</strong> DevOps, Microservices, Cloud</li>
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
                <img src="https://via.placeholder.com/600x400.png?text=Langkah+2" alt="Langkah 2: Selesaikan Setiap Modul" class="rounded-lg shadow-xl mb-6 md:mb-0">
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
                        <li>📄 Materi tertulis & cheatsheet downloadable</li>
                        <li>🧪 Self-assessment quiz untuk mengukur pemahaman</li>
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
                <img src="https://via.placeholder.com/600x400.png?text=Langkah+3" alt="Langkah 3: Terapkan dengan Proyek" class="rounded-lg shadow-xl mb-6 md:mb-0">
            </div>
            <div class="md:w-1/2 md:text-left">
                <span class="text-indigo-400 text-sm font-semibold uppercase tracking-wider">🛠️ Langkah 3</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4">Terapkan dengan Proyek Mandiri</h2>
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
                <img src="https://via.placeholder.com/600x400.png?text=Langkah+4" alt="Langkah 4: Berinteraksi di Komunitas" class="rounded-lg shadow-xl mb-6 md:mb-0">
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
                        <li>🎪 Weekly coding challenge untuk self-learner</li>
                        <li>🎉 Monthly webinar dan workshop</li>
                        <li>📚 Resource sharing antar member</li>
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
                <a href="#" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Mulai dari Computational Thinking 🧠
                </a>
                <a href="#" class="inline-block border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/10 transition-colors">
                    Lihat Semua Kursus 📚
                </a>
            </div>
        </div>
    </div>
</section>
@endsection