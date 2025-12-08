<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Loan;
use App\Models\Collection;
use App\Models\Review;

class ProfileController extends Controller
{
    /**
     * Display user profile with stats
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Hitung stats
        $totalDenda = Loan::where('id_user', $user->id_user)->sum('denda');
        $totalPinjam = Loan::where('id_user', $user->id_user)->count();
        $totalCollection = Collection::where('id_user', $user->id_user)->count();
        $totalReview = Review::where('id_user', $user->id_user)->count();

        $profile = [
            'id_user' => $user->id_user,
            'username' => $user->username,
            'email' => $user->email,
            'nama_lengkap' => $user->nama_lengkap,
            'alamat' => $user->alamat,
            'role' => $user->role,
            'banned_at' => $user->banned_at,
            'stats' => [
                'total_denda' => $totalDenda,
                'total_pinjam' => $totalPinjam,
                'total_collection' => $totalCollection,
                'total_review' => $totalReview,
            ],
        ];

        return response()->json($profile);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama salah.'],
            ]);
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}