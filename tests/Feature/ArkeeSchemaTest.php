<?php

use App\Models\Attachment;
use App\Models\Client;
use App\Models\Component;
use App\Models\DeliveryLocation;
use App\Models\Finish;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

test('a client has projects, each with a PM user', function () {
    $pm = User::factory()->create(['role' => User::ROLE_PM]);
    $client = Client::factory()->create();
    $project = Project::factory()->for($client)->for($pm, 'pmUser')->create();

    expect($client->projects)->toHaveCount(1);
    expect($client->projects->first()->is($project))->toBeTrue();
    expect($project->client->is($client))->toBeTrue();
    expect($project->pmUser->is($pm))->toBeTrue();
    expect($pm->projectsAsPm->first()->is($project))->toBeTrue();
});

test('a material is a shared catalogue entry that many components across many items can reuse', function () {
    $supplier = Supplier::factory()->create();
    $material = Material::factory()->for($supplier)->create();
    $finish = Finish::factory()->for($material)->create();

    $item1 = Item::factory()->create();
    $item2 = Item::factory()->create();
    $component1 = Component::factory()->for($item1)->for($material)->create();
    $component2 = Component::factory()->for($item2)->for($material)->create();

    expect($item1->components->first()->is($component1))->toBeTrue();
    expect($component1->item->is($item1))->toBeTrue();
    expect($component1->material->is($material))->toBeTrue();
    expect($component2->material->is($material))->toBeTrue();

    expect($material->components)->toHaveCount(2);
    expect($material->supplier->is($supplier))->toBeTrue();
    expect($material->finishes)->toHaveCount(1);
    expect($finish->material->is($material))->toBeTrue();
});

test('a component can carry its own supplier/cost independently of its material', function () {
    $material = Material::factory()->create(['supplier_id' => null]);
    $supplier = Supplier::factory()->create();

    $component = Component::factory()->for($material)->create([
        'supplier_id' => $supplier->id,
        'unit_cost' => 123.45,
    ]);

    expect($component->supplier->is($supplier))->toBeTrue();
    expect((float) $component->unit_cost)->toBe(123.45);
});

test('a furniture schedule line links a project, item, and optional delivery location', function () {
    $project = Project::factory()->create();
    $item = Item::factory()->create();
    $location = DeliveryLocation::factory()->create();

    $line = FurnitureScheduleLine::factory()
        ->for($project)
        ->for($item)
        ->for($location)
        ->create();

    expect($project->furnitureScheduleLines->first()->is($line))->toBeTrue();
    expect($line->item->is($item))->toBeTrue();
    expect($line->deliveryLocation->is($location))->toBeTrue();
});

test('furniture schedule lines self-reference for parent/sub rows, and can pick a finish', function () {
    $project = Project::factory()->create();
    $material = Material::factory()->create();
    $finish = Finish::factory()->for($material)->create();

    $parentLine = FurnitureScheduleLine::factory()->for($project)->create();
    $subLine = FurnitureScheduleLine::factory()->sub()->for($project)->for($finish)->create([
        'parent_line_id' => $parentLine->id,
    ]);

    expect($parentLine->subLines->first()->is($subLine))->toBeTrue();
    expect($subLine->parentLine->is($parentLine))->toBeTrue();
    expect($subLine->finish->is($finish))->toBeTrue();
});

test('a purchase order belongs to a project and supplier, and can include multiple schedule lines', function () {
    $project = Project::factory()->create();
    $supplier = Supplier::factory()->create();
    $line1 = FurnitureScheduleLine::factory()->for($project)->create();
    $line2 = FurnitureScheduleLine::factory()->for($project)->create();

    $po = PurchaseOrder::factory()->for($project)->for($supplier)->create();
    $po->scheduleLines()->attach([$line1->id, $line2->id]);

    expect($po->project->is($project))->toBeTrue();
    expect($po->supplier->is($supplier))->toBeTrue();
    expect($po->scheduleLines)->toHaveCount(2);
    expect($line1->purchaseOrders->first()->is($po))->toBeTrue();
});

test('attachments attach to an item or a finish via the polymorphic entity_type/entity_id columns', function () {
    $item = Item::factory()->create();
    $itemAttachment = Attachment::factory()->for($item, 'entity')->create([
        'kind' => Attachment::KIND_IMAGE,
    ]);

    expect($item->attachments)->toHaveCount(1);
    expect($item->attachments->first()->is($itemAttachment))->toBeTrue();
    expect($itemAttachment->entity->is($item))->toBeTrue();

    $finish = Finish::factory()->create();
    $finishAttachment = Attachment::factory()->for($finish, 'entity')->create([
        'kind' => Attachment::KIND_FABRIC_STAIN,
    ]);

    expect($finish->attachments->first()->is($finishAttachment))->toBeTrue();
});
