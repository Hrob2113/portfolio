<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Robin Hrdlicka',
            'email' => 'admin@robinhrdlicka.dev',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            TranslationSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
