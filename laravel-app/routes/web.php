<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractMailController;

Route::get('/', function () {
    return view('dashboard');
});
