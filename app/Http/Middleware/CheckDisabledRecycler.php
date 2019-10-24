<?php

namespace App\Http\Middleware;

use App\Models\Recycler;
use Closure;
use Dingo\Api\Exception\RateLimitExceededException;
use Illuminate\Support\Facades\Auth;


class CheckDisabledRecycler
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $recycler = Auth::guard('clean')->user();

        if ($recycler == null)
        {
            info('app/Http/Middleware/CheckDisabledRecycler');
            throw new RateLimitExceededException();
        }

        if ($recycler->disabled_at != null)
        {
            Recycler::recyclerDisabledException();
        }
        return $next($request);
    }
}
