<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Client extends Model
{
    protected $fillable = [
        'first_name',
        'lastname',
        'balance',
        'company_name',
        'address',
    ];


    public function phones(): HasMany
    {
        return $this->hasMany(ClientPhone::class);
    }
    public function balanceMovements(): HasMany
    {
        return $this->hasMany(BalanceMovement::class);
    }
}
