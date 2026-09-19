<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PackagingType>
 */
class PackagingTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Crate', 'Carton', 'Pallet', 'Flat Pack', 'Roll']),
        ];
    }
}
