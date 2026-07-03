<?php

namespace App\Services;

use App\Models\HopDong;

class HopDongService
{
    public function all()
    {
        return HopDong::with(['phongTro', 'khachThue'])->get();
    }

    public function find($id)
    {
        return HopDong::with(['phongTro', 'khachThue', 'hoaDons'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return HopDong::create($data);
    }

    public function update($id, array $data)
    {
        $contract = HopDong::findOrFail($id);
        $contract->update($data);
        return $contract;
    }

    public function delete($id)
    {
        $contract = HopDong::findOrFail($id);
        return $contract->delete();
    }

    public function search($keyword)
    {
        return HopDong::where('maPhong', 'like', "%{$keyword}%")
            ->orWhereHas('khachThue', function ($query) use ($keyword) {
                $query->where('hoTen', 'like', "%{$keyword}%");
            })
            ->with(['phongTro', 'khachThue'])
            ->get();
    }

    public function active()
    {
        return HopDong::where('trangThai', 'Đang hiệu lực')->with(['phongTro', 'khachThue'])->get();
    }

    public function terminate($id)
    {
        $contract = HopDong::findOrFail($id);
        $contract->update(['trangThai' => 'Đã thanh lý']);
        return $contract;
    }
}
