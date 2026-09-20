<?php

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

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('a client can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('clients.store'), ['company_name' => 'Acme Interiors'])
        ->assertRedirect(route('clients.index'));

    $client = Client::sole();
    expect($client->company_name)->toBe('Acme Interiors');

    $this->actingAs($this->user)
        ->put(route('clients.update', $client), ['company_name' => 'Acme Interiors Pty Ltd'])
        ->assertRedirect(route('clients.index'));
    expect($client->fresh()->company_name)->toBe('Acme Interiors Pty Ltd');

    $this->actingAs($this->user)
        ->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.index'));
    expect(Client::count())->toBe(0);
});

test('a supplier can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('suppliers.store'), ['name' => 'Timber Co'])
        ->assertRedirect(route('suppliers.index'));

    $supplier = Supplier::sole();

    $this->actingAs($this->user)
        ->put(route('suppliers.update', $supplier), ['name' => 'Timber Co Pty Ltd'])
        ->assertRedirect(route('suppliers.index'));
    expect($supplier->fresh()->name)->toBe('Timber Co Pty Ltd');

    $this->actingAs($this->user)
        ->delete(route('suppliers.destroy', $supplier))
        ->assertRedirect(route('suppliers.index'));
    expect(Supplier::count())->toBe(0);
});


test('a delivery location can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('delivery-locations.store'), ['name' => 'Head Office'])
        ->assertRedirect(route('delivery-locations.index'));

    $location = DeliveryLocation::sole();

    $this->actingAs($this->user)
        ->put(route('delivery-locations.update', $location), ['name' => 'Warehouse'])
        ->assertRedirect(route('delivery-locations.index'));
    expect($location->fresh()->name)->toBe('Warehouse');

    $this->actingAs($this->user)
        ->delete(route('delivery-locations.destroy', $location))
        ->assertRedirect(route('delivery-locations.index'));
    expect(DeliveryLocation::count())->toBe(0);
});

test('a packaging type can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('packaging-types.store'), ['name' => 'Crate'])
        ->assertRedirect(route('packaging-types.index'));

    $packagingType = PackagingType::sole();

    $this->actingAs($this->user)
        ->put(route('packaging-types.update', $packagingType), ['name' => 'Carton'])
        ->assertRedirect(route('packaging-types.index'));
    expect($packagingType->fresh()->name)->toBe('Carton');

    $this->actingAs($this->user)
        ->delete(route('packaging-types.destroy', $packagingType))
        ->assertRedirect(route('packaging-types.index'));
    expect(PackagingType::count())->toBe(0);
});

test('an item category can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('item-categories.store'), ['name' => 'Sofa'])
        ->assertRedirect(route('item-categories.index'));

    $itemCategory = ItemCategory::sole();

    $this->actingAs($this->user)
        ->put(route('item-categories.update', $itemCategory), ['name' => 'Armchair'])
        ->assertRedirect(route('item-categories.index'));
    expect($itemCategory->fresh()->name)->toBe('Armchair');

    $this->actingAs($this->user)
        ->delete(route('item-categories.destroy', $itemCategory))
        ->assertRedirect(route('item-categories.index'));
    expect(ItemCategory::count())->toBe(0);
});

test('an item can be created, updated, and deleted', function () {
    $supplier = Supplier::factory()->create();
    $sofaCategory = ItemCategory::factory()->create(['name' => 'Sofa']);
    $armchairCategory = ItemCategory::factory()->create(['name' => 'Armchair']);

    $this->actingAs($this->user)
        ->post(route('items.store'), [
            'catalogue_no' => 'CAT-001',
            'item_category_id' => $sofaCategory->id,
            'supplier_id' => $supplier->id,
        ])
        ->assertRedirect();

    $item = Item::sole();
    expect($item->catalogue_no)->toBe('CAT-001');
    expect($item->supplier_id)->toBe($supplier->id);

    $this->actingAs($this->user)
        ->put(route('items.update', $item), ['catalogue_no' => 'CAT-001', 'item_category_id' => $armchairCategory->id])
        ->assertRedirect(route('items.index'));
    expect($item->fresh()->item_category_id)->toBe($armchairCategory->id);

    $this->actingAs($this->user)
        ->delete(route('items.destroy', $item))
        ->assertRedirect(route('items.index'));
    expect(Item::count())->toBe(0);
});

