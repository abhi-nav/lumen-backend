<?php

namespace App\Listeners;

use App\Events\Referred;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ReferralMail;
use Illuminate\Support\Facades\Mail;

class MailReferralLink
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Referred  $event
     * @return void
     */
    public function handle(Referred $event)
    {
        return Mail::to($event->referred_to)->send(new ReferralMail($event->user, $event->referred_to));
    }
}
