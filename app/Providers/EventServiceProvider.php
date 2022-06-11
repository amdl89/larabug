<?php

namespace App\Providers;

use App\Events\TicketUpdated;
use App\Listeners\CreateProfileForUser;
use App\Listeners\CreateTicketChangeLogs;
use App\Models\Ticket;
use App\Observers\TicketObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TicketUpdated::class => [
            CreateTicketChangeLogs::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Ticket::observe(TicketObserver::class);
    }
}
