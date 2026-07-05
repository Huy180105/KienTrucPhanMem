<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'Hệ thống Quản lý Phòng trọ API Backend đang hoạt động.'
    ]);
});
