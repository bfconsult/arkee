<?php

use App\Models\Attachment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('an item with no images has an empty attachments list', function () {
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->get(route('items.show', $item))
        ->assertInertia(fn ($page) => $page->has('item.attachments', 0));
});

test('uploading an image resizes it and attaches it to the item', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.attachments.store', $item), [
            'image' => UploadedFile::fake()->image('photo.jpg', 2000, 2000),
        ])
        ->assertRedirect(route('items.show', $item));

    $attachment = Attachment::sole();
    expect($attachment->entity_type)->toBe(Item::class);
    expect($attachment->entity_id)->toBe($item->id);
    expect($attachment->kind)->toBe(Attachment::KIND_IMAGE);
    Storage::disk('public')->assertExists($attachment->url);
    expect($attachment->file_url)->toContain($attachment->url);
});

test('an item can have several images attached', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    $item = Item::factory()->create();

    $this->actingAs($this->user)->post(route('items.attachments.store', $item), [
        'image' => UploadedFile::fake()->image('first.jpg'),
    ]);
    $this->actingAs($this->user)->post(route('items.attachments.store', $item), [
        'image' => UploadedFile::fake()->image('second.jpg'),
    ]);

    expect($item->attachments()->count())->toBe(2);
});

test('removing an item image deletes the file and the record', function () {
    Storage::fake('public');
    config(['filesystems.default' => 'public']);
    $item = Item::factory()->create();

    $this->actingAs($this->user)->post(route('items.attachments.store', $item), [
        'image' => UploadedFile::fake()->image('photo.jpg'),
    ]);
    $attachment = Attachment::sole();
    $path = $attachment->url;

    $this->actingAs($this->user)
        ->delete(route('items.attachments.destroy', [$item, $attachment]))
        ->assertRedirect(route('items.show', $item));

    Storage::disk('public')->assertMissing($path);
    expect(Attachment::count())->toBe(0);
});

test('a non-image file is rejected for an item image upload', function () {
    Storage::fake('public');
    $item = Item::factory()->create();

    $this->actingAs($this->user)
        ->post(route('items.attachments.store', $item), [
            'image' => UploadedFile::fake()->create('document.pdf', 100),
        ])
        ->assertSessionHasErrors('image');

    expect(Attachment::count())->toBe(0);
});
