<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterialController extends Controller
{
    public function index()
    {
        return Inertia::render('Materials/Index', [
            'materials' => Material::with('supplier')->orderBy('name')->get(),
        ]);
    }

    public function show(Material $material)
    {
        $material->load([
            'supplier',
            'finishes',
            'components.item',
        ]);

        return Inertia::render('Materials/Show', [
            'material' => $material,
        ]);
    }

    public function create()
    {
        return Inertia::render('Materials/Form', [
            'material' => null,
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $material = Material::create($this->validated($request));

        return redirect()->route('materials.edit', $material)->with('success', 'Material added.');
    }

    public function edit(Material $material)
    {
        return Inertia::render('Materials/Form', [
            'material' => $material,
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function update(Request $request, Material $material)
    {
        $material->update($this->validated($request));

        return redirect()->route('materials.index')->with('success', 'Material updated.');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material deleted.');
    }

    private function supplierOptions()
    {
        return Supplier::orderBy('name')->get(['id', 'name']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'is_fabric' => 'boolean',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'code_supplier' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
    }
}
