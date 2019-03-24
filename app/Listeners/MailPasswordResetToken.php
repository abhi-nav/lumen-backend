<?php

namespace App\Listeners;

use App\Events\PasswordReset;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

class MailPasswordResetToken
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
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        return Mail::to($event->user)->send(new PasswordResetMail($event->user));
    }
}
