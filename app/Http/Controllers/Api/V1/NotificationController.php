<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index()
    {
        $userId = Auth::user()?->id_user;

        if (!$userId) {
            return response()->json([]);
        }

        // Ambil notifikasi dari database notification table
        $notifications = Notification::where('id_user', $userId)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        // Jika belum ada notifikasi dari database → fallback history peminjaman
        if ($notifications->isEmpty()) {
            $loans = Loan::where('id_user', $userId)
                ->with('book')
                ->orderByDesc('updated_at')
                ->take(20)
                ->get();

            $data = $loans->map(function ($loan) {
                $status = $loan->status_peminjaman;
                $title = $loan->book?->title ?? 'Buku';

                $message = match ($status) {
                    'pending' => "Pengajuan peminjaman '{$title}' dikirim.",
                    'siap_diambil' => "Buku '{$title}' siap diambil di perpustakaan.",
                    'ditolak' => "Pengajuan peminjaman '{$title}' ditolak petugas.",
                    'dipinjam' => "Buku '{$title}' berhasil kamu pinjam.",
                    'menunggu_validasi_pengembalian' => "Permintaan pengembalian '{$title}' sedang menunggu validasi petugas.",
                    'dikembalikan' => "Pengembalian '{$title}' selesai. Terima kasih 🙌",
                    'rusak' => "Pengembalian '{$title}' ditandai RUSAK oleh petugas.",
                    'hilang' => "Pengembalian '{$title}' ditandai HILANG oleh petugas.",
                    default => "Status '{$title}' sekarang: {$status}",
                };

                return [
                    'id' => $loan->id_loan,
                    'id_loan' => $loan->id_loan,
                    'status' => $status,
                    'book' => $title,
                    'message' => $message,
                    'is_read' => true,
                    'type' => 'loan',
                    'updated_at' => $loan->updated_at,
                ];
            });

            return response()->json($data);
        }

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $userId = Auth::user()?->id_user;

        // User hanya boleh membaca notifikasi miliknya
        if ($notification->id_user !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json($notification);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = Auth::user()?->id_user;

        Notification::where('id_user', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $userId = Auth::user()?->id_user;

        if ($notification->id_user !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Count unread notif for authenticated user
     */
    public function unreadCount()
    {
        $userId = Auth::user()?->id_user;

        $count = Notification::where('id_user', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function adminIndex(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json($notifications);
    }

    public function adminMarkAsRead(Notification $notification)
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'Notification read']);
    }

    public function staffIndex(Request $request)
    {
        $user = $request->user();

        \Log::info("Petugas Login: " . $user->username . "(ID: {$user->id_user} )");

        $notifications = Notification::where('id_user', $user->id_user)
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        \Log::info("Notifikasi untuk petugas {$user->username}" . $notifications->count());

        return response()->json($notifications);
    }
}
