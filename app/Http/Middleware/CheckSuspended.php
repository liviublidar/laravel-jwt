<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class CheckSuspended
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
        $userSuspended = $request->user->isSuspended();
        $account = $request->user->getAccount();
        $accountSuspend = $account->isSuspended();

        if($userSuspended || $accountSuspend) {
            return response()->json('Account no longer active', 403);
        }

        return $next($request);
    }
}
