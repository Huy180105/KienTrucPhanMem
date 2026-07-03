<?php

namespace App\Facades;

use App\Services\HopDongService;
use App\Services\PhongTroService;

class QuanLyThueFacade
{
    protected $hopDongService;
    protected $phongTroService;

    public function __construct(
        HopDongService $hopDongService,
        PhongTroService $phongTroService
    ) {
        $this->hopDongService = $hopDongService;
        $this->phongTroService = $phongTroService;
    }

    public function lapHopDong(array $data)
    {
        // Tạo hợp đồng
        $hopDong = $this->hopDongService->create($data);
        
        // Cập nhật trạng thái phòng trọ
        $this->phongTroService->update($data['maPhong'], ['trangThai' => 'Đã thuê']);

        return $hopDong;
    }

    public function thanhLyHopDong($id)
    {
        // Thanh lý hợp đồng
        $hopDong = $this->hopDongService->terminate($id);

        // Cập nhật trạng thái phòng trọ
        $this->phongTroService->update($hopDong->maPhong, ['trangThai' => 'Trống']);

        return $hopDong;
    }
}
