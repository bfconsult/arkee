<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'clients' => Client::orderBy('company_name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Form', [
            'client' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client added.');
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Form', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $this->validated($request);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
    }
}
