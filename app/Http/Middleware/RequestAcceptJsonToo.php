<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestAcceptJsonToo
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept',   'application/json' . ',' . $request->headers->get('Accept'));
        return $next($request);
    }
}
