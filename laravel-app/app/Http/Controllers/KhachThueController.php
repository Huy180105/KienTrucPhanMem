<?php

namespace App\Http\Controllers;

use App\Services\DichVuFactory;
use Illuminate\Http\Request;

class KhachThueController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = DichVuFactory::make('khach');
    }

    public function index()
    {
        return response()->json($this->service->all());
    }

    public function show($id)
    {
        return response()->json($this->service->find($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hoTen' => 'required|string',
            'cccd' => 'required|string|unique:khach_thues,cccd',
            'sdt' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'email' => 'required|email',
            'gioiTinh' => 'required|string',
            'ngaySinh' => 'required|date|before:today',
            'queQuan' => 'required|string',
        ], [
            'sdt.regex' => 'Số điện thoại phải chứa đúng 10 chữ số.',
            'ngaySinh.before' => 'Ngày sinh phải nhỏ hơn ngày hiện tại.'
        ]);

        try {
            $tenant = $this->service->create($validated);
            return response()->json($tenant, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Số CCCD này đã tồn tại trong hệ thống.',
                    'errors' => ['cccd' => ['Số CCCD này đã tồn tại trong hệ thống.']]
                ], 422);
            }
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'hoTen' => 'sometimes|required|string',
            'cccd' => 'sometimes|required|string|unique:khach_thues,cccd,' . $id . ',maKhach',
            'sdt' => ['sometimes', 'required', 'string', 'regex:/^[0-9]{10}$/'],
            'email' => 'sometimes|required|email',
            'gioiTinh' => 'sometimes|required|string',
            'ngaySinh' => 'sometimes|required|date|before:today',
            'queQuan' => 'sometimes|required|string',
        ], [
            'sdt.regex' => 'Số điện thoại phải chứa đúng 10 chữ số.',
            'ngaySinh.before' => 'Ngày sinh phải nhỏ hơn ngày hiện tại.'
        ]);

        try {
            $tenant = $this->service->update($id, $validated);
            return response()->json($tenant);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'message' => 'Số CCCD này đã tồn tại trong hệ thống.',
                    'errors' => ['cccd' => ['Số CCCD này đã tồn tại trong hệ thống.']]
                ], 422);
            }
            throw $e;
        }
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');
        return response()->json($this->service->search($keyword));
    }
}
