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
use App\Events\PasswordReset;
use Carbon\Carbon;

class UserController extends Controller
{
    // token valid time in minutes
    protected $tokenValidTime = 60;

    public function register(Request $request) 
    {
        $message = 'Registration Successful';
        $isNew = true;
        $token = null;

        $validator = $this->validateLoginInput($request->all());

        if($validator->fails()) {
            throw new Error('Registration Failed', $validator->errors());
        }
        
        $isNew = false;

        if(!$user = $this->getUserByEmail($request->email)) {
            $user = User::create([
                'email' => $request->input('email'),
                'password' => app('hash')->make($request->input('password'))
            ]);
            
            event(new Registered($user));

        } else {
            // dd(app('hash')->check($request->password, $user->password));
            if(! app('hash')->check($request->input('password'), $user->password)) {
                throw new Error('Sign in Failed', $validator->errors()->add('password', 'Your password is incorrect'));
            }
            
            $isNew = false;
            if($user->verified) {
                $token = $this->getUserAccessToken($user);
                $message = 'Login Successful';
            }
        }
        if(!$user->verified) {
            VerifyUser::updateOrCreate(
                ['user_id' => $user->id],
                ['token' => random_int(100000, 999999)]
            );
            event(new LoginRequested($user));
        }
        
        return [
            'user' => (new UserTransformer())->transform($user),
            'isNew' => $isNew,
            'verified' => $user->verified,
            'message' => $message,
            'access_token' => $token
        ];

        $response['user'] = (new UserTransformer())->transform($user);
        $response['isNew'] = $isNew;
        $response['message'] = 'Registration Successful';

        return $response;
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

        // verified response
        return [
            'message' => 'Your email has been successfully verified', 
            'user' => (new UserTransformer())->transform($user),
            'access_token' => $this->getUserAccessToken($user)
        ];
    }

    public function getUserAccessToken(User $user)
    {
        return JWTAuth::fromUser($user, [
            'ip'=>app('request')->ip()
        ]);
    }

    public function sendResetCodeToEmail()
    {
        $request = app('request');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
        ]);

        if($validator->fails()) {
            throw new Error('Error sending password reset code', $validator->errors());
        }
        $user = $this->getUserByEmail($request->input('email'));

        VerifyUser::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => random_int(100000, 999999)]
        );

        event(new PasswordReset($user));

        return [
            'status' => true,
            'email' => $request->input('email'),
            'message' => 'Password Reset code sent'
        ];
    }

    public function changePassword()
    {
        $request = app('request');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'resetCode' => 'required|string|min:6'
        ]);

        if($validator->fails()) {
            throw new Error('Error Resetting Password', $validator->errors());
        }

        $user = $this->getUserByEmail($request->input('email'));

        // token valid
        if($user->verifyUser->token !== $request->input('resetCode')) {
            throw new Error('Error Resetting Password', $validator->errors()->add('resetCode', 'Reset Code did not match. Please check the code with your email'));

            // return $this->response->error('Incorrect reset token', 401);
        }

        // token expiration check
        if(Carbon::parse($user->verifyUser->updated_at)->addMinutes($this->tokenValidTime)->lt(Carbon::now())) {
            return $this->response->error('Token has Expired', 401);
        }

        $user->update([
            'password' => app('hash')->make($request->input('password')),
            'verified' => true
        ]);

        return [
            'status' => true,
            'email' => $request->input('email'),
            'message' => 'Password Reset Successfully'
        ];
    }

    public function validateEmailInput($emailInput) 
    {
        $rules = [
            'email' => 'required|email'
        ];

        return Validator::make($emailInput, $rules);
    }

    public function validateLoginInput($emailInput) 
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:6|'
        ];

        return Validator::make($emailInput, $rules);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
