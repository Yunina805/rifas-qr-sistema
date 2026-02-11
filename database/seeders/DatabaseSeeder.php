<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void
    {
        // 1. CREAR ADMIN
        \App\Models\User::factory()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@sistema.com',
            'password' => bcrypt('admin123'), // Contraseña
            'role' => 'admin',
        ]);

        // 2. CREAR VENDEDOR
        \App\Models\User::factory()->create([
            'name' => 'Vendedor Juan',
            'email' => 'juan@sistema.com',
            'password' => bcrypt('vendedor123'),
            'role' => 'vendedor',
        ]);
    }
}
