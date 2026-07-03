<?php

namespace App\Services;

use App\Models\HoaDon;

class HoaDonService
{
    public function all()
    {
        return HoaDon::with('hopDong.phongTro')->get();
    }

    public function find($id)
    {
        return HoaDon::with('hopDong.khachThue')->findOrFail($id);
    }

    public function create(array $data)
    {
        return HoaDon::create($data);
    }

    public function update($id, array $data)
    {
        $invoice = HoaDon::findOrFail($id);
        $invoice->update($data);
        return $invoice;
    }

    public function delete($id)
    {
        $invoice = HoaDon::findOrFail($id);
        return $invoice->delete();
    }

    public function search(array $params)
    {
        $query = HoaDon::query();

        if (!empty($params['month'])) {
            $query->where('thang', $params['month']);
        }
        if (!empty($params['year'])) {
            $query->where('nam', $params['year']);
        }
        if (!empty($params['room_id'])) {
            $query->whereHas('hopDong', function ($q) use ($params) {
                $q->where('maPhong', $params['room_id']);
            });
        }

        return $query->with('hopDong.phongTro')->get();
    }

    public function unpaid()
    {
        return HoaDon::where('trangThai', 'Chưa thanh toán')->with('hopDong.phongTro')->get();
    }

    public function pay($id)
    {
        $invoice = HoaDon::findOrFail($id);
        $invoice->update(['trangThai' => 'Đã thanh toán']);
        return $invoice;
    }
}
