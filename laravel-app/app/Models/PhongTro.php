<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhongTro extends Model
{
    protected $table = 'phong_tros';
    protected $primaryKey = 'maPhong';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'maPhong',
        'tenPhong',
        'tang',
        'giaPhong',
        'trangThai',
    ];

    public function taiSans(): HasMany
    {
        return $this->hasMany(TaiSan::class, 'maPhong', 'maPhong');
    }

    public function hopDongs(): HasMany
    {
        return $this->hasMany(HopDong::class, 'maPhong', 'maPhong');
    }
}
