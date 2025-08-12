<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan Semua Data User
    public function index()
    {
        return response()->json(User::with('roles')->paginate(15));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nama_lengkap' => 'required|string',
            'alamat' => 'required|string',
            'role' => 'required|in:petugas,admin',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        $user->assignRole($request->role);
        return response()->json([
            'message' => 'User Berhasil Dibuat.' , 'data' => $user
        ], 201);
    }

    public function destroy(User $user)
    {
        if (auth()->id_user === $user->id_user) {
            return response()->json([
                'message' => 'Tidak Bisa Menghapus Akun Sendiri'
            ]);
        }
        $user->delete();

        return response()->json([
            'message' => 'User Berhasil Dihapus'
        ]);
    }

    public function ban(User $user)
    {
        $user0>update([
            'banned_at' => now()
        ]);
        return response()->json([
            'message' => 'User berhasil di banned'
        ]);
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);
        return response()->json([
            'message' => 'User berhasil di unbanned'
        ]);
    }
}
