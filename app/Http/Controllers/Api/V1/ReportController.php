<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ==============================
    // PETUGAS: Laporan Peminjaman
    // ==============================
    public function generateLoanReport(Request $request)
    {
        $type = $request->query('type', 'bulanan');
        $today = Carbon::today();

        $query = Loan::with(['book', 'user']);

        if ($type === 'harian') {
            $query->whereDate('tanggal_peminjaman', $today);
        } elseif ($type === 'bulanan') {
            $query->whereYear('tanggal_peminjaman', $today->year)
                  ->whereMonth('tanggal_peminjaman', $today->month);
        } elseif ($type === 'tahunan') {
            $query->whereYear('tanggal_peminjaman', $today->year);
        }

        $loans = $query->get();

        $pdf = Pdf::loadView('reports.loans', [
            'loans' => $loans,
            'type'  => ucfirst($type),
            'date'  => $today->toDateString(),
        ]);

        return $pdf->download("laporan-peminjaman-{$type}.pdf");
    }

    // ==============================
    // PETUGAS: Laporan Denda
    // ==============================
    public function generateFineReport(Request $request)
    {
        $type = $request->query('type', 'bulanan'); 
        $today = Carbon::today();

        $query = Loan::with(['book', 'user'])
            ->where('denda', '>', 0);

        if ($type === 'bulanan') {
            $query->whereYear('updated_at', $today->year)
                  ->whereMonth('updated_at', $today->month);
        }

        if ($type === 'keterlambatan') {
            $query->where('status_peminjaman', 'terlambat');
        } elseif ($type === 'rusak') {
            $query->where('status_peminjaman', 'rusak');
        } elseif ($type === 'hilang') {
            $query->where('status_peminjaman', 'hilang');
        }

        $loans = $query->get();

        $pdf = Pdf::loadView('reports.fines', [
            'loans' => $loans,
            'type'  => ucfirst($type),
            'date'  => $today->toDateString(),
        ]);

        return $pdf->download("laporan-denda-{$type}.pdf");
    }

    // ==============================
    // ADMIN: Laporan Buku
    // ==============================
    public function generateBookReport(Request $request)
    {
        $status = $request->input('status');

        $books = Book::with(['categories'])->withCount('loans');

        if ($status === 'out_of_stock') {
            $books->where('stock', '=', 0);
        }

        $pdf = Pdf::loadView('reports.books', [
            'books'  => $books->get(),
            'status' => $status,
            'date'   => now()->toDateString(),
        ]);

        return $pdf->download("laporan-inventori-buku.pdf");
    }

    public function generatePopularBooksReport(Request $request) 
    {
        $books = Book::withAvg('reviews', 'rating')
                        ->withCount('reviews')
                        ->orderByDesc('reviews_avg_rating')
                        ->orderByDesc('reviews_count')
                        ->take(10)
                        ->get();

        $pdf = Pdf::loadView('reports.popular_books', [
            'books' => $books,
        ]);

        return $pdf->download("laporan-buku-populer.pdf");
    }

    public function generateCategoryStatsReport(Request $request)
    {
        $categories = Category::withCount('books')
                        ->with(['books' => function($q) {
                            $q->withCount('loans');
                        }])
                        ->get()
                        ->map(function ($cat){
                            return [
                                'category_name' => $cat->category_name,
                                'total_books'   => $cat->books_count,
                                'total_loans'   => $cat->books->sum('loans_count'),
                            ];
                        });

        $pdf = Pdf::loadView('reports.category_stats', [
            'categories' => $categories,
        ]);

        return $pdf->download("laporan-statistik-kategori-buku.pdf");
    }
}
