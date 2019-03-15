<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateJwtToken
{
    public function handle($request, Closure $next, $guard = null)
    {

        try {
            // dd(JAuth::parseToken()->getIdentifier());
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'user_not_found', 'status_code'=>404], 404);
            }

            // ip filtration in future
            // $clientIp = JWTAuth::getPayload(JWTAuth::getToken())->get('ip');
            
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // error code 401
            return response()->json(['message' => 'token_expired', 'status_code'=>401], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'token_invalid', 'status_code'=>401], 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['message' => 'token_absent', 'status_code'=>401], 401);

        }

        return $next($request);
    }
}
