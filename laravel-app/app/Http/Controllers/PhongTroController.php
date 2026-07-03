<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use Illuminate\Http\Request;

class PhongTroController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = DichVuFactory::make('phong');
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
            'maPhong' => 'required|string|unique:phong_tros,maPhong',
            'tenPhong' => 'required|string',
            'tang' => 'required|integer|min:1',
            'giaPhong' => 'required|numeric|min:0',
            'trangThai' => 'required|string',
        ]);

        $room = $this->service->create($validated);
        return response()->json($room, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tenPhong' => 'sometimes|required|string',
            'tang' => 'sometimes|required|integer|min:1',
            'giaPhong' => 'sometimes|required|numeric|min:0',
            'trangThai' => 'sometimes|required|string',
        ]);

        $room = $this->service->update($id, $validated);
        return response()->json($room);
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
