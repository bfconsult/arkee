<?php

namespace App\Http\Controllers;

use App\Models\CatalogueItem;
use App\Models\Finish;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogueItemController extends Controller
{
    public function index()
    {
        return Inertia::render('CatalogueItems/Index', [
            'items' => CatalogueItem::with(['supplier', 'parentItem'])->orderBy('catalogue_no')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('CatalogueItems/Form', [
            'item' => null,
            ...$this->options(),
        ]);
    }

    public function store(Request $request)
    {
        CatalogueItem::create($this->validated($request));

        return redirect()->route('catalogue-items.index')->with('success', 'Catalogue item added.');
    }

    public function edit(CatalogueItem $catalogueItem)
    {
        return Inertia::render('CatalogueItems/Form', [
            'item' => $catalogueItem,
            ...$this->options($catalogueItem),
        ]);
    }

    public function update(Request $request, CatalogueItem $catalogueItem)
    {
        $catalogueItem->update($this->validated($request));

        return redirect()->route('catalogue-items.index')->with('success', 'Catalogue item updated.');
    }

    public function destroy(CatalogueItem $catalogueItem)
    {
        $catalogueItem->delete();

        return redirect()->route('catalogue-items.index')->with('success', 'Catalogue item deleted.');
    }

    /**
     * Dropdown data for the form - parent options exclude the item being
     * edited itself (an item can't be its own parent).
     */
    private function options(?CatalogueItem $editing = null): array
    {
        return [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'finishes' => Finish::orderBy('name')->get(['id', 'name']),
            'parentOptions' => CatalogueItem::where('row_type', CatalogueItem::ROW_TYPE_PARENT)
                ->when($editing, fn ($q) => $q->whereKeyNot($editing->id))
                ->orderBy('catalogue_no')
                ->get(['id', 'catalogue_no', 'item_type']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'catalogue_no' => 'nullable|string|max:255',
            'row_type' => 'required|in:parent,sub',
            'item_type' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'code_supplier' => 'nullable|string|max:255',
            'notes_supplier' => 'nullable|string',
            'unit_cost' => 'nullable|numeric|min:0',
            'meterage' => 'nullable|numeric|min:0',
            'parent_item_id' => 'nullable|exists:items_catalog,id',
            'height_mm' => 'nullable|integer|min:0',
            'width_mm' => 'nullable|integer|min:0',
            'depth_mm' => 'nullable|integer|min:0',
            'packaging_type' => 'nullable|string|max:255',
            'finish_id' => 'nullable|exists:finishes,id',
        ]);
    }
}
