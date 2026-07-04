<?php

namespace App\Services;

use App\Models\TaiSan;
use Illuminate\Support\Facades\Cache;

class TaiSanService
{
    public function all()
    {
        return Cache::remember('tai_san_all', 60, function () {
            return TaiSan::all();
        });
    }

    public function find($id)
    {
        return TaiSan::findOrFail($id);
    }

    public function create(array $data)
    {
        $asset = TaiSan::create($data);
        $this->clearCache($asset->maPhong);
        return $asset;
    }

    public function update($id, array $data)
    {
        $asset = TaiSan::findOrFail($id);
        $oldRoom = $asset->maPhong;
        $asset->update($data);
        $this->clearCache($oldRoom);
        if ($asset->maPhong !== $oldRoom) {
            $this->clearCache($asset->maPhong);
        }
        return $asset;
    }

    public function delete($id)
    {
        $asset = TaiSan::findOrFail($id);
        $maPhong = $asset->maPhong;
        $deleted = $asset->delete();
        $this->clearCache($maPhong);
        return $deleted;
    }

    public function search($keyword)
    {
        $cacheKey = 'tai_san_search_' . md5($keyword);
        return Cache::remember($cacheKey, 60, function () use ($keyword) {
            return TaiSan::where('tenTaiSan', 'like', "%{$keyword}%")->get();
        });
    }

    public function byRoom($roomId)
    {
        $cacheKey = 'tai_san_phong_' . $roomId;
        return Cache::remember($cacheKey, 60, function () use ($roomId) {
            return TaiSan::where('maPhong', $roomId)->get();
        });
    }

    protected function clearCache($roomId = null)
    {
        Cache::forget('tai_san_all');
        if ($roomId) {
            Cache::forget('tai_san_phong_' . $roomId);
        }
        // Xoá toàn bộ cache search bằng cách flush hoặc dựa vào thời gian hết hạn ngắn (60s)
        // Lưu ý: Không dùng flush để tránh mất cache của các nghiệp vụ khác, chỉ rely vào TTL 60s của search cache.
    }
}
