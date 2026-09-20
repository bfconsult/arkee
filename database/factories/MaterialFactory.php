<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['Linen Fabric', 'Oak Timber', 'Foam Fill', 'Brass Fitting', 'Powder-Coated Steel', 'Velvet', 'Rattan', 'Marble']);

        return [
            'name' => $name,
            'is_fabric' => in_array($name, ['Linen Fabric', 'Velvet'], true),
            'supplier_id' => Supplier::factory(),
            'code_supplier' => fake()->bothify('SUP-####'),
            'unit_cost' => fake()->randomFloat(2, 10, 500),
            'notes' => null,
        ];
    }
}
