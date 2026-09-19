<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'po_number' => 'PO-' . fake()->unique()->numberBetween(1000, 9999),
            'project_id' => Project::factory(),
            'supplier_id' => Supplier::factory(),
            'delivery_location_id' => null,
            'assignee_user_id' => null,
            'order_status' => PurchaseOrder::STATUS_DRAFT,
            'date_issued' => null,
        ];
    }
}
