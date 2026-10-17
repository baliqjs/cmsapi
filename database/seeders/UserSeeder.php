<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'nama' => 'Administrator',
                'password' => Hash::make('password'),
                'level' => 'admin',
            ],
            [
                'username' => 'user',
                'nama' => 'User Biasa',
                'password' => Hash::make('password'),
                'level' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
