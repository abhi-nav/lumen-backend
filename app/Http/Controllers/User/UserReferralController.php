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
        if(User::where('email', $request->input('referred_to'))->first()) {
            return $this->response->error("{$request->input('referred_to')} email is already a registered user", 400);
        }

        // already exist(referred_to) error for this user
        $referred_to = Referral::where('referred_to', $request->input('referred_to'))
            ->where('referred_by', $user->id)
            ->first();

        if($referred_to) {
            return $this->response->error("You have already referred {$request->input('referred_to')}", 400);
        }

        //save
        $referral = $user->referrals()->create(['referred_to' => $request->input('referred_to')]);

        // send referal email
        event(new Referred($user, $request->input('referred_to')));

        return [
            'message' => "{$request->input('referred_to')} has been referred successfully",
            'referral' => (new UserReferralTransformer)->transform($referral),
            'email_sent' => true
        ];
    }

    public function assignNonLoggedInUserReferral(Request $request)
    {
        // validation error
        $validator = $this->validateNonLoggedInEmailInput($request->all());

        if($validator->fails()) {
            throw new Error('Referral Failed',  $validator->errors());
        }

        // user already exist
        if(User::where('email', $request->input('referred_to'))->first()) {
            return $this->response->error("{$request->input('referred_to')} email is already a registered user", 400);
        }
        
        if(!$user = User::where('email', $request->input('referred_by'))->first()) {
            $user = User::create(['email' => $request->input('referred_by')]);
        }

        // already exist(referred_to) error for this user
        $referred_to = Referral::where('referred_to', $request->input('referred_to'))
            ->where('referred_by', $user->id)
            ->first();

        if($referred_to) {
            return $this->response->error("You have already referred {$request->input('referred_to')}", 400);
        }

        //save
        $referral = $user->referrals()->create(['referred_to' => $request->input('referred_to')]);

        // send referal email
        event(new Referred($user, $request->input('referred_to')));

        return [
            'message' => "{$request->input('referred_to')} has been referred successfully",
            'referral' => (new UserReferralTransformer)->transform($referral),
            'email_sent' => true
        ];
    }

    public function validateEmailInput($emailInput) 
    {
        $rules = [
            'referred_to' => 'required|email'
        ];

        return Validator::make($emailInput, $rules);
    }

    public function validateNonLoggedInEmailInput($emailInput) 
    {
        $rules = [
            'referred_by' => 'required|email',
            'referred_to' => 'required|email'
        ];

        return Validator::make($emailInput, $rules);
    }
}