<?php

namespace App\Services;



use App\Models\Client;
use App\Models\ClientPhone;
use App\Models\BalanceMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function all(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {   
        $query = Client::with('phones')->orderBy('id', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Client
    {
        return Client::with('phones')->find($id);
    }

    public function create(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $client = Client::create([
                'first_name'    => $data['first_name'] ?? null,
                'lastname'      => $data['lastname'] ?? null,
                'balance'       => $data['balance'] ?? 0,
                'company_name'  => $data['company_name'] ?? null,
                'address'       => $data['address'] ?? null,
            ]);

            if (!empty($data['phones'])) {
                foreach ($data['phones'] as $phone) {
                    $client->phones()->create(['phone' => $phone]);
                }
            }

            return $client->load('phones');
        });
    }

    public function update(int $id, array $data): ?Client
    {
        return DB::transaction(function () use ($id, $data) {
            $client = Client::findOrFail($id);

            $client->update([
                'first_name'    => $data['first_name'] ?? $client->first_name,
                'lastname'      => $data['lastname'] ?? $client->lastname,
                'balance'       => $data['balance'] ?? $client->balance,
                'company_name'  => $data['company_name'] ?? $client->company_name,
                'address'       => $data['address'] ?? $client->address,
            ]);

            if (isset($data['phones'])) {
                // Delete old phones
                $client->phones()->delete();

                // Add new ones
                foreach ($data['phones'] as $phone) {
                    $client->phones()->create(['phone' => $phone]);
                }
            }

            return $client->load('phones');
        });
    }

    public function delete(int $id): bool
    {
        $client = Client::findOrFail($id);
        return $client->delete();
    }

    public function addBalance(int $clientId, array $data): Client
    {
        return DB::transaction(function () use ($clientId, $data) {
            $client = Client::findOrFail($clientId);

            $newBalance = $client->balance + $data['amount'];

            $client->update(['balance' => $newBalance]);

            BalanceMovement::create([
                'user_id' => Auth::id(),
                'client_id' => $clientId,
                'amount' => $data['amount'],
                'new_balance' => $newBalance,
                'comment' => $data['comment'],
            ]);

            // Load phones to get the phone numbers
            $client->load('phones');

            // Send notification to localhost/notify-user for each phone number
            if ($client->phones->isNotEmpty()) {
                foreach ($client->phones as $phone) {
                    try {
                        $response = Http::get('http://localhost/notify-user', [
                            'phone_number' => $phone->phone,
                            'amount' => $data['amount'],
                        ]);

                        if (!$response->successful()) {
                            Log::warning('Failed to send notification to localhost/notify-user', [
                                'phone_number' => $phone->phone,
                                'amount' => $data['amount'],
                                'status' => $response->status(),
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Log the error but don't interrupt the transaction
                        Log::error('Error sending notification to localhost/notify-user', [
                            'phone_number' => $phone->phone,
                            'amount' => $data['amount'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            return $client->load('phones');
        });
    }

    public function findByPhone(string $phone): ?array
    {
        $client = Client::with('phones')
            ->whereHas('phones', fn($q) => $q->where('phone', $phone))
            ->first();
    
        if (!$client) {
            return null;
        }
    
        return [
            'id' => $client->id,
            'first_name' => $client->first_name,
            'lastname' => $client->lastname,
            'balance' => $client->balance,
            'company_name' => $client->company_name,
            'address' => $client->address,
            'phones' => $client->phones->pluck('phone')->toArray(),
            'created_at' => $client->created_at->toIso8601String(),
            'updated_at' => $client->updated_at->toIso8601String(),
        ];
    }
}
