<?php

use App\Models\Client;
use App\Models\Colour;
use App\Models\Component;
use App\Models\DeliveryLocation;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Material;
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

test('an item can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('items.store'), ['catalogue_no' => 'CAT-001', 'item_type' => 'Sofa'])
        ->assertRedirect();

    $item = Item::sole();
    expect($item->catalogue_no)->toBe('CAT-001');

    $this->actingAs($this->user)
        ->put(route('items.update', $item), ['catalogue_no' => 'CAT-001', 'item_type' => 'Armchair'])
        ->assertRedirect(route('items.index'));
    expect($item->fresh()->item_type)->toBe('Armchair');

    $this->actingAs($this->user)
        ->delete(route('items.destroy', $item))
        ->assertRedirect(route('items.index'));
    expect(Item::count())->toBe(0);
});

test('a component can be created, updated, and deleted within an item', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.components.store', $item), ['name' => 'Frame', 'quantity' => 1])
        ->assertRedirect();

    $component = Component::sole();
    expect($component->item_id)->toBe($item->id);

    $this->actingAs($this->user)
        ->put(route('items.components.update', [$item, $component]), ['name' => 'Frame', 'quantity' => 2])
        ->assertRedirect(route('items.components.index', $item));
    expect($component->fresh()->quantity)->toBe(2);

    $this->actingAs($this->user)
        ->delete(route('items.components.destroy', [$item, $component]))
        ->assertRedirect(route('items.components.index', $item));
    expect(Component::count())->toBe(0);
});

test('a material can be created, updated, and deleted within a component', function () {
    $item = Item::factory()->create();
    $component = Component::factory()->for($item)->create();
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.components.materials.store', [$item, $component]), [
            'name' => 'Linen Fabric',
            'supplier_id' => $supplier->id,
        ])
        ->assertRedirect();

    $material = Material::sole();
    expect($material->component_id)->toBe($component->id);

    $this->actingAs($this->user)
        ->put(route('items.components.materials.update', [$item, $component, $material]), [
            'name' => 'Linen Fabric',
            'supplier_id' => $supplier->id,
            'unit_cost' => 42.50,
        ])
        ->assertRedirect(route('items.components.materials.index', [$item, $component]));
    expect((float) $material->fresh()->unit_cost)->toBe(42.50);

    $this->actingAs($this->user)
        ->delete(route('items.components.materials.destroy', [$item, $component, $material]))
        ->assertRedirect(route('items.components.materials.index', [$item, $component]));
    expect(Material::count())->toBe(0);
});

test('a colour can be created, updated, and deleted within a material', function () {
    $item = Item::factory()->create();
    $component = Component::factory()->for($item)->create();
    $material = Material::factory()->for($component)->create();

    $this->actingAs($this->user)
        ->post(route('items.components.materials.colours.store', [$item, $component, $material]), [
            'name' => 'Natural Oak',
        ])
        ->assertRedirect();

    $colour = Colour::sole();
    expect($colour->material_id)->toBe($material->id);

    $this->actingAs($this->user)
        ->put(route('items.components.materials.colours.update', [$item, $component, $material, $colour]), [
            'name' => 'Natural Oak',
            'code_supplier' => 'COL-9',
        ])
        ->assertRedirect(route('items.components.materials.colours.index', [$item, $component, $material]));
    expect($colour->fresh()->code_supplier)->toBe('COL-9');

    $this->actingAs($this->user)
        ->delete(route('items.components.materials.colours.destroy', [$item, $component, $material, $colour]))
        ->assertRedirect(route('items.components.materials.colours.index', [$item, $component, $material]));
    expect(Colour::count())->toBe(0);
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
