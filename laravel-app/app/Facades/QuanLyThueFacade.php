<?php

namespace App\Facades;

use App\Services\HopDongService;
use App\Services\PhongTroService;

/**
 * Design Pattern: Facade Pattern
 * 
 * Vai trò: Cung cấp một giao diện đơn giản (Interface) cho một tập hợp các giao diện phức tạp
 * trong các hệ thống con (Subsystems). Ở đây là HopDongService và PhongTroService.
 * 
 * Thay vì để Controller tự tương tác trực tiếp với cả 2 service và tự quản lý giao dịch
 * (gây ra coupling cao), Facade sẽ gộp các thao tác liên kết phức tạp thành các phương thức 
 * duy nhất dễ gọi.
 */
class QuanLyThueFacade
{
    protected $hopDongService;
    protected $phongTroService;

    /**
     * Dependency Injection để tiêm các Service nghiệp vụ con vào Facade
     */
    public function __construct(
        HopDongService $hopDongService,
        PhongTroService $phongTroService
    ) {
        $this->hopDongService = $hopDongService;
        $this->phongTroService = $phongTroService;
    }

    /**
     * Nghiệp vụ phức tạp: Lập hợp đồng mới
     * Bước 1: Tạo hợp đồng cho khách thuê (Ghi nhận ngày thuê, tiền cọc, giá thuê).
     * Bước 2: Đồng thời cập nhật trạng thái phòng trọ sang 'Đã thuê' để không cho người khác thuê nữa.
     */
    public function lapHopDong(array $data)
    {
        // 1. Tạo bản ghi hợp đồng mới trong CSDL
        $hopDong = $this->hopDongService->create($data);
        
        // 2. Chuyển đổi trạng thái phòng trọ sang 'Đã thuê'
        $this->phongTroService->update($data['maPhong'], ['trangThai' => 'Đã thuê']);

        return $hopDong;
    }

    /**
     * Nghiệp vụ phức tạp: Thanh lý hợp đồng
     * Bước 1: Cập nhật trạng thái hợp đồng thành 'Đã thanh lý'.
     * Bước 2: Trả lại trạng thái phòng trọ thành 'Trống' để khách tiếp theo có thể thuê.
     */
    public function thanhLyHopDong($id)
    {
        // 1. Chuyển đổi trạng thái hợp đồng sang 'Đã thanh lý'
        $hopDong = $this->hopDongService->terminate($id);

        // 2. Đưa phòng trọ trở lại trạng thái 'Trống'
        $this->phongTroService->update($hopDong->maPhong, ['trangThai' => 'Trống']);

        return $hopDong;
    }
}
