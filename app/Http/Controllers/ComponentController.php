<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Item;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComponentController extends Controller
{
    public function index(Item $item)
    {
        return Inertia::render('Components/Index', [
            'item' => $item,
            'components' => $item->components()->with('material')->orderBy('name')->get(),
        ]);
    }

    public function create(Item $item)
    {
        return Inertia::render('Components/Form', [
            'item' => $item,
            'component' => null,
            ...$this->options(),
        ]);
    }

    public function store(Request $request, Item $item)
    {
        $component = $item->components()->create($this->validated($request));

        return redirect()->route('items.components.edit', [$item, $component])->with('success', 'Component added.');
    }

    public function edit(Item $item, Component $component)
    {
        return Inertia::render('Components/Form', [
            'item' => $item,
            'component' => $component,
            ...$this->options(),
        ]);
    }

    public function update(Request $request, Item $item, Component $component)
    {
        $component->update($this->validated($request));

        return redirect()->route('items.components.index', $item)->with('success', 'Component updated.');
    }

    public function destroy(Item $item, Component $component)
    {
        $component->delete();

        return redirect()->route('items.components.index', $item)->with('success', 'Component deleted.');
    }

    private function options(): array
    {
        return [
            'materials' => Material::orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'code_supplier' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
            'meterage' => 'nullable|numeric|min:0',
        ]);
    }
}