test("an item's show page brings together its components, materials, finishes, and schedule line usage", function () {
    $item = Item::factory()->create();
    $material = Material::factory()->create();
    $finish = Finish::factory()->for($material)->create();
    Component::factory()->for($item)->for($material)->create(['name' => 'Frame']);

    $project = Project::factory()->create();
    FurnitureScheduleLine::factory()->for($project)->for($item)->for($finish)->create();

    $this->actingAs($this->user)
        ->get(route('items.show', $item))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Items/Show')
            ->where('item.id', $item->id)
            ->where('item.components.0.name', 'Frame')
            ->where('item.components.0.material.id', $material->id)
            ->where('item.components.0.material.finishes.0.id', $finish->id)
            ->where('item.schedule_lines.0.project.id', $project->id)
        );
});

test("a material's show page lists its finishes and where it's used", function () {
    $material = Material::factory()->create();
    $finish = Finish::factory()->for($material)->create();
    $item = Item::factory()->create();
    $component = Component::factory()->for($item)->for($material)->create(['name' => 'Frame']);

    $this->actingAs($this->user)
        ->get(route('materials.show', $material))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Materials/Show')
            ->where('material.id', $material->id)
            ->where('material.finishes.0.id', $finish->id)
            ->where('material.components.0.id', $component->id)
            ->where('material.components.0.item.id', $item->id)
        );
});

test("an item's supplier is a default that a component's own supplier overrides", function () {
    $itemSupplier = Supplier::factory()->create();
    $componentSupplier = Supplier::factory()->create();
    $item = Item::factory()->create(['supplier_id' => $itemSupplier->id]);
    $material = Material::factory()->create(['supplier_id' => null]);

    $defaultingComponent = Component::factory()->for($item)->for($material)->create(['supplier_id' => null]);
    $overridingComponent = Component::factory()->for($item)->for($material)->create(['supplier_id' => $componentSupplier->id]);

    expect($item->supplier->is($itemSupplier))->toBeTrue();
    expect($defaultingComponent->supplier_id)->toBeNull();
    expect($overridingComponent->supplier->is($componentSupplier))->toBeTrue();
});

test('a material can be created, updated, and deleted (a shared, top-level catalogue entry)', function () {
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->user)
        ->post(route('materials.store'), [
            'name' => 'Linen Fabric',
            'supplier_id' => $supplier->id,
        ])
        ->assertRedirect();

    $material = Material::sole();
    expect($material->supplier_id)->toBe($supplier->id);

    $this->actingAs($this->user)
        ->put(route('materials.update', $material), [
            'name' => 'Linen Fabric',
            'supplier_id' => $supplier->id,
            'unit_cost' => 42.50,
        ])
        ->assertRedirect(route('materials.index'));
    expect((float) $material->fresh()->unit_cost)->toBe(42.50);

    $this->actingAs($this->user)
        ->delete(route('materials.destroy', $material))
        ->assertRedirect(route('materials.index'));
    expect(Material::count())->toBe(0);
});

test('a material can be created with no supplier at all - purchasing may live on the component instead', function () {
    $this->actingAs($this->user)
        ->post(route('materials.store'), ['name' => 'Oak Timber'])
        ->assertRedirect();

    expect(Material::sole()->supplier_id)->toBeNull();
});

test('a finish can be created, updated, and deleted within a material', function () {
    $material = Material::factory()->create();

    $this->actingAs($this->user)
        ->post(route('materials.finishes.store', $material), ['name' => 'Natural Oak'])
        ->assertRedirect();

    $finish = Finish::sole();
    expect($finish->material_id)->toBe($material->id);

    $this->actingAs($this->user)
        ->put(route('materials.finishes.update', [$material, $finish]), [
            'name' => 'Natural Oak',
            'code_supplier' => 'COL-9',
        ])
        ->assertRedirect(route('materials.finishes.index', $material));
    expect($finish->fresh()->code_supplier)->toBe('COL-9');

    $this->actingAs($this->user)
        ->delete(route('materials.finishes.destroy', [$material, $finish]))
        ->assertRedirect(route('materials.finishes.index', $material));
    expect(Finish::count())->toBe(0);
});

