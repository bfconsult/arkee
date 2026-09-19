<?php

use App\Models\CatalogueItem;
use App\Models\Client;
use App\Models\DeliveryLocation;
use App\Models\Finish;
use App\Models\FurnitureScheduleLine;
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

test('a finish can be created, updated, and deleted', function () {
    $this->actingAs($this->user)
        ->post(route('finishes.store'), ['name' => 'Walnut Stain', 'type' => 'timber_stain'])
        ->assertRedirect(route('finishes.index'));

    $finish = Finish::sole();

    $this->actingAs($this->user)
        ->put(route('finishes.update', $finish), ['name' => 'Walnut Stain', 'type' => 'fabric'])
        ->assertRedirect(route('finishes.index'));
    expect($finish->fresh()->type)->toBe('fabric');

    $this->actingAs($this->user)
        ->delete(route('finishes.destroy', $finish))
        ->assertRedirect(route('finishes.index'));
    expect(Finish::count())->toBe(0);
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

test('a catalogue item can be created as a parent, updated to reference a sub, and deleted', function () {
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->user)
        ->post(route('catalogue-items.store'), [
            'catalogue_no' => 'CI-001',
            'row_type' => 'parent',
            'supplier_id' => $supplier->id,
        ])
        ->assertRedirect(route('catalogue-items.index'));

    $parent = CatalogueItem::sole();
    expect($parent->row_type)->toBe('parent');

    $sub = CatalogueItem::factory()->for($supplier)->create(['row_type' => 'sub', 'catalogue_no' => 'CI-002']);

    $this->actingAs($this->user)
        ->put(route('catalogue-items.update', $sub), [
            'catalogue_no' => 'CI-002',
            'row_type' => 'sub',
            'supplier_id' => $supplier->id,
            'parent_item_id' => $parent->id,
        ])
        ->assertRedirect(route('catalogue-items.index'));
    expect($sub->fresh()->parent_item_id)->toBe($parent->id);

    $this->actingAs($this->user)
        ->delete(route('catalogue-items.destroy', $sub))
        ->assertRedirect(route('catalogue-items.index'));
    expect(CatalogueItem::count())->toBe(1);
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
    $item = CatalogueItem::factory()->create();

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
