<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users.email'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset password telah dikirim'
            ]);
        }
        return response()->json([
            'message' => 'Gagal mengirim link reset.'
        ],400);
    }

    public function reset(Request $request) 
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmend|min:8',
        ]);

        $status = Password::reset($request->all(), function(User $user, string $password){
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });
        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password Berhasil direset.'
            ]);
        }
        return response()->json([
            'message' => 'Token Tidak Valid.'
        ],400);
    }
}
