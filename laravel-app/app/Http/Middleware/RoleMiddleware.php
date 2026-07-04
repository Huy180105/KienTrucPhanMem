<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chặn các phương thức chỉnh sửa dữ liệu (POST, PUT, DELETE, PATCH)
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $path = $request->path();
            
            // Ngoại trừ các API tính toán hoặc gửi mail không ảnh hưởng trực tiếp CRUD
            if (str_ends_with($path, 'calculate') || str_ends_with($path, 'send-reminder')) {
                return $next($request);
            }

            // Lấy role từ Header, mặc định là admin nếu không truyền
            $role = $request->header('X-User-Role', 'admin');

            if ($role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này. Chỉ Quản trị viên mới được phép Thêm, Sửa, Xóa.'
                ], 403);
            }
        }

        return $next($request);
    }
}
