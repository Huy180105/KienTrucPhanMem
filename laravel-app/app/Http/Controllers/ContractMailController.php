<?php

namespace App\Http\Controllers;

use App\Mail\ContractExpirationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContractMailController extends Controller
{
    public function sendReminder(Request $request, $id)
    {
        $validated = $request->validate([
            'contract' => 'required|array',
            'tenant' => 'required|array',
            'email' => 'required|email'
        ]);

        $contract = $validated['contract'];
        $tenant = $validated['tenant'];
        $email = $validated['email'];

        // Send email
        Mail::to($email)->send(new ContractExpirationMail($contract, $tenant));

        return response()->json([
            'success' => true,
            'message' => 'Email notification sent successfully!'
        ]);
    }
}
