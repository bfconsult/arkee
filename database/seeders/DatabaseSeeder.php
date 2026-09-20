<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Component;
use App\Models\DeliveryLocation;
use App\Models\Finish;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\ItemCategory;
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
        $itemCategories = ItemCategory::factory()->count(6)->create();

        // Materials are a shared catalogue now - build the pool (with their
        // Finishes) before any Item/Component references them.
        $materials = Material::factory()
            ->count(8)
            ->create(['supplier_id' => fn () => $suppliers->random()->id])
            ->each(function (Material $material) {
                Finish::factory()->count(rand(1, 4))->for($material)->create();
            });

        Item::factory()
            ->count(12)
            ->create([
                'item_category_id' => fn () => $itemCategories->random()->id,
                'packaging_type_id' => fn () => $packagingTypes->random()->id,
                'supplier_id' => fn () => $suppliers->random()->id,
            ])
            ->each(function (Item $item) use ($materials) {
                Component::factory()
                    ->count(rand(1, 3))
                    ->for($item)
                    ->create(['material_id' => fn () => $materials->random()->id]);
            });

        $pmUsers = User::all();

        // Projects need a PM user, and this seeder deliberately never creates
        // one - skip them until someone has registered (they'll bootstrap as
        // admin) rather than crashing or seeding a throwaway user.
        if ($pmUsers->isEmpty()) {
            $this->command?->warn('No users yet - skipping Projects/Schedule Lines/Purchase Orders. Register an account, then re-run db:seed to also get those.');

            return;
        }

        $items = Item::all();
        $finishes = Finish::all();

        Project::factory()
            ->count(5)
            ->create([
                'client_id' => fn () => $clients->random()->id,
                'pm_user_id' => fn () => $pmUsers->random()->id,
            ])
            ->each(function (Project $project) use ($items, $finishes, $suppliers) {
                FurnitureScheduleLine::factory()
                    ->count(rand(3, 6))
                    ->for($project)
                    ->create([
                        'item_id' => fn () => $items->random()->id,
                        'finish_id' => fn () => $finishes->isNotEmpty() ? $finishes->random()->id : null,
                    ]);

                PurchaseOrder::factory()
                    ->count(rand(1, 3))
                    ->for($project)
                    ->create(['supplier_id' => fn () => $suppliers->random()->id]);
            });
    }
}
