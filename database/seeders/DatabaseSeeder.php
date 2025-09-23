<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::beginTransaction();

        $this->call([
            SpatieSeeder::class,
            SettingSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DevSeeder::class);
        } else {
            $this->call([
                CourseCategorySeeder::class,
                CourseSeeder::class,
            ]);
        }

        // Membuat pengguna 'test@example.com' jika belum ada.
        if (! User::where('email', 'test@example.com')->exists()) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $user->assignRole('admin');
        }

        DB::commit();
    }
}
