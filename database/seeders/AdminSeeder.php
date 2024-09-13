<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'lastName' => 'Admin',
            'email' => 'topUser',
            'password' => bcrypt('topUser'), // Assurez-vous de hasher le mot de passe
            'role' => 'admin', // Spécifier le rôle comme 'admin'
            'verification_code' => null,
        ]);
    }
}
