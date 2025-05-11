<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\BalanceMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientsExport implements WithMultipleSheets
{
    protected $clientId;
    protected $startDate;
    protected $endDate;

    public function __construct(?int $clientId = null, ?string $startDate = null, ?string $endDate = null)
    {
        $this->clientId = $clientId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {

        $sheets = [];

        // Sheet for client data
        $sheets[] = new class($this->clientId) implements FromCollection, WithHeadings, WithMapping {
            protected $clientId;

            public function __construct(?int $clientId)
            {
                $this->clientId = $clientId;
            }

            public function collection()
            {
                $query = Client::with('phones');
                if ($this->clientId) {
                    $query->where('id', $this->clientId);
                }
                return $query->get();
            }

            public function map($client): array
            {
                return [
                    $client->id,
                    $client->first_name,
                    $client->lastname,
                    $client->company_name,
                    $client->balance,
                    $client->address,
                    $client->phones->pluck('phone')->implode(', '),
                ];
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Ism',
                    'Familiya',
                    'Kompaniyasi',
                    'Balansi (UZS)',
                    'Manzil',
                    'Telefonlar',
                ];
            }
        };

        // Sheet for balance movements (only for single client)
        if ($this->clientId) {
            $sheets[] = new class($this->clientId, $this->startDate, $this->endDate) implements FromCollection, WithHeadings, WithMapping {
                protected $clientId;
                protected $startDate;
                protected $endDate;

                public function __construct(int $clientId, ?string $startDate, ?string $endDate)
                {
                    $this->clientId = $clientId;
                    $this->startDate = $startDate;
                    $this->endDate = $endDate;
                }

                public function collection()
                {
                    $query = BalanceMovement::where('client_id', $this->clientId)
                        ->with(['paymentType', 'user'])
                        ->orderBy('created_at', 'desc');

                    // Apply date filters if provided
                    if ($this->startDate) {
                        $query->whereDate('created_at', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $query->whereDate('created_at', '<=', $this->endDate);
                    }

                    return $query->get();
                }

                public function map($movement): array
                {
                    return [
                        $movement->id,
                        $movement->amount,
                        $movement->new_balance,
                        $movement->paymentType->name ?? 'N/A',
                        $movement->user->name ?? 'Unknown',
                        $movement->comment,
                        $movement->created_at->format('Y-m-d H:i:s'),
                    ];
                }

                public function headings(): array
                {
                    return [
                        'ID',
                        'Summa (UZS)',
                        'Yangi Balans (UZS)',
                        'To\'lov Turi',
                        'Bugalter',
                        'Izoh',
                        'Sana',
                    ];
                }
            };
        }

        return $sheets;
    }
}