<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoaDon extends Model
{
    protected $table = 'hoa_dons';
    protected $primaryKey = 'maHD';

    protected $fillable = [
        'maHopDong',
        'thang',
        'nam',
        'ngayLap',
        'tongTien',
        'trangThai',
    ];

    public function hopDong(): BelongsTo
    {
        return $this->belongsTo(HopDong::class, 'maHopDong', 'maHopDong');
    }
}
