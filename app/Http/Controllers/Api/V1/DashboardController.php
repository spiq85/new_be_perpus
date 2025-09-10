<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('admin')) {
            $data = $this->getAdminDashboardData();
        } elseif ($user->hasRole('petugas')) {
            $data = $this->getPetugasDashboardData();
        } else {
            $data = $this->getUserDashboardData();
        }

        return response()->json($data);
    }

    private function getAdminDashboardData()
    {
        return [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeLoans' => Loan::where('status_peminjaman', 'dipinjam')->count(),
            'pendingLoans' => Loan::where('status_peminjaman', 'pending')->count(),
            'overdue' => Loan::where('status_peminjaman', 'terlambat')->count(),
            'todayReturns' => Loan::whereDate('tanggal_peminjaman', now())->count(),
        ];
    }

    private function getPetugasDashboardData()
    {
        return [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeLoans' => Loan::where('status_peminjaman', 'dipinjam')->count(),
            'pendingLoans' => Loan::where('status_peminjaman', 'pending')->count(),
            'overdue' => Loan::where('status_peminjaman', 'terlambat')->count(),
            'todayReturns' => Loan::whereDate('tanggal_peminjaman', now())->count(),
        ];
    }

    private function getUserDashboardData()
    {
        $user = Auth::user();
        return [
            'message' => 'Welcome, ' . $user->username . '!',
            'active_loans' => $user->loans()->where('status_peminjaman', 'dipinjam')->count()
        ];
    }
}
