<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use Illuminate\Http\Request;

class TaiSanController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = DichVuFactory::make('taisan');
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
            'tenTaiSan' => 'required|string',
            'soLuong' => 'required|integer|min:1',
            'tinhTrang' => 'required|string',
            'maPhong' => 'nullable|string|exists:phong_tros,maPhong',
        ]);

        $asset = $this->service->create($validated);
        return response()->json($asset, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tenTaiSan' => 'sometimes|required|string',
            'soLuong' => 'sometimes|required|integer|min:1',
            'tinhTrang' => 'sometimes|required|string',
            'maPhong' => 'nullable|string|exists:phong_tros,maPhong',
        ]);

        $asset = $this->service->update($id, $validated);
        return response()->json($asset);
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

    public function byRoom($roomId)
    {
        return response()->json($this->service->byRoom($roomId));
    }
}
