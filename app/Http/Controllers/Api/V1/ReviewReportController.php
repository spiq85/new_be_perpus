<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewReport;
use Illuminate\Support\Facades\Auth;

class ReviewReportController extends Controller
{
    public function store(Request $request, Review $review)
    {
        ReviewReport::create([
            'id_review' => $review->id_review,
            'id_user' => Auth::id(),
            'reason' => $request->reason,
        ]);
        return response()->json([
            'message' => 'Laporan Berhasil Dikirim.'
        ]);
    }
}
