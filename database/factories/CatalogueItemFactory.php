<?php

namespace Database\Factories;

use App\Models\CatalogueItem;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogueItem>
 */
class CatalogueItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'catalogue_no' => fake()->unique()->bothify('CAT-####'),
            'row_type' => CatalogueItem::ROW_TYPE_PARENT,
            'item_type' => fake()->randomElement(['Sofa', 'Armchair', 'Dining Table', 'Bed Frame']),
            'supplier_id' => Supplier::factory(),
            'code_supplier' => fake()->bothify('SUP-####'),
            'notes_supplier' => null,
            'unit_cost' => fake()->randomFloat(2, 100, 5000),
            'meterage' => null,
            'parent_item_id' => null,
            'height_mm' => fake()->numberBetween(400, 1200),
            'width_mm' => fake()->numberBetween(400, 2000),
            'depth_mm' => fake()->numberBetween(400, 1000),
            'packaging_type' => fake()->randomElement(['Crate', 'Carton', 'Pallet']),
            'finish_id' => null,
        ];
    }

    /**
     * A Sub Item row - fabric always lives here, not on the Parent.
     */
    public function sub(): static
    {
        return $this->state(fn (array $attributes) => [
            'row_type' => CatalogueItem::ROW_TYPE_SUB,
            'meterage' => fake()->randomFloat(3, 1, 15),
        ]);
    }
}
