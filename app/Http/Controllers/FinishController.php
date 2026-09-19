<?php

namespace App\Http\Controllers;

use App\Models\Finish;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinishController extends Controller
{
    public function index()
    {
        return Inertia::render('Finishes/Index', [
            'finishes' => Finish::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Finishes/Form', [
            'finish' => null,
        ]);
    }

    public function store(Request $request)
    {
        Finish::create($this->validated($request));

        return redirect()->route('finishes.index')->with('success', 'Finish added.');
    }

    public function edit(Finish $finish)
    {
        return Inertia::render('Finishes/Form', [
            'finish' => $finish,
        ]);
    }

    public function update(Request $request, Finish $finish)
    {
        $finish->update($this->validated($request));

        return redirect()->route('finishes.index')->with('success', 'Finish updated.');
    }

    public function destroy(Finish $finish)
    {
        $finish->delete();

        return redirect()->route('finishes.index')->with('success', 'Finish deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:timber_stain,fabric,other',
        ]);
    }
}
