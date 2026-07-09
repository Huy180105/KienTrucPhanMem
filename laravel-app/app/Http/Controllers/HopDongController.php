<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use App\Facades\QuanLyThueFacade;
use Illuminate\Http\Request;

class HopDongController extends Controller
{
    // Tầng nghiệp vụ xử lý hợp đồng độc lập
    protected $service;
    
    // Hệ thống Facade trung gian điều phối các nghiệp vụ liên kết (Hợp đồng + Phòng trọ)
    protected $facade;

    /**
     * Khởi tạo Controller Hợp Đồng
     * Áp dụng Dependency Injection để lấy đối tượng QuanLyThueFacade
     */
    public function __construct(QuanLyThueFacade $facade)
    {
        // Sử dụng Design Pattern: Factory Method (DichVuFactory) để khởi tạo HopDongService động
        $this->service = DichVuFactory::make('hopdong');
        $this->facade = $facade;
    }

    /**
     * API Lấy danh sách toàn bộ hợp đồng kèm theo thông tin Phòng trọ và Khách thuê
     * GET /api/contracts
     */
    public function index()
    {
        return response()->json($this->service->all());
    }

    /**
     * API Lấy chi tiết thông tin một hợp đồng theo ID kèm lịch sử hóa đơn
     * GET /api/contracts/{id}
     */
    public function show($id)
    {
        return response()->json($this->service->find($id));
    }

    /**
     * API Lập hợp đồng mới
     * POST /api/contracts
     * 
     * Luồng chạy:
     * 1. Xác thực dữ liệu đầu vào (CCCD, SĐT, ngày bắt đầu/kết thúc hợp lệ)
     * 2. Gọi Facade (QuanLyThueFacade) để điều phối hành động phức tạp (Tạo HĐ + Cập nhật phòng thành Đã thuê)
     */
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

        // Sử dụng Design Pattern: Facade để ẩn đi sự phức tạp khi lập hợp đồng (Tạo HĐ & Đổi trạng thái phòng)
        $contract = $this->facade->lapHopDong($validated);
        return response()->json($contract, 201);
    }

    /**
     * API Cập nhật thông tin hợp đồng
     * PUT /api/contracts/{id}
     */
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

        return response()->json($this->service->update($id, $validated));
    }

    /**
     * API Xóa hợp đồng khỏi cơ sở dữ liệu
     * DELETE /api/contracts/{id}
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
    }

    /**
     * API Thanh lý hợp đồng
     * PUT /api/contracts/{id}/terminate
     * 
     * Luồng chạy:
     * 1. Gọi Facade để thực hiện thanh lý hợp đồng
     * 2. Facade cập nhật trạng thái hợp đồng thành "Đã thanh lý" và đổi trạng thái phòng về "Trống"
     */
    public function terminate($id)
    {
        // Sử dụng Design Pattern: Facade để đơn giản hóa quá trình thanh lý hợp đồng
        $contract = $this->facade->thanhLyHopDong($id);
        return response()->json($contract);
    }

    /**
     * API Lấy danh sách hợp đồng đang còn hiệu lực hoạt động
     * GET /api/contracts/active
     */
    public function active()
    {
        return response()->json($this->service->active());
    }

    /**
     * API Tìm kiếm hợp đồng theo mã phòng hoặc tên khách thuê
     * GET /api/contracts/search?keyword=...
     */
    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');
        return response()->json($this->service->search($keyword));
    }
}
