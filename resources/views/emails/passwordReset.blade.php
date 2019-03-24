Hello {{$user->email}},

<p> If you'd like to reset your password for Easy Solution please use Reset Code below: </p>

<h2> {{$user->verifyUser->token}} </h2>

<p> If you think that you shouldn't have received this email, you can safely ignore it. </p>

Thanks