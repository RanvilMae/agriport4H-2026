<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Core Seeders First
        $this->call([
            RegionSeeder::class,
            LsaLevelSeeder::class,
            UserSeeder::class,
            SuffixSeeder::class,
            LocationSeeder::class,
            MemberSeeder::class,
        ]);
    }
}