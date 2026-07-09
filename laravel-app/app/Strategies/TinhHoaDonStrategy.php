<?php

namespace App\Strategies;

use App\Models\HopDong;

/**
 * Design Pattern: Strategy Pattern (Mẫu chiến lược)
 * 
 * Vai trò: Khai báo một Interface dùng chung cho toàn bộ thuật toán tính toán tiền hóa đơn phòng.
 * Các lớp cụ thể (Concrete Strategies) sẽ triển khai thuật toán tính tiền khác nhau tùy 
 * theo hoàn cảnh (đóng đúng hạn, đóng trễ hạn, có khuyến mãi,...).
 * 
 * Cho phép hoán đổi linh hoạt thuật toán tính tiền hóa đơn lúc chạy (Runtime) mà không cần 
 * sửa đổi mã nguồn điều hướng gốc (Open/Closed Principle).
 */
interface TinhHoaDonStrategy
{
    /**
     * Phương thức chung tính tổng tiền hóa đơn
     */
    public function tinh(HopDong $hopDong, array $chiTietPhuPhi): float;
}
