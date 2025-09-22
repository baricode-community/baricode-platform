<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\DevSeeder;
use Database\Seeders\SpatieSeeder;
use Database\Seeders\SettingSeeder;

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
        }

        DB::commit();
    }
}
