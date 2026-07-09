<?php

namespace App\Observers;

use App\Models\HopDong;

/**
 * Design Pattern: Observer Pattern (Trình quan sát)
 * 
 * Vai trò: Giúp đăng ký và tự động lắng nghe các sự kiện thay đổi trạng thái (Life-cycle events)
 * của Eloquent Model (như created, updated, deleted,...) để thực hiện các phản ứng tự động 
 * ở một nơi khác (ở đây là cập nhật trạng thái phòng trọ tương ứng).
 * 
 * Điều này tách biệt hoàn toàn sự phụ thuộc (De-coupling): Model HopDong không cần biết 
 * ai đang quan sát nó, nhưng bất cứ khi nào nó được tạo hoặc sửa, trạng thái phòng trọ 
 * vẫn luôn được đồng bộ an toàn.
 */
class HopDongObserver
{
    /**
     * Tự động kích hoạt khi có một Hợp đồng mới được tạo trong CSDL (sự kiện created)
     * Hành động: Tìm phòng trọ liên kết và đánh dấu phòng đó thành 'Đã thuê'
     */
    public function created(HopDong $hopDong): void
    {
        $hopDong->phongTro()->update(['trangThai' => 'Đã thuê']);
    }

    /**
     * Tự động kích hoạt khi có một Hợp đồng được cập nhật (sự kiện updated)
     * Hành động: Nếu trạng thái hợp đồng bị chuyển sang 'Đã thanh lý',
     * giải phóng phòng trọ về trạng thái 'Trống' để khách sau thuê.
     */
    public function updated(HopDong $hopDong): void
    {
        // Kiểm tra xem trường trangThai có bị sửa đổi (isDirty) và giá trị mới có phải là 'Đã thanh lý'
        if ($hopDong->isDirty('trangThai') && $hopDong->trangThai === 'Đã thanh lý') {
            $hopDong->phongTro()->update(['trangThai' => 'Trống']);
        }
    }
}
