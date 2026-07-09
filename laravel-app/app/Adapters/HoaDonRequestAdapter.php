<?php

namespace App\Adapters;

/**
 * Design Pattern: Adapter Pattern (Mẫu tương thích/bộ chuyển đổi)
 * 
 * Vai trò: Giúp chuyển đổi giao diện dữ liệu đầu vào (Ví dụ định dạng camelCase từ Client 
 * như contractId, month, year) thành cấu trúc dữ liệu tiếng Việt chuẩn tương thích với các 
 * thuộc tính cơ sở dữ liệu MySQL ở Backend (maHopDong, thang, nam).
 * 
 * Giúp bảo vệ logic backend không bị ảnh hưởng khi định dạng request của frontend thay đổi.
 */
class HoaDonRequestAdapter
{
    /**
     * Chuyển đổi dữ liệu Request thô từ client thành mảng thuộc tính tiếng Việt chuẩn cho CSDL
     */
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
