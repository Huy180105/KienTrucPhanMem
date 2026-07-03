<?php

namespace App\Observers;

use App\Models\HopDong;

class HopDongObserver
{
    public function created(HopDong $hopDong): void
    {
        $hopDong->phongTro()->update(['trangThai' => 'Đã thuê']);
    }

    public function updated(HopDong $hopDong): void
    {
        if ($hopDong->isDirty('trangThai') && $hopDong->trangThai === 'Đã thanh lý') {
            $hopDong->phongTro()->update(['trangThai' => 'Trống']);
        }
    }
}
