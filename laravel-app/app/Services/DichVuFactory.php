<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Design Pattern: Factory Method Pattern (Mẫu nhà máy)
 * 
 * Vai trò: Cung cấp một phương thức tĩnh `make()` chịu trách nhiệm khởi tạo động các lớp Service.
 * Giúp che giấu việc dùng từ khóa `new` thủ công ở các Controller và tạo ra sự đồng bộ 
 * khi muốn thay đổi lớp khởi tạo trong tương lai.
 */
class DichVuFactory
{
    /**
     * Khởi tạo đối tượng Service cụ thể dựa trên loại chuỗi đầu vào ($type)
     * Trả về service tương ứng được phân giải từ Laravel Container (để hỗ trợ tự động tiêm Dependency)
     */
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
