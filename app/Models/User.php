<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    
    protected $fillable = [
        'name', 'email', 'password', 'verified'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function referralCode()
    {
        // return relation object if exist else create and return 
        if($relation = $this->_referralCode) {
            return $this->_referralCode();
        }

        try{
            $this->_referralCode()->create(['code' => str_random(40)]);
        } catch (\Exception $e) {
            $this->referralCode();
        }

        return $this->_referralCode();
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referred_by')
            ->orderBy('created_at', 'desc');
    }

    public function _referralCode()
    {
        return $this->hasOne(ReferralCode::class);
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }
}