test('a component can be created, updated, and deleted within an item, and must reference a material', function () {
    $item = Item::factory()->create();
    $material = Material::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.components.store', $item), [
            'material_id' => $material->id,
            'name' => 'Frame',
            'quantity' => 1,
        ])
        ->assertRedirect();

    $component = Component::sole();
    expect($component->item_id)->toBe($item->id);
    expect($component->material_id)->toBe($material->id);

    $this->actingAs($this->user)
        ->put(route('items.components.update', [$item, $component]), [
            'material_id' => $material->id,
            'name' => 'Frame',
            'quantity' => 2,
        ])
        ->assertRedirect(route('items.components.index', $item));
    expect($component->fresh()->quantity)->toBe(2);

    $this->actingAs($this->user)
        ->delete(route('items.components.destroy', [$item, $component]))
        ->assertRedirect(route('items.components.index', $item));
    expect(Component::count())->toBe(0);
});

test('a component can be given its own supplier/cost independently of its material', function () {
    $item = Item::factory()->create();
    $material = Material::factory()->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.components.store', $item), [
            'material_id' => $material->id,
            'name' => 'Frame',
            'supplier_id' => $supplier->id,
            'unit_cost' => 88,
        ])
        ->assertRedirect();

    $component = Component::sole();
    expect($component->supplier_id)->toBe($supplier->id);
    expect((float) $component->unit_cost)->toBe(88.0);
});

test('a project can be created, updated, and deleted', function () {
    $client = Client::factory()->create();
    $pm = User::factory()->create();

    $this->actingAs($this->user)
        ->post(route('projects.store'), [
            'client_id' => $client->id,
            'pm_user_id' => $pm->id,
            'project_descriptor' => 'Riverside Fitout',
        ])
        ->assertRedirect();

    $project = Project::sole();
    expect($project->project_descriptor)->toBe('Riverside Fitout');

    $this->actingAs($this->user)
        ->put(route('projects.update', $project), [
            'client_id' => $client->id,
            'pm_user_id' => $pm->id,
            'project_descriptor' => 'Riverside Fitout v2',
        ])
        ->assertRedirect(route('projects.index'));
    expect($project->fresh()->project_descriptor)->toBe('Riverside Fitout v2');

    $this->actingAs($this->user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'));
    expect(Project::count())->toBe(0);
});

test('a furniture schedule line can be created, updated, and deleted within a project', function () {
    $project = Project::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->post(route('projects.schedule-lines.store', $project), [
            'item_id' => $item->id,
            'row_type' => 'parent',
            'quantity' => 4,
        ])
        ->assertRedirect(route('projects.schedule-lines.index', $project));

    $line = FurnitureScheduleLine::sole();
    expect($line->project_id)->toBe($project->id);
    expect($line->quantity)->toBe(4);

    $this->actingAs($this->user)
        ->put(route('projects.schedule-lines.update', [$project, $line]), [
            'item_id' => $item->id,
            'row_type' => 'parent',
            'quantity' => 6,
        ])
        ->assertRedirect(route('projects.schedule-lines.index', $project));
    expect($line->fresh()->quantity)->toBe(6);

    $this->actingAs($this->user)
        ->delete(route('projects.schedule-lines.destroy', [$project, $line]))
        ->assertRedirect(route('projects.schedule-lines.index', $project));
    expect(FurnitureScheduleLine::count())->toBe(0);
});

test('a schedule line with no quantity given falls back to the column default instead of erroring', function () {
    $project = Project::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->post(route('projects.schedule-lines.store', $project), [
            'item_id' => $item->id,
            'row_type' => 'parent',
        ])
        ->assertRedirect(route('projects.schedule-lines.index', $project));

    expect(FurnitureScheduleLine::sole()->quantity)->toBe(1);
});

test('a purchase order can be created, updated, and deleted within a project', function () {
    $project = Project::factory()->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->user)
        ->post(route('projects.purchase-orders.store', $project), [
            'supplier_id' => $supplier->id,
            'order_status' => 'draft',
            'po_number' => 'PO-100',
        ])
        ->assertRedirect(route('projects.purchase-orders.index', $project));

    $po = PurchaseOrder::sole();
    expect($po->project_id)->toBe($project->id);
    expect($po->order_status)->toBe('draft');

    $this->actingAs($this->user)
        ->put(route('projects.purchase-orders.update', [$project, $po]), [
            'supplier_id' => $supplier->id,
            'order_status' => 'sent',
            'po_number' => 'PO-100',
        ])
        ->assertRedirect(route('projects.purchase-orders.index', $project));
    expect($po->fresh()->order_status)->toBe('sent');

    $this->actingAs($this->user)
        ->delete(route('projects.purchase-orders.destroy', [$project, $po]))
        ->assertRedirect(route('projects.purchase-orders.index', $project));
    expect(PurchaseOrder::count())->toBe(0);
});
