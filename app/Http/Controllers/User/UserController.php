<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerifyUser;
use App\Transformers\UserTransformer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Dingo\Api\Exception\StoreResourceFailedException as Error;
use App\Events\Registered;
use App\Events\LoginRequested;
use Carbon\Carbon;

class UserController extends Controller
{
    // token valid time in minutes
    protected $tokenValidTime = 60;

    public function register(Request $request) 
    {

        $validator = $this->validateEmailInput($request->all());

        if($validator->fails()) {
            throw new Error('Registration Failed', $validator->errors());
        }
        
        $isNew = false;

        if(!$user = $this->getUserByEmail($request->email)) {
            $user = User::create($request->only('email'));
            $isNew = true;
            event(new Registered($user));
        }

        $verifyUser = VerifyUser::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => random_int(100000, 999999)]
        );
        
        event(new LoginRequested($user));

        $response['user'] = (new UserTransformer())->transform($user);
        $response['isNew'] = $isNew;
        $response['message'] = 'Registration Successful';

        // event(new Registered($user));

        // $customClaims = [
        //     'ip'=>$request->ip(),
        // ];
        
        // $response['token'] = JWTAuth::fromUser($user, $customClaims);

        return $response;

        // return $this->response->item($user, new UserTransformer)->addMeta('token', $token);
    	// return Auth::login(User::first());
    	// return config('auth');
    	// return User::all();
    	// return $this->response->paginator(User::paginate(3), new UserTransformer);
    	// return $this->response->errorBadRequest();
    	// return $this->response->error('This is an error.', 404);
    	// return $this->response->noContent();
    }

    public function validateUser(Request $request) {
        // token exist
        if(!$request->input('token')) {
            return $this->response->errorBadRequest('No token provided');
        }

        if(!$request->input('email') || !$user = $this->getUserByEmail($request->input('email')) ) {
            return $this->response->errorBadRequest('User does not exist');
        }

        // token valid
        if($user->verifyUser->token !== $request->input('token')) {
            return $this->response->error('Wrong Token', 401);
        }

        // token expiration check
        if(Carbon::parse($user->verifyUser->updated_at)->addMinutes($this->tokenValidTime)->lt(Carbon::now())) {
            return $this->response->error('Token has Expired', 401);
        }

        // success user verified
        if(!$user->verified) {
            $user->update(['verified'=>true]);
        }

        // issue token
        $token = JWTAuth::fromUser($user, [
            'ip'=>$request->ip()
        ]);

        // verified response
        return [
            'message' => 'verified', 
            'user' => (new UserTransformer())->transform($user),
            'access_token' => $token
        ];
    }

    public function validateEmailInput($emailInput) 
    {
        $rules = [
            'email' => 'required|email'
        ];

        return Validator::make($emailInput, $rules);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
