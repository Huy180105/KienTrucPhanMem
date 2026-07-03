<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhongTroController;
use App\Http\Controllers\KhachThueController;
use App\Http\Controllers\HopDongController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\TaiSanController;

// 1. API Danh Sách Phòng
Route::get('/rooms', [PhongTroController::class, 'index']);
Route::get('/rooms/search', [PhongTroController::class, 'search']);
Route::get('/rooms/{id}', [PhongTroController::class, 'show']);
Route::post('/rooms', [PhongTroController::class, 'store']);
Route::put('/rooms/{id}', [PhongTroController::class, 'update']);
Route::delete('/rooms/{id}', [PhongTroController::class, 'destroy']);
Route::get('/rooms/{id}/assets', [TaiSanController::class, 'byRoom']);

// 2. API Khách Thuê
Route::get('/tenants', [KhachThueController::class, 'index']);
Route::get('/tenants/search', [KhachThueController::class, 'search']);
Route::get('/tenants/{id}', [KhachThueController::class, 'show']);
Route::post('/tenants', [KhachThueController::class, 'store']);
Route::put('/tenants/{id}', [KhachThueController::class, 'update']);
Route::delete('/tenants/{id}', [KhachThueController::class, 'destroy']);

// 3. API Hợp Đồng
Route::get('/contracts', [HopDongController::class, 'index']);
Route::get('/contracts/search', [HopDongController::class, 'search']);
Route::get('/contracts/active', [HopDongController::class, 'active']);
Route::get('/contracts/{id}', [HopDongController::class, 'show']);
Route::post('/contracts', [HopDongController::class, 'store']);
Route::put('/contracts/{id}', [HopDongController::class, 'update']);
Route::delete('/contracts/{id}', [HopDongController::class, 'destroy']);
Route::put('/contracts/{id}/terminate', [HopDongController::class, 'terminate']);

// 4. API Hóa Đơn
Route::get('/invoices', [HoaDonController::class, 'index']);
Route::get('/invoices/search', [HoaDonController::class, 'search']);
Route::get('/invoices/unpaid', [HoaDonController::class, 'unpaid']);
Route::get('/invoices/{id}', [HoaDonController::class, 'show']);
Route::post('/invoices', [HoaDonController::class, 'store']);
Route::put('/invoices/{id}', [HoaDonController::class, 'update']);
Route::delete('/invoices/{id}', [HoaDonController::class, 'destroy']);
Route::post('/invoices/calculate', [HoaDonController::class, 'calculate']);
Route::put('/invoices/{id}/pay', [HoaDonController::class, 'pay']);

// 5. API Tài Sản
Route::get('/assets', [TaiSanController::class, 'index']);
Route::get('/assets/search', [TaiSanController::class, 'search']);
Route::get('/assets/{id}', [TaiSanController::class, 'show']);
Route::post('/assets', [TaiSanController::class, 'store']);
Route::put('/assets/{id}', [TaiSanController::class, 'update']);
Route::delete('/assets/{id}', [TaiSanController::class, 'destroy']);
