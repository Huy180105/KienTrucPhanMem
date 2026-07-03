<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaiSan extends Model
{
    protected $table = 'tai_sans';
    protected $primaryKey = 'maTaiSan';

    protected $fillable = [
        'tenTaiSan',
        'soLuong',
        'tinhTrang',
        'maPhong',
    ];

    public function phongTro(): BelongsTo
    {
        return $this->belongsTo(PhongTro::class, 'maPhong', 'maPhong');
    }
}
