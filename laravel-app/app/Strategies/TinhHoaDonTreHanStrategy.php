<?php

namespace App\Strategies;

use App\Models\HopDong;

/**
 * Thuật toán tính tiền hóa đơn Phạt trễ hạn (Late Fee Strategy)
 * 
 * Công thức: Tổng tiền = Giá phòng + (Điện * 4.000đ) + (Nước * 30.000đ) + Phí đóng trễ hạn (100.000đ)
 */
class TinhHoaDonTreHanStrategy implements TinhHoaDonStrategy
{
    /**
     * Triển khai thuật toán tính tiền hóa đơn cộng thêm phụ phí đóng phạt
     */
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float
    {
        $giaThue = $hopDong->giaThueThang;
        $dien = ($chiTietPhuPhi['dienMoi'] - $chiTietPhuPhi['dienCu']) * 4000;
        $nuoc = ($chiTietPhuPhi['nuocMoi'] - $chiTietPhuPhi['nuocCu']) * 30000;
        $phiTreHan = 100000; // Phí phạt đóng muộn 100.000đ cố định

        return $giaThue + max(0, $dien) + max(0, $nuoc) + $phiTreHan;
    }
}
