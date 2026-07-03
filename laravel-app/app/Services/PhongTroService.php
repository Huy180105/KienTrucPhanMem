<?php

namespace App\Services;

use App\Models\PhongTro;

class PhongTroService
{
    public function all()
    {
        return PhongTro::with(['taiSans', 'hopDongs'])->get();
    }

    public function find($id)
    {
        return PhongTro::with(['taiSans', 'hopDongs.khachThue', 'hopDongs.hoaDons'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return PhongTro::create($data);
    }

    public function update($id, array $data)
    {
        $room = PhongTro::findOrFail($id);
        $room->update($data);
        return $room;
    }

    public function delete($id)
    {
        $room = PhongTro::findOrFail($id);
        return $room->delete();
    }

    public function search($keyword)
    {
        return PhongTro::where('maPhong', 'like', "%{$keyword}%")
            ->orWhere('tenPhong', 'like', "%{$keyword}%")
            ->get();
    }
}
