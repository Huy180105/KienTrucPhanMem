<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'admin', // Default role for authenticated user
                ],
                'message' => 'Đăng nhập thành công!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không đúng.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    public function user(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'admin',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Chưa đăng nhập.'
        ], 401);
    }
}
