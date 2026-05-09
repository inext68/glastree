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
        $this->call([
            DiocesiSeeder::class,
            ComuniSeeder::class,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@glastree.local',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }
}