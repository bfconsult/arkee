<?php

use App\Models\Attachment;
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

test('an item breaks down into components, materials, and colours', function () {
    $supplier = Supplier::factory()->create();

    $item = Item::factory()->create();
    $component = Component::factory()->for($item)->create();
    $material = Material::factory()->for($component)->for($supplier)->create();
    $colour = Colour::factory()->for($material)->create();

    expect($item->components)->toHaveCount(1);
    expect($item->components->first()->is($component))->toBeTrue();
    expect($component->item->is($item))->toBeTrue();

    expect($component->materials)->toHaveCount(1);
    expect($material->component->is($component))->toBeTrue();
    expect($material->supplier->is($supplier))->toBeTrue();

    expect($material->colours)->toHaveCount(1);
    expect($colour->material->is($material))->toBeTrue();
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

test('furniture schedule lines self-reference for parent/sub rows, and can pick a colour', function () {
    $project = Project::factory()->create();
    $material = Material::factory()->create();
    $colour = Colour::factory()->for($material)->create();

    $parentLine = FurnitureScheduleLine::factory()->for($project)->create();
    $subLine = FurnitureScheduleLine::factory()->sub()->for($project)->for($colour)->create([
        'parent_line_id' => $parentLine->id,
    ]);

    expect($parentLine->subLines->first()->is($subLine))->toBeTrue();
    expect($subLine->parentLine->is($parentLine))->toBeTrue();
    expect($subLine->colour->is($colour))->toBeTrue();
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

test('attachments attach to an item or a colour via the polymorphic entity_type/entity_id columns', function () {
    $item = Item::factory()->create();
    $itemAttachment = Attachment::factory()->for($item, 'entity')->create([
        'kind' => Attachment::KIND_IMAGE,
    ]);

    expect($item->attachments)->toHaveCount(1);
    expect($item->attachments->first()->is($itemAttachment))->toBeTrue();
    expect($itemAttachment->entity->is($item))->toBeTrue();

    $colour = Colour::factory()->create();
    $colourAttachment = Attachment::factory()->for($colour, 'entity')->create([
        'kind' => Attachment::KIND_FABRIC_STAIN,
    ]);

    expect($colour->attachments->first()->is($colourAttachment))->toBeTrue();
});
