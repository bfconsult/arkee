<?php

namespace App\Http\Controllers;

use App\Models\Colour;
use App\Models\Component;
use App\Models\Item;
use App\Models\Material;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColourController extends Controller
{
    public function index(Item $item, Component $component, Material $material)
    {
        return Inertia::render('Colours/Index', [
            'item' => $item,
            'component' => $component,
            'material' => $material,
            'colours' => $material->colours()->orderBy('name')->get(),
        ]);
    }

    public function create(Item $item, Component $component, Material $material)
    {
        return Inertia::render('Colours/Form', [
            'item' => $item,
            'component' => $component,
            'material' => $material,
            'colour' => null,
        ]);
    }

    public function store(Request $request, Item $item, Component $component, Material $material)
    {
        $material->colours()->create($this->validated($request));

        return redirect()
            ->route('items.components.materials.colours.index', [$item, $component, $material])
            ->with('success', 'Colour added.');
    }

    public function edit(Item $item, Component $component, Material $material, Colour $colour)
    {
        return Inertia::render('Colours/Form', [
            'item' => $item,
            'component' => $component,
            'material' => $material,
            'colour' => $colour,
        ]);
    }

    public function update(Request $request, Item $item, Component $component, Material $material, Colour $colour)
    {
        $colour->update($this->validated($request));

        return redirect()
            ->route('items.components.materials.colours.index', [$item, $component, $material])
            ->with('success', 'Colour updated.');
    }

    public function destroy(Item $item, Component $component, Material $material, Colour $colour)
    {
        $colour->delete();

        return redirect()
            ->route('items.components.materials.colours.index', [$item, $component, $material])
            ->with('success', 'Colour deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code_supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
    }
}
