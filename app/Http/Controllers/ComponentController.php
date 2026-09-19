<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComponentController extends Controller
{
    public function index(Item $item)
    {
        return Inertia::render('Components/Index', [
            'item' => $item,
            'components' => $item->components()->orderBy('name')->get(),
        ]);
    }

    public function create(Item $item)
    {
        return Inertia::render('Components/Form', [
            'item' => $item,
            'component' => null,
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

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);
    }
}
