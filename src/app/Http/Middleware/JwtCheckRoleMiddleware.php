<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtCheckRoleMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user_role = JWTAuth::user()->role;
            if($user_role !== 1){ // only admin
                return Helper::error(null, 'Access Forbidden', 403);
            }
        } catch (Exception $e) {
            return Helper::error(null, 'Unautorize', 401);
        }
        return $next($request);
    }
}
