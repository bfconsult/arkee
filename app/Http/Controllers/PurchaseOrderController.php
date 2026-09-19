<?php

namespace App\Http\Controllers;

use App\Models\DeliveryLocation;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function index(Project $project)
    {
        return Inertia::render('PurchaseOrders/Index', [
            'project' => $project,
            'purchaseOrders' => $project->purchaseOrders()->with(['supplier', 'assignee'])->get(),
        ]);
    }

    public function create(Project $project)
    {
        return Inertia::render('PurchaseOrders/Form', [
            'project' => $project,
            'purchaseOrder' => null,
            ...$this->options(),
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $project->purchaseOrders()->create($this->validated($request));

        return redirect()->route('projects.purchase-orders.index', $project)->with('success', 'Purchase order added.');
    }

    public function edit(Project $project, PurchaseOrder $purchaseOrder)
    {
        return Inertia::render('PurchaseOrders/Form', [
            'project' => $project,
            'purchaseOrder' => $purchaseOrder,
            ...$this->options(),
        ]);
    }

    public function update(Request $request, Project $project, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update($this->validated($request));

        return redirect()->route('projects.purchase-orders.index', $project)->with('success', 'Purchase order updated.');
    }

    public function destroy(Project $project, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('projects.purchase-orders.index', $project)->with('success', 'Purchase order deleted.');
    }

    private function options(): array
    {
        return [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'deliveryLocations' => DeliveryLocation::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'po_number' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_location_id' => 'nullable|exists:delivery_locations,id',
            'assignee_user_id' => 'nullable|exists:users,id',
            'order_status' => 'required|in:draft,sent,confirmed,received',
            'date_issued' => 'nullable|date',
        ]);
    }
}
