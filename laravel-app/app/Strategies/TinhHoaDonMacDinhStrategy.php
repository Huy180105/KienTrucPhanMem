<?php

namespace App\Strategies;

use App\Models\HopDong;

/**
 * Thuật toán tính tiền hóa đơn Mặc định (Default Strategy)
 * 
 * Công thức: Tổng tiền = Giá phòng + (Chỉ số điện tiêu thụ * 4.000đ) + (Chỉ số nước tiêu thụ * 30.000đ)
 */
class TinhHoaDonMacDinhStrategy implements TinhHoaDonStrategy
{
    /**
     * Triển khai thuật toán tính tiền hóa đơn thông thường
     */
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float
    {
        $giaThue = $hopDong->giaThueThang;
        $dien = ($chiTietPhuPhi['dienMoi'] - $chiTietPhuPhi['dienCu']) * 4000; // Đơn giá điện 4000đ / kWh
        $nuoc = ($chiTietPhuPhi['nuocMoi'] - $chiTietPhuPhi['nuocCu']) * 30000; // Đơn giá nước 30000đ / m3
        
        return $giaThue + max(0, $dien) + max(0, $nuoc);
    }
}
