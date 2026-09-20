<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('ItemCategories/Index', [
            'itemCategories' => ItemCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('ItemCategories/Form', [
            'itemCategory' => null,
        ]);
    }

    public function store(Request $request)
    {
        ItemCategory::create($this->validated($request));

        return redirect()->route('item-categories.index')->with('success', 'Item category added.');
    }

    public function edit(ItemCategory $itemCategory)
    {
        return Inertia::render('ItemCategories/Form', [
            'itemCategory' => $itemCategory,
        ]);
    }

    public function update(Request $request, ItemCategory $itemCategory)
    {
        $itemCategory->update($this->validated($request));

        return redirect()->route('item-categories.index')->with('success', 'Item category updated.');
    }

    public function destroy(ItemCategory $itemCategory)
    {
        $itemCategory->delete();

        return redirect()->route('item-categories.index')->with('success', 'Item category deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
}
