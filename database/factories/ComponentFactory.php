<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Component>
 */
class ComponentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'name' => fake()->randomElement(['Frame', 'Seat Cushion', 'Backrest', 'Legs']),
            'quantity' => fake()->numberBetween(1, 4),
            'notes' => null,
        ];
    }
}
