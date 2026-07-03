<?php

namespace App\Services;

use App\Models\KhachThue;

class KhachThueService
{
    public function all()
    {
        return KhachThue::all();
    }

    public function find($id)
    {
        return KhachThue::with('hopDongs')->findOrFail($id);
    }

    public function create(array $data)
    {
        return KhachThue::create($data);
    }

    public function update($id, array $data)
    {
        $tenant = KhachThue::findOrFail($id);
        $tenant->update($data);
        return $tenant;
    }

    public function delete($id)
    {
        $tenant = KhachThue::findOrFail($id);
        return $tenant->delete();
    }

    public function search($keyword)
    {
        return KhachThue::where('hoTen', 'like', "%{$keyword}%")
            ->orWhere('cccd', 'like', "%{$keyword}%")
            ->orWhere('sdt', 'like', "%{$keyword}%")
            ->get();
    }
}
