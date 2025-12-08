<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LoanRequested;
use App\Events\LoanStatusUpdated;
use App\Events\ReviewReportCreated;
use App\Listeners\SendLoanNotificationToStaff;
use App\Listeners\SendLoanStatusNotificationToUser;
use App\Listeners\NotifyAdminAboutReportedReview;
use App\Listeners\SendReturnRequestNotificationToStaff;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LoanRequested::class => [
            SendLoanNotificationToStaff::class,
        ],
        LoanStatusUpdated::class => [
            SendLoanStatusNotificationToUser::class,
            SendReturnRequestNotificationToStaff::class
        ],
        ReviewReportCreated::class => [
            NotifyAdminAboutReportedReview::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
