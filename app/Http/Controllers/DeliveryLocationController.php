<?php

namespace App\Http\Controllers;

use App\Models\DeliveryLocation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryLocationController extends Controller
{
    public function index()
    {
        return Inertia::render('DeliveryLocations/Index', [
            'deliveryLocations' => DeliveryLocation::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('DeliveryLocations/Form', [
            'deliveryLocation' => null,
        ]);
    }

    public function store(Request $request)
    {
        DeliveryLocation::create($this->validated($request));

        return redirect()->route('delivery-locations.index')->with('success', 'Delivery location added.');
    }

    public function edit(DeliveryLocation $deliveryLocation)
    {
        return Inertia::render('DeliveryLocations/Form', [
            'deliveryLocation' => $deliveryLocation,
        ]);
    }

    public function update(Request $request, DeliveryLocation $deliveryLocation)
    {
        $deliveryLocation->update($this->validated($request));

        return redirect()->route('delivery-locations.index')->with('success', 'Delivery location updated.');
    }

    public function destroy(DeliveryLocation $deliveryLocation)
    {
        $deliveryLocation->delete();

        return redirect()->route('delivery-locations.index')->with('success', 'Delivery location deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
    }
}
