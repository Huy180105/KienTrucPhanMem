<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractMailController;

Route::get('/', function () {
    return view('dashboard');
});

Route::post('/api/contracts/{id}/send-reminder', [ContractMailController::class, 'sendReminder']);
