<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Usuário Padrão',
            'email' => 'admin@precpago.com.br',
            'password' => bcrypt('senha123'), // Adicione uma senha padrão
        ]);
    }
}
