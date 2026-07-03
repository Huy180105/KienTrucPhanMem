<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use App\Facades\QuanLyThueFacade;
use Illuminate\Http\Request;

class HopDongController extends Controller
{
    protected $service;
    protected $facade;

    public function __construct(QuanLyThueFacade $facade)
    {
        $this->service = DichVuFactory::make('hopdong');
        $this->facade = $facade;
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
            'maPhong' => 'required|string|exists:phong_tros,maPhong',
            'maKhach' => 'required|integer|exists:khach_thues,maKhach',
            'ngayLap' => 'required|date',
            'ngayBatDau' => 'required|date',
            'ngayKetThuc' => 'required|date|after_or_equal:ngayBatDau',
            'tienCoc' => 'required|numeric|min:0',
            'giaThueThang' => 'required|numeric|min:0',
        ]);

        $contract = $this->facade->lapHopDong($validated);
        return response()->json($contract, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ngayLap' => 'sometimes|required|date',
            'ngayBatDau' => 'sometimes|required|date',
            'ngayKetThuc' => 'sometimes|required|date',
            'tienCoc' => 'sometimes|required|numeric|min:0',
            'giaThueThang' => 'sometimes|required|numeric|min:0',
            'trangThai' => 'sometimes|required|string',
        ]);

        $contract = $this->service->update($id, $validated);
        return response()->json($contract);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
    }

    public function terminate($id)
    {
        $contract = $this->facade->thanhLyHopDong($id);
        return response()->json($contract);
    }

    public function active()
    {
        return response()->json($this->service->active());
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');
        return response()->json($this->service->search($keyword));
    }
}
