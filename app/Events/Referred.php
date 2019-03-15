<?php

namespace App\Events;

use App\Models\User;

class Referred extends Event
{
    public $user;
    public $referred_to;

    public function __construct(User $user, $referred_to)
    {
    	$this->user = $user;	    
    	$this->referred_to = $referred_to;
    }

}
