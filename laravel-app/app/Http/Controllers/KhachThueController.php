<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use Illuminate\Http\Request;

class KhachThueController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = DichVuFactory::make('khach');
    }

    public function index()
    {
        return response()->json($this->service->all());
    }

    public function show($id)
    {
        return response()->json($this->service->find($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hoTen' => 'required|string',
            'cccd' => 'required|string|unique:khach_thues,cccd',
            'sdt' => 'required|string',
            'email' => 'required|email',
            'gioiTinh' => 'required|string',
            'ngaySinh' => 'required|date',
            'queQuan' => 'required|string',
        ]);

        $tenant = $this->service->create($validated);
        return response()->json($tenant, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'hoTen' => 'sometimes|required|string',
            'cccd' => 'sometimes|required|string|unique:khach_thues,cccd,' . $id . ',maKhach',
            'sdt' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'gioiTinh' => 'sometimes|required|string',
            'ngaySinh' => 'sometimes|required|date',
            'queQuan' => 'sometimes|required|string',
        ]);

        $tenant = $this->service->update($id, $validated);
        return response()->json($tenant);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');
        return response()->json($this->service->search($keyword));
    }
}
