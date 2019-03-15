Hello {{$referred_to}},

Welcome on Easy Solution,

<h2>You have been successfully refferred by your friend {{$user->email}} </h2>

<p>
	Please click on the below link to signup and enjoy our referral bonus
</p>

<p>
	<a href="{{env('FRONTEND_APP_URL')}}/login?referral_code={{$user->referralCode->code}}">Login</a>
</p> 
