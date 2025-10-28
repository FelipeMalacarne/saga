<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
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
        // User::factory(10)->create();

        Wallet::factory()->create([
            'name' => 'Wallet A',
            'balance' => 5000,
        ]);

        Wallet::factory()->create([
            'name' => 'Wallet B',
            'balance' => 3000,
        ]);
    }
}
