<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

Route::get('/clients/{phone}', [ClientController::class, 'getByPhone']);