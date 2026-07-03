<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhachThue extends Model
{
    protected $table = 'khach_thues';
    protected $primaryKey = 'maKhach';

    protected $fillable = [
        'hoTen',
        'cccd',
        'sdt',
        'email',
        'gioiTinh',
        'ngaySinh',
        'queQuan',
    ];

    public function hopDongs(): HasMany
    {
        return $this->hasMany(HopDong::class, 'maKhach', 'maKhach');
    }
}
