<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $paymentTypes = [
            ['name' => 'SO\'M', 'currency' => 'UZS'],
            ['name' => 'Uzcard', 'currency' => 'UZS'],
            ['name' => 'HUMO', 'currency' => 'UZS'],
            ['name' => 'CLICK/PAYME', 'currency' => 'UZS'],
            ['name' => 'BOshqa xarajatlar', 'currency' => 'UZS'],
        ];

        foreach ($paymentTypes as $type) {
            PaymentType::create($type);
        }
    }
}