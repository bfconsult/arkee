<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class AttachmentController extends Controller
{
    /**
     * Catalogue images are viewed larger than an avatar (product photos,
     * not a small header icon), so allow more pixels before downscaling.
     */
    private const MAX_DIMENSION = 1600;

    private const JPEG_QUALITY = 82;

    public function store(Request $request, Item $item): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $image = (new ImageManager(new Driver()))
            ->decode($request->file('image')->getRealPath())
            ->scaleDown(width: self::MAX_DIMENSION, height: self::MAX_DIMENSION);

        $encoded = $image->encode(new JpegEncoder(quality: self::JPEG_QUALITY));

        $path = 'item-images/'.Str::uuid().'.jpg';

        Storage::disk(config('filesystems.default'))->put($path, (string) $encoded);

        $item->attachments()->create([
            'kind' => Attachment::KIND_IMAGE,
            'url' => $path,
        ]);

        return redirect()->route('items.show', $item)->with('success', 'Image uploaded.');
    }

    public function destroy(Item $item, Attachment $attachment): RedirectResponse
    {
        Storage::disk(config('filesystems.default'))->delete($attachment->url);
        $attachment->delete();

        return redirect()->route('items.show', $item)->with('success', 'Image removed.');
    }
}
