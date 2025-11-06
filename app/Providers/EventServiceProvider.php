<?php

namespace App\Providers;

use App\Modules\Campaign\EventListeners\UpdateCampaignTotal;
use App\Modules\Campaign\Events\CampaignGoalReachedEvent;
use App\Modules\Donation\Events\DonationStatusEvent;
use App\Modules\Payment\EventListeners\HandlePaymentCompleted;
use App\Modules\Payment\EventListeners\HandlePaymentFailed;
use App\Modules\Payment\Events\PaymentCompletedEvent;
use App\Modules\Payment\Events\PaymentFailedEvent;
use App\Modules\User\EventListeners\SendDonationNotification;
use App\Modules\User\EventListeners\SendDonationReceipt;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DonationStatusEvent::class => [
            UpdateCampaignTotal::class,
            SendDonationNotification::class,
            SendDonationReceipt::class,
        ],
        CampaignGoalReachedEvent::class => [
            // Add listeners for goal reached (e.g., notifications to admins/creators)
        ],
        PaymentCompletedEvent::class => [
            HandlePaymentCompleted::class,
        ],
        PaymentFailedEvent::class => [
            HandlePaymentFailed::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool {
        return false;
    }
}
