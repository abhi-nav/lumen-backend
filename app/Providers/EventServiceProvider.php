<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\Registered::class => [
            \App\Listeners\SendWelcomeMail::class,
            \App\Listeners\CheckReferral::class
        ],

        \App\Events\LoginRequested::class => [
            \App\Listeners\MailVerificationToken::class
        ],
        
        \App\Events\Referred::class => [
            \App\Listeners\MailReferralLink::class
        ]
    ];
}
