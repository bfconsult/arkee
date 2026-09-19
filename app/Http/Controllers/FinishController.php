<?php

namespace App\Http\Controllers;

use App\Models\Finish;
use App\Models\Material;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinishController extends Controller
{
    public function index(Material $material)
    {
        return Inertia::render('Finishes/Index', [
            'material' => $material,
            'finishes' => $material->finishes()->orderBy('name')->get(),
        ]);
    }

    public function create(Material $material)
    {
        return Inertia::render('Finishes/Form', [
            'material' => $material,
            'finish' => null,
        ]);
    }

    public function store(Request $request, Material $material)
    {
        $material->finishes()->create($this->validated($request));

        return redirect()->route('materials.finishes.index', $material)->with('success', 'Finish added.');
    }

    public function edit(Material $material, Finish $finish)
    {
        return Inertia::render('Finishes/Form', [
            'material' => $material,
            'finish' => $finish,
        ]);
    }

    public function update(Request $request, Material $material, Finish $finish)
    {
        $finish->update($this->validated($request));

        return redirect()->route('materials.finishes.index', $material)->with('success', 'Finish updated.');
    }

    public function destroy(Material $material, Finish $finish)
    {
        $finish->delete();

        return redirect()->route('materials.finishes.index', $material)->with('success', 'Finish deleted.');
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
