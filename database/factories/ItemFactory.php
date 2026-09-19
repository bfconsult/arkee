<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'catalogue_no' => fake()->unique()->bothify('CAT-####'),
            'item_type' => fake()->randomElement(['Sofa', 'Armchair', 'Dining Table', 'Bed Frame']),
            'height_mm' => fake()->numberBetween(400, 1200),
            'width_mm' => fake()->numberBetween(400, 2000),
            'depth_mm' => fake()->numberBetween(400, 1000),
            'packaging_type' => fake()->randomElement(['Crate', 'Carton', 'Pallet']),
            'notes' => null,
        ];
    }
}
