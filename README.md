# Baricode Community

Platform komunitas ngoding yang didedikasikan untuk menyediakan kurikulum pembelajaran terstruktur dan gratis, serta ekosistem kolaborasi yang aktif. Proyek ini bertujuan untuk memberdayakan setiap orang agar dapat menguasai keterampilan pemrograman tanpa batasan biaya.

## Fitur Utama

-   **Kurikulum Terstruktur**: Materi belajar yang dirancang dari tingkat dasar hingga mahir.
-   **Akses Mandiri**: Pengguna dapat belajar sesuai kecepatan dan waktu luang mereka.
-   **Forum Komunitas**: Tempat untuk berdiskusi, bertanya, dan saling membantu antar anggota.
-   **Proyek Kolaborasi**: Kesempatan untuk menerapkan ilmu yang didapat dengan membangun proyek nyata bersama.
-   **Halaman Khusus**: Halaman khusus yang terorganisir, termasuk `Landing Page`, `Tentang Kami`, `Daftar Kursus`, `Halaman Detail Kursus`, dan `Halaman Login`.

## Teknologi yang Digunakan

-   **Laravel**: Kerangka kerja PHP yang kuat untuk backend.
-   **Livewire**: Kerangka kerja *full-stack* yang membuat pengembangan antarmuka dinamis menjadi mudah.
-   **Laravel Folio**: Sistem *file-based routing* yang mempercepat pembuatan halaman.
-   **Blade**: *Template engine* Laravel yang elegan untuk tampilan frontend.
-   **Tailwind CSS**: Kerangka kerja CSS untuk membangun antarmuka yang modern dan responsif.
-   **MySQL**: Sistem manajemen basis data relasional.

## Cara Instalasi

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek di lingkungan lokalmu.

### Prasyarat

Pastikan kamu sudah menginstal:
-   PHP (versi 8.2 atau lebih tinggi)
-   Composer
-   Node.js & npm
-   MySQL atau database lain yang kompatibel

### Langkah-langkah

1.  **Kloning Repositori:**
    ```bash
    git clone https://github.com/baricode-community/baricode-platform
    cd baricode-platform
    ```

2.  **Instal Dependensi Composer dan NPM:**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Lingkungan:**
    -   Salin file `.env.example` menjadi `.env`.
    -   Sesuaikan konfigurasi database di file `.env`.
    -   Jalankan perintah berikut untuk menghasilkan kunci aplikasi.
    ```bash
    php artisan key:generate
    ```

4.  **Migrasi dan Seed Database:**
    -   Jalankan migrasi untuk membuat tabel database.
    -   Gunakan seeder untuk mengisi data awal (kursus, modul, dll.).
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Jalankan Server:**
    -   Jalankan server pengembangan Laravel.
    ```bash
    composer run dev
    ```

Sekarang kamu bisa mengakses proyek di `http://127.0.0.1:8000`.

## Kontribusi

Kami sangat menyambut kontribusi dari siapa pun! Jika kamu tertarik untuk berkontribusi, silakan ikuti langkah-langkah berikut:
1.  *Fork* repositori ini.
2.  Buat *branch* baru untuk fitur atau perbaikanmu (`git checkout -b fitur/nama-fitur-baru`).
3.  Lakukan *commit* atas perubahanmu (`git commit -am 'Tambahkan fitur baru'`).
4.  *Push* ke *branch* (`git push origin fitur/nama-fitur-baru`).
5.  Buat *Pull Request* baru.
