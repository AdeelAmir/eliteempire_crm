<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AcquisitionRepresentativeRouteValidate
{
    public function handle($request, Closure $next)
    {
        if (auth::user()->role_id == 1){
            return redirect('/admin/dashboard');
        }
        elseif(auth::user()->role_id == 2){
            return redirect('/global_manager/dashboard');
        }
        elseif (auth::user()->role_id == 3) {
            return redirect('/acquisition_manager/dashboard');
        }
        elseif (auth::user()->role_id == 4) {
            return redirect('/disposition_manager/dashboard');
        }
        elseif (auth::user()->role_id == 6) {
            return redirect('/disposition_representative/dashboard');
        }
        elseif (auth::user()->role_id == 7) {
            return redirect('/cold_caller/dashboard');
        }
        elseif (auth::user()->role_id == 8) {
            return redirect('/affiliate/dashboard');
        }
        elseif (auth::user()->role_id == 9) {
            return redirect('/realtor/dashboard');
        }
        return $next($request);
    }
}
