<?php

namespace App\Listeners;

use App\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Referral;
use App\Models\ReferralCode;

class CheckReferral
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
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $request = app('request');

        if($request->referral_code) {
            $referral = ReferralCode::where('code', $request->referral_code)
                ->first();
            if($referral) {
                Referral::updateOrCreate(
                    ['referred_by' => $referral->user->id, 'referred_to' => $event->user->id],
                    ['is_registered' => 1]
                );
            }
            return true;
        }
    }
}
