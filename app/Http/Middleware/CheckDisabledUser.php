<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Dingo\Api\Exception\RateLimitExceededException;
use Illuminate\Support\Facades\Auth;


class CheckDisabledUser
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('client')->user();

        if ($user == null)
        {
            info('app/Http/Middleware/CheckDisabledUser');
            throw new RateLimitExceededException();
        }

        if ($user->disabled_at != null)
        {
            User::userDisabledException();
        }
        return $next($request);
    }
}
