<?php

namespace Database\Factories;

use App\Models\Component;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'component_id' => Component::factory(),
            'name' => fake()->randomElement(['Linen Fabric', 'Oak Timber', 'Foam Fill', 'Brass Fitting']),
            'supplier_id' => Supplier::factory(),
            'code_supplier' => fake()->bothify('SUP-####'),
            'unit_cost' => fake()->randomFloat(2, 10, 500),
            'meterage' => fake()->randomFloat(3, 1, 15),
            'notes' => null,
        ];
    }
}
