<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FurnitureScheduleLine>
 */
class FurnitureScheduleLineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'item_id' => Item::factory(),
            'parent_line_id' => null,
            'fabric_supplier_id' => null,
            'fabric_notes' => null,
            'quantity' => fake()->numberBetween(1, 6),
            'fabric_price_pm' => null,
            'price_override' => null,
            'markup_target_pct' => fake()->randomFloat(2, 20, 60),
            'required_by' => null,
            'delivery_location_id' => null,
            'include_on_po' => false,
            'finish_id' => null,
            'internal_cost_manual' => null,
        ];
    }
}
