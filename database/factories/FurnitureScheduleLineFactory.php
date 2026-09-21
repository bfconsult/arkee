<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FurnitureScheduleLine>
 */
class FurnitureScheduleLineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'item_id' => Item::factory(),
            'fabric_notes' => null,
            'quantity' => fake()->numberBetween(1, 6),
            'price_override' => null,
            'markup_target_pct' => fake()->randomFloat(2, 20, 60),
            'required_by' => null,
            'include_on_po' => false,
            'internal_cost_manual' => null,
        ];
    }
}
