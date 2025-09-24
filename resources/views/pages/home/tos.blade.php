@extends('layouts.base')

@section('title', 'Syarat dan Ketentuan - Baricode')

@section('content')
<div class="mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-gray-50 rounded-lg shadow-lg p-8 mt-12">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">
            📋 Syarat dan Ketentuan Layanan
        </h1>
        
        <div class="prose max-w-none">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-blue-800">
                    <strong>🔔 Penting:</strong> Dengan menggunakan layanan Baricode, Anda dianggap telah membaca, memahami, dan menyetujui semua syarat dan ketentuan berikut.
                </p>
            </div>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">🎯 1. Tentang Layanan Kami</h2>
                <p class="mb-4 text-gray-700">
                    Baricode adalah platform pembelajaran pemrograman open source yang menyediakan:
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li>📚 Materi pembelajaran programming gratis</li>
                    <li>💻 Tutorial dan panduan coding</li>
                    <li>👥 Komunitas developer Indonesia</li>
                    <li>🔓 Kode sumber yang dapat diakses dan dimodifikasi</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">⚖️ 2. Lisensi Software</h2>
                <div class="bg-green-50 p-4 rounded-lg mb-4">
                    <h3 class="font-semibold text-green-800 mb-2">📜 GNU General Public License v3.0:</h3>
                    <p class="text-green-700 mb-2">
                        Platform Baricode dilisensikan di bawah GNU General Public License versi 3 (GPL-3.0). Ini berarti:
                    </p>
                    <ul class="list-disc pl-6 text-green-700">
                        <li>✅ Anda bebas menggunakan, mempelajari, dan memodifikasi kode sumber</li>
                        <li>✅ Anda bebas mendistribusikan salinan asli atau modifikasi</li>
                        <li>✅ Anda dapat menggunakan untuk tujuan komersial</li>
                        <li>⚠️ Setiap modifikasi yang didistribusikan harus menggunakan lisensi yang sama (GPL-3.0)</li>
                        <li>⚠️ Anda harus menyediakan kode sumber untuk setiap versi yang didistribusikan</li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-blue-700">
                        <strong>📖 Lisensi lengkap:</strong> Silakan baca teks lengkap GPL-3.0 di 
                        <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="underline">https://www.gnu.org/licenses/gpl-3.0.html</a>
                    </p>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">👤 3. Akun Pengguna</h2>
                <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                    <h3 class="font-semibold text-yellow-800 mb-2">📝 Pendaftaran:</h3>
                    <ul class="list-disc pl-6 text-yellow-700">
                        <li>Jaga keamanan akun dan password Anda</li>
                        <li>Gunakan username dan data akun yang sesuai dengan etika komunitas</li>
                    </ul>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-purple-800 mb-2">👥 Sebagai Member Komunitas:</h3>
                    <ul class="list-disc pl-6 text-purple-700">
                        <li>Saling membantu sesama developer</li>
                        <li>Berbagi pengalaman dan tips coding</li>
                        <li>Menghormati perbedaan level skill programming</li>
                        <li>Aktif berpartisipasi dalam diskusi teknis</li>
                        <li>Berkontribusi pada pengembangan platform (opsional)</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">✅ 4. Aturan Penggunaan</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-800 mb-2">✨ Yang Boleh Dilakukan:</h3>
                        <ul class="list-disc pl-6 text-green-700 text-sm">
                            <li>Menggunakan, mempelajari, dan memodifikasi platform</li>
                            <li>Mendistribusikan salinan atau modifikasi (dengan GPL-3.0)</li>
                            <li>Menggunakan untuk tujuan komersial</li>
                            <li>Berbagi pengetahuan dengan sesama pengguna</li>
                            <li>Memberikan feedback konstruktif</li>
                            <li>Berkontribusi pada pengembangan</li>
                        </ul>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-red-800 mb-2">❌ Yang Dilarang:</h3>
                        <ul class="list-disc pl-6 text-red-700 text-sm">
                            <li>Mendistribusikan modifikasi tanpa menyediakan kode sumber</li>
                            <li>Menggunakan lisensi yang tidak kompatibel untuk modifikasi</li>
                            <li>Menyebarkan konten yang melanggar hukum</li>
                            <li>Spam, iklan, atau promosi tidak relevan</li>
                            <li>Mengganggu atau merugikan pengguna lain</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">📜 5. Kode Sumber dan Kontribusi</h2>
                <div class="bg-indigo-50 p-4 rounded-lg mb-4">
                    <h3 class="font-semibold text-indigo-800 mb-2">💻 Akses Kode Sumber:</h3>
                    <ul class="list-disc pl-6 text-indigo-700">
                        <li>Kode sumber tersedia di repository publik</li>
                        <li>Anda dapat mengunduh, mempelajari, dan memodifikasi</li>
                        <li>Dokumentasi untuk pengembang tersedia</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800 mb-2">🤝 Kontribusi:</h3>
                    <ul class="list-disc pl-6 text-green-700">
                        <li>Kontribusi sangat diterima melalui pull request</li>
                        <li>Semua kontribusi akan dilisensikan di bawah GPL-3.0</li>
                        <li>Ikuti panduan kontribusi yang tersedia</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">🔒 6. Privasi dan Data</h2>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <p class="text-indigo-800 mb-2">
                        <strong>🛡️ Komitmen Kami:</strong>
                    </p>
                    <ul class="list-disc pl-6 text-indigo-700">
                        <li>Data pribadi Anda akan dijaga kerahasiaannya</li>
                        <li>Kami tidak akan menjual data Anda ke pihak ketiga</li>
                        <li>Data hanya digunakan untuk meningkatkan layanan</li>
                        <li>Anda dapat menghapus akun kapan saja</li>
                        <li>Lihat kebijakan privasi lengkap untuk detail lebih lanjut</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">🚫 7. Penafian Jaminan (GPL-3.0 Section 15)</h2>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <p class="text-gray-700 mb-2">
                        <strong>⚠️ TIDAK ADA JAMINAN:</strong>
                    </p>
                    <p class="text-gray-700 mb-2">
                        Sesuai dengan GPL-3.0, program ini disediakan "SEBAGAIMANA ADANYA" tanpa jaminan apapun, 
                        baik tersurat maupun tersirat, termasuk namun tidak terbatas pada jaminan kelayakan untuk 
                        diperdagangkan dan kesesuaian untuk tujuan tertentu.
                    </p>
                    <ul class="list-disc pl-6 text-gray-700">
                        <li>Seluruh risiko kualitas dan kinerja program ada pada Anda</li>
                        <li>Jika program terbukti cacat, Anda menanggung biaya perbaikan</li>
                    </ul>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">⚠️ 8. Pembatasan Tanggung Jawab (GPL-3.0 Section 16)</h2>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-red-700 mb-2">
                        Sesuai dengan GPL-3.0, dalam hal apapun pemegang hak cipta atau pihak lain yang memodifikasi 
                        dan/atau menyampaikan program tidak akan bertanggung jawab atas kerusakan, termasuk kerusakan 
                        umum, khusus, insidental, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan 
                        menggunakan program.
                    </p>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">🔄 9. Perubahan Ketentuan</h2>
                <p class="mb-4 text-gray-700">
                    📢 Kami berhak mengubah syarat dan ketentuan layanan ini sewaktu-waktu. Lisensi GPL-3.0 
                    tetap berlaku untuk kode sumber. Perubahan akan diberitahukan melalui:
                </p>
                <ul class="list-disc pl-6 text-gray-700">
                    <li>📧 Email ke akun terdaftar</li>
                    <li>📱 Notifikasi di platform</li>
                    <li>🌐 Pengumuman di komunitas</li>
                    <li>📝 Update di repository (<a href="https://github.com/baricode-community/baricode-platform" class="underline text-blue-600" target="_blank">GitHub</a>)</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">📞 10. Kontak</h2>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-blue-800 mb-2">
                        <strong>💬 Ada pertanyaan tentang syarat dan ketentuan atau lisensi?</strong>
                    </p>
                    <p class="text-blue-700">
                        Hubungi kami di: <strong>support@baricode.org</strong> atau melalui form kontak di website.
                    </p>
                </div>
            </section>

            <div class="border-t pt-6 mt-8">
                <p class="text-center text-gray-600">
                    <strong>📅 Terakhir diperbarui:</strong> {{ date('d F Y') }}<br>
                    <strong>🏢 Baricode Community</strong><br>
                    <strong>📜 Lisensi:</strong> GNU General Public License v3.0
                </p>
                <div class="text-center mt-4">
                    <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-blue-600 underline text-sm">
                        📖 Baca GPL-3.0 Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection