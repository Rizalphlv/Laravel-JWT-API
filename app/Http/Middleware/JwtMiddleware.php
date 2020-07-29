<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware
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
        try{
            $user = JWTAuth::parseToken()->authenticate();
        }catch (Exception $ex){
            if($ex instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                return response()->json(['status' => 'Token is Invalid']);

            }
            else if($ex instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return response()->json(['status' => 'Token Expired']);
            }
            else
            {
                return response()->json(['status' => 'Authorization Token Not Found']);
            }
        }

        return $next($request);
    }
}
