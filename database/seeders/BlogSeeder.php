<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\Auth\User;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama atau buat jika tidak ada
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@baricode.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $blogs = [
            [
                'title' => 'Mengenal Laravel: Framework PHP yang Powerful',
                'slug' => 'mengenal-laravel-framework-php-yang-powerful',
                'content' => 'Laravel adalah salah satu framework PHP yang paling populer dan powerful di dunia. Dikembangkan oleh Taylor Otwell, Laravel menyediakan sintaks yang elegan dan ekspresif yang memudahkan pengembangan aplikasi web.

Laravel menawarkan berbagai fitur unggulan seperti:
- Eloquent ORM untuk interaksi database yang mudah
- Blade templating engine yang powerful
- Artisan command line interface
- Built-in authentication dan authorization
- Migration untuk version control database
- Queue system untuk background jobs

Dengan Laravel, pengembang dapat membangun aplikasi web yang robust dan scalable dengan lebih cepat dan efisien.',
                'excerpt' => 'Panduan lengkap mengenal Laravel, framework PHP yang powerful dan populer untuk pengembangan aplikasi web modern.',
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Tips Optimasi Performance Website dengan Laravel',
                'slug' => 'tips-optimasi-performance-website-dengan-laravel',
                'content' => 'Performance adalah salah satu faktor krusial dalam pengembangan aplikasi web. Laravel menyediakan berbagai tools dan teknik untuk mengoptimasi performa aplikasi Anda.

Berikut adalah beberapa tips optimasi performance:

1. **Caching**
   - Gunakan Redis atau Memcached untuk session dan cache
   - Implementasikan view caching dan route caching
   - Cache query database yang sering digunakan

2. **Database Optimization**
   - Gunakan eager loading untuk menghindari N+1 problem
   - Indexing pada kolom yang sering di-query
   - Gunakan raw queries untuk operasi kompleks

3. **Asset Optimization**
   - Minify CSS dan JavaScript
   - Gunakan CDN untuk static assets
   - Compress images dan gunakan format modern

4. **Server Configuration**
   - Gunakan OPCache untuk PHP
   - Konfigurasi web server yang optimal
   - Load balancing untuk traffic tinggi',
                'excerpt' => 'Pelajari berbagai teknik dan strategi untuk mengoptimasi performance aplikasi Laravel Anda.',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Membangun API RESTful dengan Laravel Sanctum',
                'slug' => 'membangun-api-restful-dengan-laravel-sanctum',
                'content' => 'Laravel Sanctum menyediakan sistem autentikasi yang ringan untuk SPA (Single Page Applications), mobile applications, dan simple, token-based APIs.

Keunggulan Laravel Sanctum:
- Token authentication untuk mobile apps
- Session authentication untuk SPAs
- Multiple guards support
- Token abilities dan scopes

Tutorial step-by-step:

1. **Installation**
   ```bash
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Configuration**
   - Tambahkan middleware Sanctum
   - Konfigurasi CORS untuk SPA
   - Setup token model

3. **Implementation**
   - Buat authentication endpoints
   - Protect routes dengan sanctum middleware
   - Handle token refresh dan logout

Dengan Sanctum, Anda dapat membangun API yang secure dan scalable dengan mudah.',
                'excerpt' => 'Tutorial lengkap membangun API RESTful yang secure menggunakan Laravel Sanctum untuk autentikasi.',
                'status' => 'draft',
                'published_at' => null,
            ],
            [
                'title' => 'Modern Frontend Development dengan Vue.js dan Laravel',
                'slug' => 'modern-frontend-development-dengan-vuejs-dan-laravel',
                'content' => 'Kombinasi Vue.js dan Laravel adalah stack yang powerful untuk membangun aplikasi web modern. Vue.js menyediakan reactive frontend, sementara Laravel menghandle backend dengan elegan.

Setup Development Environment:
1. Install Node.js dan npm
2. Setup Laravel dengan Vite
3. Konfigurasi Vue.js dengan Laravel

Fitur-fitur Modern:
- Component-based architecture
- State management dengan Vuex/Pinia
- Real-time updates dengan WebSockets
- Server-side rendering dengan Nuxt.js

Best Practices:
- Separation of concerns
- API-first development
- Progressive Web App features
- Testing dengan Jest dan Laravel Dusk

Dengan stack ini, Anda dapat membangun aplikasi yang responsive, interactive, dan maintainable.',
                'excerpt' => 'Pelajari cara mengintegrasikan Vue.js dengan Laravel untuk membangun aplikasi web modern.',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::create($blogData);
        }
    }
}
