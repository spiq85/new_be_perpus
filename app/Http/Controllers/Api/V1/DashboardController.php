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
        return array_merge($this->getSharedDashboardData(), [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeLoans' => Loan::where('status_peminjaman', 'dipinjam')->count(),
            'pendingLoans' => Loan::where('status_peminjaman', 'pending')->count(),
            'overdue' => Loan::where('status_peminjaman', 'terlambat')->count(),
            'todayReturns' => Loan::whereDate('tanggal_peminjaman', now())->count(),
        ]);
    }

    private function getPetugasDashboardData()
    {
        return array_merge($this->getSharedDashboardData(), [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeLoans' => Loan::where('status_peminjaman', 'dipinjam')->count(),
            'pendingLoans' => Loan::where('status_peminjaman', 'pending')->count(),
            'overdue' => Loan::where('status_peminjaman', 'terlambat')->count(),
            'todayReturns' => Loan::whereDate('tanggal_peminjaman', now())->count(),
        ]);
    }

    private function getSharedDashboardData()
    {
        // Loan Trends (by month)
        $loanTrends = Loan::selectRaw('MONTH(tanggal_peminjaman) as month, COUNT(*) as loans')
            ->whereYear('tanggal_peminjaman', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $returns = Loan::whereMonth('tanggal_pengembalian', $item->month)
                    ->whereYear('tanggal_pengembalian', now()->year)
                    ->count();
                return [
                    'period' => date("M", mktime(0, 0, 0, $item->month, 1)),
                    'loans' => $item->loans,
                    'returns' => $returns,
                ];
            });

        // Distribusi Kategori Buku
        $categoryDistribution = Book::selectRaw('categories.name, COUNT(books.id_book) as total')
            ->join('categories', 'books.id_category', '=', 'categories.id_category')
            ->groupBy('categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'value' => $item->total,
                    'color' => '#' . substr(md5($item->name), 0, 6),
                ];
            });

        // Aktivitas Harian (by day name)
        $dailyActivity = Loan::selectRaw('DAYNAME(tanggal_peminjaman) as day, COUNT(*) as total')
            ->whereYear('tanggal_peminjaman', now()->year)
            ->groupBy('day')
            ->get()
            ->map(function ($item) {
                return [
                    'day' => $item->day,
                    'morning' => rand(5, 15),
                    'afternoon' => rand(5, 15),
                    'evening' => rand(5, 15),
                ];
            });

        return [
            'loanTrends' => $loanTrends,
            'categoryDistribution' => $categoryDistribution,
            'dailyActivity' => $dailyActivity,
        ];
    }

    public function userGrowth(Request $request)
    {
        $range = $request->get('range', 'month');

        if ($range === 'week') {
            $data = User::selectRaw('WEEK(created_at) as week, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('week')
                ->orderBy('week')
                ->get()
                ->map(fn($item) => [
                    'period' => 'Minggu ' . $item->week,
                    'users' => $item->total,
                ]);
        } elseif ($range === 'year') {
            $data = User::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->map(fn($item) => [
                    'period' => $item->year,
                    'users' => $item->total,
                ]);
        } else {
            $data = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($item) => [
                    'period' => date("M", mktime(0, 0, 0, $item->month, 1)),
                    'users' => $item->total,
                ]);
        }

        return response()->json($data);
    }

    public function topBooks()
    {
        $data = Loan::selectRaw("books.title, COUNT(loans.id_loan) as total")
            ->join("books", 'loans.id_book', '=', 'books.id_book')
            ->groupBy('books.id_book', 'books.title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    public function topUsers()
    {
        $data = Loan::selectRaw("users.username, COUNT(loans.id_loan) as total")
            ->join("users", 'loans.id_user', '=', 'users.id_user')
            ->groupBy('users.id_user', 'users.username')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json($data);
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
