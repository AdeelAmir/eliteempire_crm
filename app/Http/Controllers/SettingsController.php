<?php

namespace App\Http\Controllers;

use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    function index(){
        $page = 'settings';
        $Settings = Settings::find(1);
        return view('admin.settings.settings', compact('page', 'Settings'));
    }

    function UpdateHoursPrice(Request $request){
        $Price = $request->post('hoursPrice');
        $Affected = DB::table('settings')
            ->where('id', '=', 1)
            ->update([
                'hours_price' => $Price,
                'updated_at' => Carbon::now()
            ]);
        if($Affected){
            return redirect(url('/admin/payout'))->with('message', 'Hours Price Updated Successfully!');
        }
        else{
            return redirect(url('/admin/payout'))->with('error', 'An unhandled error occurred!');
        }
    }
}