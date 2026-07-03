<?php

namespace App\Services;

use App\Models\TaiSan;

class TaiSanService
{
    public function all()
    {
        return TaiSan::all();
    }

    public function find($id)
    {
        return TaiSan::findOrFail($id);
    }

    public function create(array $data)
    {
        return TaiSan::create($data);
    }

    public function update($id, array $data)
    {
        $asset = TaiSan::findOrFail($id);
        $asset->update($data);
        return $asset;
    }

    public function delete($id)
    {
        $asset = TaiSan::findOrFail($id);
        return $asset->delete();
    }

    public function search($keyword)
    {
        return TaiSan::where('tenTaiSan', 'like', "%{$keyword}%")->get();
    }

    public function byRoom($roomId)
    {
        return TaiSan::where('maPhong', $roomId)->get();
    }
}
