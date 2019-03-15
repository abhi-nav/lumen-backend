<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Referral;
use Carbon\Carbon;

class UserReferralTransformer extends TransformerAbstract
{
    public function transform(Referral $referral)
    {
        return [
            'referred_to' => $referral->referred_to,
            'status' => $referral->is_registered ? 'Success': 'Pending',
            'is_registered' => $referral->is_registered ? true : false,
            'added' => Carbon::parse($referral->created_at)->diffForHumans(Carbon::now())
        ];
    }
}