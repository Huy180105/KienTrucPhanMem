<?php

namespace App\Strategies;

use App\Models\HopDong;

interface TinhHoaDonStrategy
{
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float;
}
