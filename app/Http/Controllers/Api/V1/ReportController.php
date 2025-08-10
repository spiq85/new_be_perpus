<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Generate Laporan Peminjaman & Pengembalian
    public function generateLoanReport(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4'
        ]);

        $loans = Loan::with(['book', 'user'])
            ->whereYear('tanggal_peminjaman', $request->year)
            ->whereMonth('tanggal_peminjaman', $request->month)
            ->get();

            return response()->json($loans);
    }

    public function generateBookReport(Request $request)
    {
        $books = Book::query();

        if ($request->input('status') === 'out_of_stock') {
            $books->where('stock', '=', 0);
        }
        return response()->json($books->get());
    }
}
