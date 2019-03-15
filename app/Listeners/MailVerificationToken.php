<?php

namespace App\Listeners;

use App\Events\LoginRequested;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

class MailVerificationToken
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
     * @param  LoginRequested  $event
     * @return void
     */
    public function handle(LoginRequested $event)
    {
        return Mail::to($event->user)->send(new VerificationMail($event->user));
    }
}
