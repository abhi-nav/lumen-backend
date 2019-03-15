<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Dingo\Api\Exception\StoreResourceFailedException as Error;
use Auth;
use Validator;

class UserDetailController extends Controller
{
    // token valid time in minutes
    protected $tokenValidTime = 60;

    public function store(Request $request) 
    {
        // Auth
        if(!$user = Auth::user()) {
            return $this->response->errorNotFound();
        }

        $validator = $this->validateInput($request->all());

        if($validator->fails()) {
            throw new Error('Profile Update Failed', $validator->errors());
        }

        if($user->userDetail) {
            $user->userDetail()->update($this->getStoreData($request));
        } else {
            return $user->userDetail()->create($this->getStoreData($request));
        }

        return ['message' => 'Profile Updated Successfully', 'user_detail' => $user->userDetail];
    }

    public function get() 
    {
        if(!$user = Auth::user()) {
            return $this->response->errorNotFound();
        } 

        return $user->userDetail;
    }

    public function validateInput($inputs) 
    {
        $rules = [
            'first_name' => 'required',            
            'last_name' => 'required',            
            'address1' => 'required',            
            'address2' => 'required',            
            'occupation' => 'required',            
            'mobile' => 'required|numeric',            
            'marrital_status' => 'required'
        ];      

        return Validator::make($inputs, $rules);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function getStoreData(Request $request) 
    {
        return $request->only([
            'first_name',
            'last_name',
            'address1',
            'address2',
            'occupation',
            'mobile',
            'marrital_status'
        ]);

    }

}
