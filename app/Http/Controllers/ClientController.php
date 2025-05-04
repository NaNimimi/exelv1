<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\PaymentType;
use Inertia\Inertia;
use Inertia\Response;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;
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
        $search = $request->input('search');
        $clients = $this->clientService->all(perPage: 15, search: $search);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'title' => 'Clients Dashboard',
            'filters' => ['search' => $search],
            'paymentTypes' => $this->clientService->getPaymentTypes() // Add paymentTypes
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

    public function show( $id, Request $request): Response
    {
        if (!Auth::user()->hasPermissionTo('view-clients')) {
            abort(403, 'Unauthorized action.');
        }
        $id = (int) $id;
    
        $client = $this->clientService->find($id);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $balanceMovements = $this->clientService->getBalanceMovements($id, 10, $startDate, $endDate);
    
        return Inertia::render('Clients/Show', [
            'client' => $client,
            'balanceMovements' => $balanceMovements,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
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
            'payment_type_id' => 'required|exists:payment_types,id',
            'operation' => 'required|in:add,subtract',
        ]);

        $this->clientService->addBalance($id, $validated);

        return redirect()->route('clients.index')
                         ->with('success', 'Balance updated successfully.');
    }

    public function getByPhone(string $phone): JsonResponse
    {
        $client = Client::with('phones')
            ->whereHas('phones', function ($query) use ($phone) {
                $query->where('phone', '=', $phone); 
            })
            ->firstOrFail();
    
        return response()->json([
            'ok' => 'true',
            'data' => [
                'id' => $client->id,
                'name' => $client->first_name,
                'balance' => $client->balance,
                'phones' => $client->phones->pluck('phone')
            ]
        ]);
    }
    public function exportAll()
    {
        if (!Auth::user()->hasPermissionTo('export-client')) {
            abort(403, 'Unauthorized action.');
        }
        \Log::info('Exporting all clients');
        $clientsCount = Client::count();
        \Log::info('Clients count: ' . $clientsCount);
        $export = new ClientsExport();
        return Excel::download($export, 'all_clients.xlsx');
    }
    
    public function exportClient(int $id, Request $request)
    {
        if (!Auth::user()->hasPermissionTo('export-clients')) {
            abort(403, 'Unauthorized action.');
        }
    
        return $this->clientService->exportClient($id);
    }

    public function exportClientWithDateFilter(int $id, Request $request)
    {
        if (!Auth::user()->hasPermissionTo('view-clients')) {
            abort(403, 'Unauthorized action.');
        }  
        if (!Auth::user()->hasPermissionTo('export-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        return Excel::download(
            new ClientsExport($id, $startDate, $endDate),
            'client_' . $id . '_details_' . ($startDate ?: 'start') . '_to_' . ($endDate ?: 'end') . '.xlsx'
        );
    }
}