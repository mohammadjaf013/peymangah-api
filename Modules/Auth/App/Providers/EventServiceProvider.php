<?php
namespace Modules\Auth\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Auth\App\Events\OtpEvent;
use Modules\Auth\App\Listeners\OtpListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OtpEvent::class => [
            OtpListener::class,
        ],
    ];
}
