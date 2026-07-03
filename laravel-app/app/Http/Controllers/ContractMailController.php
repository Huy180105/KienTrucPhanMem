<?php

namespace App\Http\Controllers;

use App\Mail\ContractExpirationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContractMailController extends Controller
{
    public function sendReminder(Request $request, $id)
    {
        // Nếu không truyền dữ liệu body, tự động truy vấn CSDL theo $id
        if (!$request->has('contract')) {
            $contractModel = \App\Models\HopDong::with('khachThue')->find($id);
            if (!$contractModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hợp đồng với mã ' . $id
                ], 404);
            }

            $contract = $contractModel->toArray();
            $tenant = $contractModel->khachThue ? $contractModel->khachThue->toArray() : [];
            $email = $contractModel->khachThue && $contractModel->khachThue->sdt 
                ? $contractModel->khachThue->sdt . '@example.com' 
                : 'khachthue@gmail.com';
        } else {
            // Trường hợp frontend truyền sẵn payload
            $validated = $request->validate([
                'contract' => 'required|array',
                'tenant' => 'required|array',
                'email' => 'required|email'
            ]);

            $contract = $validated['contract'];
            $tenant = $validated['tenant'];
            $email = $validated['email'];
        }

        // Gửi email nhắc nhở
        Mail::to($email)->send(new ContractExpirationMail($contract, $tenant));

        return response()->json([
            'success' => true,
            'message' => 'Email notification sent successfully!',
            'email_sent_to' => $email,
            'contract_id' => $id
        ]);
    }
}
