<?php

namespace Database\Factories;

use App\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_wallet_id' => $this->faker->uuid(),
            'to_wallet_id' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'status' => $this->faker->randomElement(TransferStatus::cases()),
        ];
    }
}
