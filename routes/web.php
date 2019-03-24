<?php
$api = app('Dingo\Api\Routing\Router');

$apiGroup = [
	'prefix' => 'v1',
	'namespace' => 'App\Http\Controllers',
];

$middleware = [
	'middleware' => ['api.throttle'], 
	'limit' => 100, 
	'expires' => 3
];

$api->version('v1', $middleware, function ($api) use($apiGroup) {

	$api->group($apiGroup, function ($api) {

		// for local testing (temporary)
		$api->get('/get-token-for-testing', function() {
			return ['access_token' =>  \Auth::login(App\Models\User::find(1))];
		});

		$api->post('/register', 'User\UserController@register');

		$api->post('/validate-user', 'User\UserController@validateUser');

		$api->post('/forgot-password', 'User\UserController@sendResetCodeToEmail');

		$api->post('/reset-password', 'User\UserController@changePassword');

		$api->post('/referral', 'User\UserReferralController@assignNonLoggedInUserReferral');

		// protected route for user
		$api->group(['middleware' => 'jwt.api'], function ($api) {
			// USER DETAILS 
			$api->get('/user/details', 'User\UserDetailController@get');
			$api->post('/user/details', 'User\UserDetailController@store');

			// USER REFERRAL
			$api->get('/user/referrals', 'User\UserReferralController@getReferrals');
			$api->post('/user/referral', 'User\UserReferralController@assignUserReferral');
			
		});
    });
});

