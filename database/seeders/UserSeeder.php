<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Dev User',
            'email' => 'dev@email.com',
            'password' => Hash::make('12345678'),
        ]);

        $devRole = \App\Models\Role::factory()->create([
            'name' => 'Developer',
            'slug' => 'developer',
        ]);

        $user->roles()->attach($devRole->id);
    }
}
