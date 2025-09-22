<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Loan $loan;

    /**
     * Create a new event instance.
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan->load(['book', 'user']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new \Illuminate\Broadcasting\PrivateChannel('user.'.$this->loan->id_user),
        ];
    }

    public function broadcastAs()
    {
        return 'loan.status.updated';
    }

    public function broadcastWith()
    {
        return [
            'id_loan' => $this->loan->id_loan,
            'status' => $this->loan->status_peminjaman,
            'book' => $this->loan->book->title,
            'user' => $this->loan->user->username,
        ];
    }
}
