<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Material;
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
            'material_id' => Material::factory(),
            'name' => fake()->randomElement(['Frame', 'Seat Cushion', 'Backrest', 'Legs']),
            'quantity' => fake()->numberBetween(1, 4),
            'notes' => null,
            'supplier_id' => null,
            'code_supplier' => null,
            'unit_cost' => null,
            'meterage' => null,
        ];
    }
}
