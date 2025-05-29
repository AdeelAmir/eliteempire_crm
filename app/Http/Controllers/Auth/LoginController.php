<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (isset($user)) {
            if ($user->status != 1) {
                Auth::logout();
                return redirect('/login')->with('error', 'Your account has been deactivated. Please contact your manager.');
            } else {
                Session::put('user_role', $user->role_id);
                // Update user logged in datetime
                $user->last_logged_in = Carbon::now();
                $user->online_status = 1;
                $user->save();

                // Add an entry in user activity to record login time
                $affected = UserActivity::create([
                    'user_id' => $user->id,
                    'sender_id' => $user->id,
                    'message' => "This user is logged in at " . Carbon::now()->format('m/d/Y g:i a'),
                    'created_at' => Carbon::now(),
                ]);

                // Admin
                if ($user->role_id == 1) {
                    return redirect()->route('adminDashboard');
                }
                // Global Manager
                if ($user->role_id == 2) {
                    return redirect()->route('globalManagerDashboard');
                }
                // Acquisition Manager
                if ($user->role_id == 3) {
                    return redirect(url('/acquisition_manager/dashboard'));
                }
                // Disposition Manager
                if ($user->role_id == 4) {
                    // return redirect()->route('dispositionManagerDashboard');
                    return redirect(url('/disposition_manager/dashboard'));
                }
                // Acquisition Representative
                if ($user->role_id == 5) {
                    return redirect()->route('acquisitionRepresentativeTraining');
                }
                 // cold caller Representative
                if ($user->role_id == 7) {
                    return redirect()->route('coldCallerDashboard');
                }
                // cold caller Representative
                if ($user->role_id == 8) {
                    return redirect()->route('affiliateDashboard');
                }
                // cold caller Representative
                if ($user->role_id == 9) {
                    return redirect()->route('realtorDashboard');
                }
            }
        }

        return redirect('/login');
    }

    public function logout()
    {
        // Add an entry in user activity to record login time
        if(isset(auth()->user()->id)){
            DB::beginTransaction();
            $affected = UserActivity::create([
                'user_id' => auth()->user()->id,
                'sender_id' => auth()->user()->id,
                'message' => "This user is logout in at " . Carbon::now()->format('m/d/Y g:i a'),
                'created_at' => Carbon::now(),
            ]);

            $affected1 = DB::table('users')
                ->where('id', auth()->user()->id)
                ->update([
                  'online_status' => 0,
                  'updated_at' => Carbon::now(),
            ]);
            if ($affected && $affected1) {
              DB::commit();
            }
            else {
              DB::rollback();
            }
        }

        Auth::logout();
        return redirect('/login');
    }
}
