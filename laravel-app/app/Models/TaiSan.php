<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaiSan extends Model
{
    use SoftDeletes;
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
