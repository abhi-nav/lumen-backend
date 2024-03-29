<?php

namespace App\Events;

use App\Models\User;

class LoginRequested extends Event
{
    public $user;

    public function __construct(User $user)
    {
    	$this->user = $user;	    
    }

}
