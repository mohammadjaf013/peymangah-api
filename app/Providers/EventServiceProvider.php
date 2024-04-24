<?php

namespace App\Providers;

use App\Events\SendSmsActiveReceiptEvent;
use App\Events\SendSmsPreviewEvent;
use App\Events\SendSmsActiveEvent;
use App\Events\SendSmsPreviewReceiptEvent;
use App\Listeners\SendSmsActiveReceiptListener;
use App\Listeners\SendSmsPreviewListener;
use App\Listeners\SendSmsActiveListener;
use App\Listeners\SendSmsReceiptPreviewListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Auth\App\Events\OtpEvent;
use Modules\Auth\App\Listeners\OtpListener;

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
        OtpEvent::class => [
            OtpListener::class,
        ],
        SendSmsPreviewEvent::class => [
            SendSmsPreviewListener::class,
        ],
        SendSmsActiveEvent::class => [
            SendSmsActiveListener::class,
        ],
        SendSmsPreviewReceiptEvent::class => [
            SendSmsReceiptPreviewListener::class,
        ],
        SendSmsActiveReceiptEvent::class => [
            SendSmsActiveReceiptListener::class,
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
