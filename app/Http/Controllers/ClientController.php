<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

public function index(Request $request): Response
    {
        if (!Auth::user()->hasPermissionTo('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        $clients = $this->clientService->all(perPage: 15, search: $search);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'title' => 'Clients Dashboard',
            'filters' => ['search' => $search]
        ]);
    }

    public function create(): Response
    {
        if (!Auth::user()->hasPermissionTo('create-clients')) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Clients/Create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('create-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $this->clientService->create($request->validated());

        return redirect()->route('clients.index')
                         ->with('success', 'Client created successfully.');
    }

    public function show(int $id): Response
    {
        if (!Auth::user()->hasPermissionTo('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $client = $this->clientService->find($id);

        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    public function edit(int $id): Response
    {
        if (!Auth::user()->hasPermissionTo('edit-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $client = $this->clientService->find($id);

        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, int $id): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('edit-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $this->clientService->update($id, $request->validated());

        return redirect()->route('clients.index')
                         ->with('success', 'Client updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('delete-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $this->clientService->delete($id);

        return redirect()->route('clients.index')
                         ->with('success', 'Client deleted successfully.');
    }

    public function addBalance(Request $request, int $id): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('add-balance')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'required|string|max:255',
        ]);

        $this->clientService->addBalance($id, $validated);

        return redirect()->route('clients.index')
                         ->with('success', 'Balance updated successfully.');
    }

    public function getByPhone(string $phone): JsonResponse
    {
    

        $client = Client::with('phones')
            ->whereHas('phones', fn($q) => $q->where('phone', $phone))
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $client->id,
                'name' => $client->first_name,
                'balance' => $client->balance,
                'phones' => $client->phones->pluck('phone')
            ]
        ]);
    }
}