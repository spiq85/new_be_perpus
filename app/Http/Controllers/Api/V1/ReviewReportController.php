<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewReport;
use App\Events\ReviewReportCreated;
use Illuminate\Support\Facades\Auth;

class ReviewReportController extends Controller
{
    public function index()
    {
        $reports = ReviewReport::with([
            'review.book',
            'review.user',
            'user'
        ])
        ->latest()
        ->get();

        return response()->json($reports);
    }

    public function store(Request $request, Review $review)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $report = ReviewReport::create([
            'id_review' => $review->id_review,
            'id_user' => Auth::id(),
            'reason' => $request->reason,
            'reported_at' => now()->toDateString(),
        ]);

        // Trigger event untuk notify admin
        ReviewReportCreated::dispatch($report);

        return response()->json([
            'message' => 'Laporan Berhasil Dikirim.'
        ], 201);
    }

    public function destroy(ReviewReport $report)
    {
        $report->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus.'
        ], 200);
    }

    /**
     * Delete review beserta semua laporannya
     */
    public function deleteReview(Review $review)
    {
        $user = Auth::user();
        
        // Hanya admin yang bisa delete review
        if (!$user->hasRole('admin')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus review.'
            ], 403);
        }

        // Hapus semua laporan terkait review ini (cascade)
        ReviewReport::where('id_review', $review->id_review)->delete();
        
        // Kemudian hapus review-nya
        $review->delete();

        return response()->json([
            'message' => 'Review dan semua laporannya berhasil dihapus.'
        ], 200);
    }
}
