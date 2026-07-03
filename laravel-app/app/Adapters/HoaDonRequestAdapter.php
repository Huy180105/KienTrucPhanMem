<?php

namespace App\Adapters;

class HoaDonRequestAdapter
{
    public function toHoaDonData(array $request): array
    {
        return [
            'maHopDong' => $request['maHopDong'] ?? $request['contractId'] ?? null,
            'thang' => (int)($request['thang'] ?? $request['month'] ?? date('m')),
            'nam' => (int)($request['nam'] ?? $request['year'] ?? date('Y')),
            'ngayLap' => $request['ngayLap'] ?? $request['ngayCreated'] ?? date('Y-m-d'),
            'tongTien' => (double)($request['tongTien'] ?? $request['total'] ?? 0),
            'trangThai' => $request['trangThai'] ?? 'Chưa thanh toán',
        ];
    }
}
