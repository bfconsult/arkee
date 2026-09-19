<?php

namespace App\Http\Controllers;

use App\Models\PackagingType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackagingTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('PackagingTypes/Index', [
            'packagingTypes' => PackagingType::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('PackagingTypes/Form', [
            'packagingType' => null,
        ]);
    }

    public function store(Request $request)
    {
        PackagingType::create($this->validated($request));

        return redirect()->route('packaging-types.index')->with('success', 'Packaging type added.');
    }

    public function edit(PackagingType $packagingType)
    {
        return Inertia::render('PackagingTypes/Form', [
            'packagingType' => $packagingType,
        ]);
    }

    public function update(Request $request, PackagingType $packagingType)
    {
        $packagingType->update($this->validated($request));

        return redirect()->route('packaging-types.index')->with('success', 'Packaging type updated.');
    }

    public function destroy(PackagingType $packagingType)
    {
        $packagingType->delete();

        return redirect()->route('packaging-types.index')->with('success', 'Packaging type deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
}
