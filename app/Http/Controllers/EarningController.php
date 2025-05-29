<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class EarningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

    }

    public function AdminEarningRecord()
    {
        $page = "earning";
        $Role = Session::get('user_role');
        $CurrentDate = date('Y-m-d');
        $first_date = date('Y-m-d', strtotime('first day of this month'));
        $last_date = date('Y-m-d', strtotime('last day of this month'));
        $Date7th = Carbon::now()->firstOfMonth()->addDays(6)->format('Y-m-d');
        $Date22th = Carbon::now()->firstOfMonth()->addDays(21)->format('Y-m-d');
        $Date23th = Carbon::now()->firstOfMonth()->addDays(22)->format('Y-m-d');
        $Date6thNextMonth = Carbon::now()->lastOfMonth()->addDays(6)->format('Y-m-d');

        // Total Sales
        $TotalSales = DB::table('sales')
            ->whereBetween('created_at', array(Carbon::parse($Date7th), Carbon::parse($Date6thNextMonth)))
            ->where('deleted_at', '=', null)
            ->count();

        $TotalPayroll = 0;
        // Get Total Net Amount
        $NetAmount = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->whereBetween('created_at', array(Carbon::parse($Date7th), Carbon::parse($Date6thNextMonth)))
            ->where('sale_type', '=', "Approve Sales")
            ->sum('sales.net_profit_amount');
        // Get Total Bank turn down
        $Totallost = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->where('sale_type', '=', "Bank Turn Down")
            ->count('sales.contract_amount');
        $_TotalPayroll = DB::table('pay_periods')
            ->where(function ($query) use ($Date7th, $Date6thNextMonth) {
                $query->orWhere('pay_periods.startDate', '=', $Date7th);
                $query->orWhere('pay_periods.endDate', '=', $Date6thNextMonth);
            })
            ->where('pay_periods.approved_at', '!=', null)
            ->select(DB::raw('SUM(pay_periods.net_income) AS NetIncome'))
            ->get();
        if(sizeof($_TotalPayroll) > 0){
            $TotalPayroll = $_TotalPayroll[0]->NetIncome;
        }

        // $TotalProfit = $NetAmount - $TotalPayroll;
        $TotalProfit = $NetAmount - $TotalPayroll;

        return view('admin.earning.earnings', compact('page', 'Role', 'TotalSales', 'NetAmount', 'Totallost', 'TotalProfit'));
    }

    public function ManagerEarningRecord()
    {
        $page = "earning";
        $Role = Session::get('user_role');

//        $Earnings = DB::table('earnings_ms')->where('u_id', '=', Auth::id())->get();
        $Earnings = DB::table('pay_periods')
            ->where('user_id', '=', Auth::id())
            ->orderBy('approved_at', 'DESC')
            ->limit(1)
            ->get();
        $Earning = 0;
        if (sizeof($Earnings) > 0) {
            $Earning = $Earnings[0]->net_income;
        }

        return view('admin.earning.earnings', compact('page', 'Role', 'Earning'));
    }

    public function ConfirmationAgentEarningRecord()
    {
        $page = "earning";
        $Role = Session::get('user_role');

//        $Earnings = DB::table('earnings_ms')->where('u_id', '=', Auth::id())->get();
        $Earnings = DB::table('pay_periods')
            ->where('user_id', '=', Auth::id())
            ->orderBy('approved_at', 'DESC')
            ->limit(1)
            ->get();
        $Earning = 0;
        if (sizeof($Earnings) > 0) {
            $Earning = $Earnings[0]->net_income;
        }

        return view('admin.earning.earnings', compact('page', 'Role', 'Earning'));
    }

    public function SupervisorEarningRecord(Request $request)
    {
        $page = "earning";
        $Role = Session::get('user_role');
        $user_id = Auth::id();
        // $CurrentDate = date('Y-m-d');
        // $first_date = date('Y-m-d',strtotime('first day of this month'));
        // $last_date = date('Y-m-d',strtotime('last day of this month'));

        // New Leads
        $NewLeads = DB::table('leads')
            ->where('lead_status', '=', 3)
            ->where('lead_type', '=', 1)
            // ->where('lead_date', '=', $CurrentDate)
            ->where('user_id', '=', $user_id)
            ->where('deleted_at', '=', null)
            ->count();
        // Total Leads
        $TotalLeads = DB::table('leads')
            ->where('lead_type', '=', 1)
            ->where('user_id', '=', $user_id)
            // ->whereBetween('lead_date', array($first_date, $last_date))
            ->where('deleted_at', '=', null)
            ->count();
        // Total Confirmed Leads
        $TotalConfirmedLeads = DB::table('leads')
            ->whereIn('leads.lead_status', array(1, 4, 5))
            ->where('lead_type', '=', 1)
            ->where('user_id', '=', $user_id)
            // ->whereBetween('lead_date', array($first_date, $last_date))
            ->where('deleted_at', '=', null)
            ->count();
        // Earning
//        $Earnings = DB::table('earnings_ms')->where('u_id', '=', Auth::id())->get();
        $Earnings = DB::table('pay_periods')
            ->where('user_id', '=', Auth::id())
            ->orderBy('approved_at', 'DESC')
            ->limit(1)
            ->get();
        $Earning = 0;
        if (sizeof($Earnings) > 0) {
            $Earning = $Earnings[0]->net_income;
        }

        return view('admin.earning.earnings', compact('page', 'Role', 'NewLeads', 'TotalLeads', 'TotalConfirmedLeads', 'Earning'));
    }

    public function RepresentativeEarningRecord(Request $request)
    {
        $page = "earning";
        $Role = Session::get('user_role');
        $user_id = Auth::id();
        // $CurrentDate = date('Y-m-d');
        // $first_date = date('Y-m-d',strtotime('first day of this month'));
        // $last_date = date('Y-m-d',strtotime('last day of this month'));

        // New Leads
        $NewLeads = DB::table('leads')
            ->where('lead_status', '=', 3)
            ->where('lead_type', '=', 1)
            // ->where('lead_date', '=', $CurrentDate)
            ->where('user_id', '=', $user_id)
            ->where('deleted_at', '=', null)
            ->count();
        // Total Leads
        $TotalLeads = DB::table('leads')
            ->where('lead_type', '=', 1)
            ->where('user_id', '=', $user_id)
            // ->whereBetween('lead_date', array($first_date, $last_date))
            ->where('deleted_at', '=', null)
            ->count();
        // Total Confirmed Leads
        $TotalConfirmedLeads = DB::table('leads')
            ->whereIn('leads.lead_status', array(1, 4, 5))
            ->where('lead_type', '=', 1)
            ->where('user_id', '=', $user_id)
            // ->whereBetween('lead_date', array($first_date, $last_date))
            ->where('deleted_at', '=', null)
            ->count();
        // Earning
//        $Earnings = DB::table('earnings_ms')->where('u_id', '=', Auth::id())->get();
        $Earnings = DB::table('pay_periods')
            ->where('user_id', '=', Auth::id())
            ->orderBy('approved_at', 'DESC')
            ->limit(1)
            ->get();
        $Earning = 0;
        if (sizeof($Earnings) > 0) {
            $Earning = $Earnings[0]->net_income;
        }

        return view('admin.earning.earnings', compact('page', 'Role', 'NewLeads', 'TotalLeads', 'TotalConfirmedLeads', 'Earning'));
    }
}