<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Items/Index', [
            'items' => Item::orderBy('catalogue_no')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Form', [
            'item' => null,
        ]);
    }

    public function store(Request $request)
    {
        $item = Item::create($this->validated($request));

        return redirect()->route('items.edit', $item)->with('success', 'Item added.');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Items/Form', [
            'item' => $item,
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $item->update($this->validated($request));

        return redirect()->route('items.index')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'catalogue_no' => 'nullable|string|max:255',
            'item_type' => 'nullable|string|max:255',
            'height_mm' => 'nullable|integer|min:0',
            'width_mm' => 'nullable|integer|min:0',
            'depth_mm' => 'nullable|integer|min:0',
            'packaging_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
    }
}
