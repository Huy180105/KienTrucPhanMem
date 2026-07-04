<?php

namespace App\Observers;

use App\Models\TaiSan;
use App\Models\TaiSanLog;

class TaiSanObserver
{
    public function created(TaiSan $taiSan): void
    {
        TaiSanLog::create([
            'maTaiSan' => $taiSan->maTaiSan,
            'tenTaiSan' => $taiSan->tenTaiSan,
            'hanhDong' => 'THÊM MỚI',
            'trangThaiCu' => null,
            'trangThaiMoi' => "Số lượng: {$taiSan->soLuong}, Tình trạng: {$taiSan->tinhTrang}, Phòng: " . ($taiSan->maPhong ?? 'Chưa phân bổ'),
            'nguoiThucHien' => request()->header('X-User-Role') === 'admin' ? 'Quản trị viên' : (request()->header('X-User-Role', 'Quản trị viên') === 'viewer' ? 'Khách' : 'Hệ thống'),
        ]);
    }

    public function updated(TaiSan $taiSan): void
    {
        if ($taiSan->isDirty(['tenTaiSan', 'soLuong', 'tinhTrang', 'maPhong'])) {
            $changes = [];
            $original = [];
            
            if ($taiSan->isDirty('tinhTrang')) {
                $original[] = "Tình trạng cũ: " . $taiSan->getOriginal('tinhTrang');
                $changes[] = "Tình trạng mới: " . $taiSan->tinhTrang;
            }
            if ($taiSan->isDirty('soLuong')) {
                $original[] = "Số lượng cũ: " . $taiSan->getOriginal('soLuong');
                $changes[] = "Số lượng mới: " . $taiSan->soLuong;
            }
            if ($taiSan->isDirty('maPhong')) {
                $original[] = "Phòng cũ: " . ($taiSan->getOriginal('maPhong') ?? 'Chưa phân bổ');
                $changes[] = "Phòng mới: " . ($taiSan->maPhong ?? 'Chưa phân bổ');
            }

            TaiSanLog::create([
                'maTaiSan' => $taiSan->maTaiSan,
                'tenTaiSan' => $taiSan->tenTaiSan,
                'hanhDong' => 'CẬP NHẬT',
                'trangThaiCu' => implode(', ', $original),
                'trangThaiMoi' => implode(', ', $changes),
                'nguoiThucHien' => request()->header('X-User-Role') === 'admin' ? 'Quản trị viên' : (request()->header('X-User-Role', 'Quản trị viên') === 'viewer' ? 'Khách' : 'Hệ thống'),
            ]);
        }
    }

    public function deleted(TaiSan $taiSan): void
    {
        TaiSanLog::create([
            'maTaiSan' => $taiSan->maTaiSan,
            'tenTaiSan' => $taiSan->tenTaiSan,
            'hanhDong' => 'THANH LÝ',
            'trangThaiCu' => "Tình trạng: {$taiSan->tinhTrang}, Số lượng: {$taiSan->soLuong}",
            'trangThaiMoi' => 'Đã thanh lý (Xóa mềm)',
            'nguoiThucHien' => request()->header('X-User-Role') === 'admin' ? 'Quản trị viên' : (request()->header('X-User-Role', 'Quản trị viên') === 'viewer' ? 'Khách' : 'Hệ thống'),
        ]);
    }
}
