<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Item;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterialController extends Controller
{
    public function index(Item $item, Component $component)
    {
        return Inertia::render('Materials/Index', [
            'item' => $item,
            'component' => $component,
            'materials' => $component->materials()->with('supplier')->orderBy('name')->get(),
        ]);
    }

    public function create(Item $item, Component $component)
    {
        return Inertia::render('Materials/Form', [
            'item' => $item,
            'component' => $component,
            'material' => null,
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function store(Request $request, Item $item, Component $component)
    {
        $material = $component->materials()->create($this->validated($request));

        return redirect()
            ->route('items.components.materials.edit', [$item, $component, $material])
            ->with('success', 'Material added.');
    }

    public function edit(Item $item, Component $component, Material $material)
    {
        return Inertia::render('Materials/Form', [
            'item' => $item,
            'component' => $component,
            'material' => $material,
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function update(Request $request, Item $item, Component $component, Material $material)
    {
        $material->update($this->validated($request));

        return redirect()->route('items.components.materials.index', [$item, $component])->with('success', 'Material updated.');
    }

    public function destroy(Item $item, Component $component, Material $material)
    {
        $material->delete();

        return redirect()->route('items.components.materials.index', [$item, $component])->with('success', 'Material deleted.');
    }

    private function supplierOptions()
    {
        return Supplier::orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'code_supplier' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
            'meterage' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
    }
}
