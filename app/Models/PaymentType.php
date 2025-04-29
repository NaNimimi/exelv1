<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $fillable = ['name', 'currency'];

    public function balanceMovements()
    {
        return $this->hasMany(BalanceMovement::class);
    }
}