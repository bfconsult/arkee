<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PackagingType;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Items/Index', [
            'items' => Item::with(['packagingType', 'supplier'])->orderBy('catalogue_no')->get(),
        ]);
    }

    public function show(Item $item)
    {
        $item->load([
            'packagingType',
            'supplier',
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
            ...$this->options(),
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
            ...$this->options(),
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

    private function options(): array
    {
        return [
            'packagingTypes' => PackagingType::orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ];
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);
    }
}
