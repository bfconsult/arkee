<?php

namespace Database\Factories;

use App\Models\Component;
use App\Models\FurnitureScheduleLine;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FabricComponent>
 */
class FabricComponentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'furniture_schedule_line_id' => FurnitureScheduleLine::factory(),
            'component_id' => Component::factory()->fabric(),
            'material_id' => Material::factory(),
            'finish_id' => null,
        ];
    }
}
