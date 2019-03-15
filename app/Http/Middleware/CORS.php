<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $headers = [
            'Access-Control-Allow-Origin' =>  '*',
            'Access-Control-Allow-Methods' => 'OPTIONS, POST, GET',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization'
        ];

        foreach ($headers as $key => $value) {
             $response->headers->set($key, $value); 
        }

        if ($request->getMethod() == "OPTIONS") {
            return $response->setStatusCode(200)->setContent('OK');
        }

        return $response;
    }
}
