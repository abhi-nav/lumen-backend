<?php

namespace App\Events;

use App\Models\User;

class Registered extends Event
{
    public $user;

    public function __construct(User $user)
    {
    	$this->user = $user;	    
    }

}
