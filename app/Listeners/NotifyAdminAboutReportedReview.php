<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ReviewReportCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotifyAdminAboutReportedReview
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewReportCreated $event): void
    {
        $report = $event->report;

        try {
            // Get all admin users
            $admins = User::where('role','admin')->get();

            foreach ($admins as $admin) {
                // Create notification for each admin
                Notification::create([
                    'id_user' => $admin->id_user,
                    'type' => 'report',
                    'title' => 'Ulasan Dilaporkan',
                    'message' => "Ulasan baru dilaporkan: \"{$report->reason}\"",
                    'data' => [
                        'report_id' => $report->id_report,
                        'review_id' => $report->id_review,
                        'user_id' => $report->id_user,
                    ],
                    'is_read' => false,
                ]);
            }

            Log::info("Notifikasi laporan ulasan dikirim ke {$admins->count()} admin");
        } catch (\Exception $e) {
            Log::error("Error saat create notifikasi report: " . $e->getMessage());
        }
    }
}
