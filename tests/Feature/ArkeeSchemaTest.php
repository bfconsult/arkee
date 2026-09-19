<?php

use App\Models\Attachment;
use App\Models\CatalogueItem;
use App\Models\Client;
use App\Models\DeliveryLocation;
use App\Models\Finish;
use App\Models\FurnitureScheduleLine;
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

test('catalogue items support a self-referencing parent/sub structure with fabric on the sub', function () {
    $supplier = Supplier::factory()->create();
    $finish = Finish::factory()->create(['type' => Finish::TYPE_TIMBER_STAIN]);

    $parent = CatalogueItem::factory()->for($supplier)->create();
    $sub = CatalogueItem::factory()->sub()->for($supplier)->for($finish)->create([
        'parent_item_id' => $parent->id,
    ]);

    expect($parent->subItems)->toHaveCount(1);
    expect($parent->subItems->first()->is($sub))->toBeTrue();
    expect($sub->parentItem->is($parent))->toBeTrue();
    expect($sub->row_type)->toBe(CatalogueItem::ROW_TYPE_SUB);
    expect($sub->meterage)->not->toBeNull();
    expect($sub->finish->is($finish))->toBeTrue();
});

test('a furniture schedule line links a project, catalogue item, and optional delivery location', function () {
    $project = Project::factory()->create();
    $item = CatalogueItem::factory()->create();
    $location = DeliveryLocation::factory()->create();

    $line = FurnitureScheduleLine::factory()
        ->for($project)
        ->for($item, 'catalogueItem')
        ->for($location)
        ->create();

    expect($project->furnitureScheduleLines->first()->is($line))->toBeTrue();
    expect($line->catalogueItem->is($item))->toBeTrue();
    expect($line->deliveryLocation->is($location))->toBeTrue();
});

test('furniture schedule lines self-reference for parent/sub rows the same way catalogue items do', function () {
    $project = Project::factory()->create();
    $parentLine = FurnitureScheduleLine::factory()->for($project)->create();
    $subLine = FurnitureScheduleLine::factory()->sub()->for($project)->create([
        'parent_line_id' => $parentLine->id,
    ]);

    expect($parentLine->subLines->first()->is($subLine))->toBeTrue();
    expect($subLine->parentLine->is($parentLine))->toBeTrue();
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

test('attachments attach to any entity via the polymorphic entity_type/entity_id columns', function () {
    $item = CatalogueItem::factory()->create();

    $attachment = Attachment::factory()->for($item, 'entity')->create([
        'kind' => Attachment::KIND_IMAGE,
    ]);

    expect($item->attachments)->toHaveCount(1);
    expect($item->attachments->first()->is($attachment))->toBeTrue();
    expect($attachment->entity->is($item))->toBeTrue();
});
