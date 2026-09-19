<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finish>
 */
class FinishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'material_id' => Material::factory(),
            'name' => fake()->randomElement(['Natural Oak', 'Walnut Stain', 'Charcoal Grey', 'Ivory']),
            'code_supplier' => fake()->bothify('COL-####'),
            'notes' => null,
        ];
    }
}
