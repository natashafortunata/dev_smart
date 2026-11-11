<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smart.test'],
            ['name' => 'Administrator', 'role' => 'admin', 'password' => 'password']
        );

        User::updateOrCreate(
            ['email' => 'pimpinan@smart.test'],
            ['name' => 'Pimpinan', 'role' => 'pimpinan', 'password' => 'password']
        );

        $this->call(ContohDataSeeder::class);
    }
}
