<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HopDong extends Model
{
    protected $table = 'hop_dongs';
    protected $primaryKey = 'maHopDong';

    protected $fillable = [
        'maPhong',
        'maKhach',
        'ngayLap',
        'ngayBatDau',
        'ngayKetThuc',
        'tienCoc',
        'giaThueThang',
        'trangThai',
    ];

    public function phongTro(): BelongsTo
    {
        return $this->belongsTo(PhongTro::class, 'maPhong', 'maPhong');
    }

    public function khachThue(): BelongsTo
    {
        return $this->belongsTo(KhachThue::class, 'maKhach', 'maKhach');
    }

    public function hoaDons(): HasMany
    {
        return $this->hasMany(HoaDon::class, 'maHopDong', 'maHopDong');
    }
}
