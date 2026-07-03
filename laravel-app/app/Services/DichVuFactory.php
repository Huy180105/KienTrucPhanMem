<?php

namespace App\Services;

use InvalidArgumentException;

class DichVuFactory
{
    public static function make(string $type)
    {
        return match ($type) {
            'phong' => app(PhongTroService::class),
            'khach' => app(KhachThueService::class),
            'hopdong' => app(HopDongService::class),
            'hoadon' => app(HoaDonService::class),
            'taisan' => app(TaiSanService::class),
            default => throw new InvalidArgumentException('Loại dịch vụ không hợp lệ: ' . $type),
        };
    }
}
