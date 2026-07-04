<?php

namespace App\Strategies;

use App\Models\HopDong;

class TinhHoaDonMacDinhStrategy implements TinhHoaDonStrategy
{
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float
    {
        $giaThue = $hopDong->giaThueThang;
        $dien = ($chiTietPhuPhi['dienMoi'] - $chiTietPhuPhi['dienCu']) * 4000; // 4000đ / kWh
        $nuoc = ($chiTietPhuPhi['nuocMoi'] - $chiTietPhuPhi['nuocCu']) * 30000; // 30000đ / m3
        
        return $giaThue + max(0, $dien) + max(0, $nuoc);
    }
}
