<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verifiedAt = Carbon::now();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'email_verified_at' => $verifiedAt,
            'password' => Hash::make('password'),
            'role' => 'admin',
            'remember_token' => Str::random(10),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Editor $i",
                'email' => "editor$i@example.com",
                'email_verified_at' => $verifiedAt,
                'password' => Hash::make('password'),
                'role' => 'editor',
                'remember_token' => Str::random(10),
            ]);
        }


        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'email_verified_at' => $verifiedAt,
                'password' => Hash::make('password'),
                'role' => 'user',
                'remember_token' => Str::random(10),
            ]);
        }

    }
}
