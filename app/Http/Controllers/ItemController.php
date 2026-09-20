<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PackagingType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Items/Index', [
            'items' => Item::with('packagingType')->orderBy('catalogue_no')->get(),
        ]);
    }

    public function show(Item $item)
    {
        $item->load([
            'packagingType',
            'components.material.finishes',
            'components.material.supplier',
            'components.supplier',
            'scheduleLines.project',
            'scheduleLines.finish',
        ]);

        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Form', [
            'item' => null,
            'packagingTypes' => $this->packagingTypeOptions(),
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
            'packagingTypes' => $this->packagingTypeOptions(),
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

    private function packagingTypeOptions()
    {
        return PackagingType::orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'catalogue_no' => 'nullable|string|max:255',
            'item_type' => 'nullable|string|max:255',
            'height_mm' => 'nullable|integer|min:0',
            'width_mm' => 'nullable|integer|min:0',
            'depth_mm' => 'nullable|integer|min:0',
            'packaging_type_id' => 'nullable|exists:packaging_types,id',
            'notes' => 'nullable|string',
        ]);
    }
}
