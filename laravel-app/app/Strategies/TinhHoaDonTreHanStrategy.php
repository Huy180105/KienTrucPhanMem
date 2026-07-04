<?php

namespace App\Strategies;

use App\Models\HopDong;

class TinhHoaDonTreHanStrategy implements TinhHoaDonStrategy
{
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float
    {
        $giaThue = $hopDong->giaThueThang;
        $dien = ($chiTietPhuPhi['dienMoi'] - $chiTietPhuPhi['dienCu']) * 4000;
        $nuoc = ($chiTietPhuPhi['nuocMoi'] - $chiTietPhuPhi['nuocCu']) * 30000;
        $phiTreHan = 150000; // Phí phạt đóng muộn mặc định 150.000đ

        return $giaThue + max(0, $dien) + max(0, $nuoc) + $phiTreHan;
    }
}
