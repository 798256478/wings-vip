<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CardCreated' => [
            'App\Listeners\CardCreatedListener'
        ],
        'App\Events\ProfileFilled' => [
            'App\Listeners\ProfileFilledListener'
        ],
        'App\Events\TicketVerified' => [
            'App\Listeners\TicketVerifiedListener'
        ],
        'App\Events\CodeRedeemed' => [
            'App\Listeners\CodeRedeemedListener'
        ],
        'App\Events\OrderCompleted' => [
            'App\Listeners\OrderCompletedListener'
        ],
        'App\Events\UseProperty' => [
            'App\Listeners\UsePropertyListener'
        ],
        'App\Events\ProgressChanged' => [
            'App\Listeners\ProgressChangedListener'
        ],
        'App\Events\SignEvent' => [
            'App\Listeners\SignListener'
        ],
    ];
    
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        //'App\Listeners\CardEventListener',
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
