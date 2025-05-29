<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class DispositionRepresentative
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

        if(auth::user()->role_id == 1){
            return redirect('/admin/dashboard');
        }
        elseif(auth::user()->role_id == 2) {
            return redirect('/global_manager/dashboard');
        }
        elseif (auth::user()->role_id == 3) {
            return redirect('/acquisition_manager/dashboard');
        }
        elseif (auth::user()->role_id == 4) {
            return redirect('/disposition_manager/dashboard');
        }
        elseif (auth::user()->role_id == 5) {
            return redirect('/acquisition_representative/dashboard');
        }
        elseif (auth::user()->role_id == 7) {
            return redirect('/cold_caller/dashboard');
        }
        elseif (auth::user()->role_id == 8) {
            return redirect('/affiliate/dashboard');
        }
        return $next($request);
    }
}
