<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class TestMiddleware
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
        if (Session::has('asdf')){
            Session::put('asdf', Session::get('asdf') + 1);
        }
        else{
            Session::put('asdf', 1);
        }
        
        return $next($request);
    }
}
