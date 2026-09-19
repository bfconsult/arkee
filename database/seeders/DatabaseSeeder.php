<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Colour;
use App\Models\Component;
use App\Models\DeliveryLocation;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Material;
use App\Models\PackagingType;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed sample business data for manual testing. Deliberately does not
     * touch the users table - the first real registration is meant to
     * bootstrap itself as the admin (see RegisteredUserController).
     */
    public function run(): void
    {
        $clients = Client::factory()->count(6)->create();
        $suppliers = Supplier::factory()->count(8)->create();
        DeliveryLocation::factory()->count(4)->create();
        $packagingTypes = PackagingType::factory()->count(5)->create();

        Item::factory()
            ->count(12)
            ->create(['packaging_type_id' => fn () => $packagingTypes->random()->id])
            ->each(function (Item $item) use ($suppliers) {
                Component::factory()
                    ->count(rand(1, 3))
                    ->for($item)
                    ->create()
                    ->each(function (Component $component) use ($suppliers) {
                        Material::factory()
                            ->count(rand(1, 2))
                            ->for($component)
                            ->create(['supplier_id' => fn () => $suppliers->random()->id])
                            ->each(function (Material $material) {
                                Colour::factory()->count(rand(1, 3))->for($material)->create();
                            });
                    });
            });

        $items = Item::all();
        $colours = Colour::all();
        $pmUsers = User::all();

        Project::factory()
            ->count(5)
            ->create([
                'client_id' => fn () => $clients->random()->id,
                'pm_user_id' => fn () => $pmUsers->random()->id,
            ])
            ->each(function (Project $project) use ($items, $colours, $suppliers) {
                FurnitureScheduleLine::factory()
                    ->count(rand(3, 6))
                    ->for($project)
                    ->create([
                        'item_id' => fn () => $items->random()->id,
                        'colour_id' => fn () => $colours->isNotEmpty() ? $colours->random()->id : null,
                    ]);

                PurchaseOrder::factory()
                    ->count(rand(1, 3))
                    ->for($project)
                    ->create(['supplier_id' => fn () => $suppliers->random()->id]);
            });
    }
}
