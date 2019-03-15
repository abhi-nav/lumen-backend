<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserDetail;
use App\Events\Referred;
use Illuminate\Http\Request;
use App\Transformers\UserReferralTransformer;
use Dingo\Api\Exception\StoreResourceFailedException as Error;
use Auth;
use Validator;

class UserReferralController extends Controller
{
   
   public function getReferrals(Request $request) 
   {
        // Auth error
        if(!$user = Auth::user()) {
            return $this->response->errorNotFound();
        }
        // $userReferrals = (new UserReferralTransformer)->transform($user->referrals);
        return $this->response->collection($user->referrals, new UserReferralTransformer);
   }

   public function assignUserReferral(Request $request)
    {
        // Auth error
        if(!$user = Auth::user()) {
            return $this->response->errorNotFound();
        }

        // validation error
        $validator = $this->validateEmailInput($request->all());

        if($validator->fails()) {
            throw new Error('Referral Failed',  $validator->errors());
        }

        // user already exist
        if(User::where('email', $request->input('email'))->first()) {
            return $this->response->error("{$request->input('email')} email is already a registered user", 400);
        }

        // already exist(referred_to) error for this user
        $referred_to = Referral::where('referred_to', $request->input('email'))
            ->where('referred_by', $user->id)
            ->first();

        if($referred_to) {
            return $this->response->error("You have already referred {$request->input('email')}", 400);
        }

        //save
        $referral = $user->referrals()->create(['referred_to' => $request->input('email')]);

        // send referal email
        event(new Referred($user, $request->input('email')));

        return [
            'message' => "{$request->input('email')} has been referred successfully",
            'referral' => (new UserReferralTransformer)->transform($referral),
            'email_sent' => true
        ];
    }

    public function validateEmailInput($emailInput) 
    {
        $rules = [
            'email' => 'required|email'
        ];

        return Validator::make($emailInput, $rules);
    }
}