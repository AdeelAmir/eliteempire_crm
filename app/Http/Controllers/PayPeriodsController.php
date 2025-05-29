<?php

namespace App\Http\Controllers;

use App\Earnings_m;
use App\Pay_periods;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayPeriodsController extends Controller
{
    function ViewIncomeDetails(Request $request){
        $MasterId = $request->post('id');
        $StartDate = Carbon::parse($request['StartDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['EndDate'])->format("Y-m-d");
        $UserId = Earnings_m::find($MasterId);
        $PayPeriodData = DB::table('pay_periods')
            ->where('user_id', '=', $UserId->u_id)
            ->where('startDate', '=', $StartDate)
            ->where('endDate', '=', $EndDate)
            ->get();
        echo json_encode($PayPeriodData);
        exit();
    }

    function StoreUpdateIncomeDetails(Request $request){
        $PayPeriodId = $request->post('id');
        $Hours = $request->post('hours');
        $HourRate = floatval(Settings::find(1)->hours_price);
        $HoursPrice = floatval($Hours) * $HourRate;
        $Tax = $request->post('tax');
        $DrawBalance = $request->post('drawBalance');
        $MasterId = $request->post('MasterId');
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->addDays(1)->format("Y-m-d");
        $UserId = Earnings_m::find($MasterId)->u_id;

        $Earning_d = DB::table('earnings_ms')
            ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->where('earnings_ms.id', '=', $MasterId)
            ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
            ->select('earning_ds.*')
            ->get();
        $Earnings_d = array();
        $Earning = 0;
        $Bonus = 0;
        foreach ($Earning_d as $item) {
            $Earnings_d[] = $item->id;
            if($item->earning != ''){
                $Earning = $Earning + floatval($item->earning);
            }
            if($item->bonus != ''){
                $Bonus = $Bonus + floatval($item->bonus);
            }
        }

        $GrossIncome = $Earning + $Bonus + $HoursPrice;
        $TaxAmount = ($GrossIncome) * floatval($Tax)/100;
        $NetIncome = $GrossIncome - (floatval($TaxAmount) + floatval($DrawBalance));
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->format("Y-m-d");
        $Earnings_d = implode(',', $Earnings_d);

        DB::beginTransaction();
        if($PayPeriodId != 0){
            // Record Update
            $Affected = DB::update("UPDATE pay_periods SET earnings = :earnings, bonus = :bonus, hours = :hours, hoursPrice = :hoursPrice, grossIncome = :grossIncome, tax = :tax, taxAmount = :taxAmount, draw_balance = :draw_balance, net_income = :net_income WHERE id = :id",
                array($Earning, $Bonus, $Hours, $HoursPrice, $GrossIncome, $Tax, $TaxAmount, $DrawBalance, $NetIncome, $PayPeriodId));
            if($Affected){
                DB::commit();
                echo json_encode(['status' => 'success']);
                exit();
            }
            else{
                DB::rollBack();
                echo json_encode(['status' => 'failed']);
                exit();
            }
        }
        else{
            // Record Insert
            $Affected = Pay_periods::create([
                'user_id' => $UserId,
                'startDate' => $StartDate,
                'endDate' => $EndDate,
                'earning_d' => $Earnings_d,
                'earnings' => $Earning,
                'bonus' => $Bonus,
                'hours' => $Hours,
                'hoursPrice' => $HoursPrice,
                'grossIncome' => $GrossIncome,
                'tax' => $Tax,
                'taxAmount' => $TaxAmount,
                'draw_balance' => $DrawBalance,
                'net_income' => $NetIncome,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            if($Affected){
                DB::commit();
                echo json_encode(['status' => 'success']);
                exit();
            }
            else{
                DB::rollBack();
                echo json_encode(['status' => 'failed']);
                exit();
            }
        }
    }

    public function SubmittedPayroll()
    {
        $page = "submittedPayroll";
        $Role = Session::get('user_role');
        return view('admin.payroll.submittedPayrolls', compact('page', 'Role'));
    }

    public function loadSubmittedPayroll(Request $request)
    {
        DB::beginTransaction();
        /*First Remove all unapproved pay periods*/
        DB::table('pay_periods')
            ->where('submitted_at', '=', null)
            ->where('approved_at', '=', null)
            ->delete();
        /*Second add new pay periods*/
        $UserEarnings = DB::table('earnings_ms')
            ->where('deleted_at', '=', null)
            ->select('earnings_ms.*')
            ->get();
        foreach ($UserEarnings as $userEarning) {
            if($userEarning->u_id != 1){
                $PayPeriodsEntryCheck = DB::table('pay_periods')
                    ->where('pay_periods.user_Id', '=', $userEarning->u_id)
                    ->where('pay_periods.submitted_at', '!=', null)
                    ->where('pay_periods.approve_status', '=', null)
                    ->count();
                if($PayPeriodsEntryCheck > 0){
                    /*Avoid creating duplicate entry of that user*/
                    continue;
                }
                $UserWiseEarnings = DB::table('earning_ds')
                    ->where('earning_ds.earning_id', '=', $userEarning->id)
                    ->where('earning_ds.approve_status', '=', null)
                    ->select('earning_ds.*')
                    ->get();
                /*If All user earnings are approved skip*/
                if(sizeof($UserWiseEarnings) == 0){
                    continue;
                }
                $Earnings_d = "";
                $Count = 0;
                $Earnings = 0;
                $Bonuses = 0;
                $GrossIncome = 0;
                $Tax = 0;
                $TaxAmount = 0;
                $DrawBalance = 0;
                $NetIncome = 0;
                foreach ($UserWiseEarnings as $earning){
                    if($Count == 0){
                        $Earnings_d = $earning->id;
                    }
                    else{
                        $Earnings_d .= ',' . $earning->id;
                    }
                    $Count++;
                    $Earnings += floatval($earning->earning);
                    $Bonuses += floatval($earning->bonus);
                    $GrossIncome = $earning->earning + $earning->bonus;
                    $NetIncome = $GrossIncome;
                }
                // Record Pay Period Record
                $Affected = Pay_periods::create([
                    'user_id' => $userEarning->u_id,
                    'earning_d' => $Earnings_d,
                    'earnings' => $Earnings,
                    'bonus' => $Bonuses,
                    'grossIncome' => $GrossIncome,
                    'tax' => $Tax,
                    'taxAmount' => $TaxAmount,
                    'draw_balance' => $DrawBalance,
                    'net_income' => $NetIncome,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        DB::commit();

        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = DB::table('pay_periods')
                ->where('pay_periods.approve_status', '=', null)
                ->select('pay_periods.id', DB::raw('SUM(pay_periods.earnings) AS Earnings'), DB::raw('SUM(pay_periods.bonus) AS Bonus'), DB::raw('SUM(pay_periods.grossIncome) AS GrossIncome'), DB::raw('SUM(pay_periods.taxAmount) AS TaxAmount'), DB::raw('SUM(pay_periods.draw_balance) AS DrawBalance'), DB::raw('SUM(pay_periods.net_income) AS NetIncome'))
                ->groupBy('pay_periods.user_id')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('pay_periods')
                ->where('pay_periods.approve_status', '=', null)
                ->select('pay_periods.id', DB::raw('SUM(pay_periods.earnings) AS Earnings'), DB::raw('SUM(pay_periods.bonus) AS Bonus'), DB::raw('SUM(pay_periods.grossIncome) AS GrossIncome'), DB::raw('SUM(pay_periods.taxAmount) AS TaxAmount'), DB::raw('SUM(pay_periods.draw_balance) AS DrawBalance'), DB::raw('SUM(pay_periods.net_income) AS NetIncome'))
                ->groupBy('pay_periods.user_id')
                ->count();
        } else {
            $fetch_data = DB::table('pay_periods')
                ->where('pay_periods.approve_status', '=', null)
                ->select('pay_periods.id', DB::raw('SUM(pay_periods.earnings) AS Earnings'), DB::raw('SUM(pay_periods.bonus) AS Bonus'), DB::raw('SUM(pay_periods.grossIncome) AS GrossIncome'), DB::raw('SUM(pay_periods.taxAmount) AS TaxAmount'), DB::raw('SUM(pay_periods.draw_balance) AS DrawBalance'), DB::raw('SUM(pay_periods.net_income) AS NetIncome'))
                ->groupBy('pay_periods.user_id')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('pay_periods')
                ->where('pay_periods.approve_status', '=', null)
                ->select('pay_periods.id', DB::raw('SUM(pay_periods.earnings) AS Earnings'), DB::raw('SUM(pay_periods.bonus) AS Bonus'), DB::raw('SUM(pay_periods.grossIncome) AS GrossIncome'), DB::raw('SUM(pay_periods.taxAmount) AS TaxAmount'), DB::raw('SUM(pay_periods.draw_balance) AS DrawBalance'), DB::raw('SUM(pay_periods.net_income) AS NetIncome'))
                ->groupBy('pay_periods.user_id')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $__StartDate = ' ';
            $__EndDate = ' ';
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['startDate'] = '';
            $sub_array['Earnings'] = "$" . $item->Earnings;
            $sub_array['Bonus'] = "$" . $item->Bonus;
            $sub_array['Hours'] = 0;
            $sub_array['HoursPrice'] = "$" . 0;
            $sub_array['GrossIncome'] = "$" . $item->GrossIncome;
            $sub_array['TaxAmount'] = "$" . $item->TaxAmount;
            $sub_array['DrawBalance'] = "$" . $item->DrawBalance;
            $sub_array['NetIncome'] = "$" . $item->NetIncome;
            $sub_array['view'] = '<button class="btn btn-info" onclick="ViewSubmittedEarningPayrollDetails(\'' . $item->id . '\');">VIEW</button>';
            $sub_array['approve'] = '<button class="btn btn-success" onclick="GeneratePayroll(\'' . $item->id . '\');">GENERATE PAYROLL</button>';
            $SrNo++;
            $data[] = $sub_array;
        }

        $json_data = array(
            "draw" => intval($request->post('draw')),
            "iTotalRecords" => $recordsTotal,
            "iTotalDisplayRecords" => $recordsFiltered,
            "aaData" => $data
        );

        echo json_encode($json_data);
    }

    public function loadSubmittedPayrollBreakdowns(Request $request)
    {
        $Id = $request['Id'];
        $user_payroll_breakdowns = DB::table('pay_periods')
            ->join('profiles', 'profiles.user_id', '=', 'pay_periods.user_id')
            ->where('pay_periods.id', '=', $Id)
            ->select('pay_periods.*', 'profiles.firstname', 'profiles.lastname')
            ->get();

        $data = "";
        $data .=
            '<div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                Details
                            </h6>
                            <div class="table-responsive">
                                <table class="table w-100">
                                   <thead>
                                      <tr>
                                          <th>Sr. No.</th>
                                          <th>Name</th>
                                          <th>Commissions</th>
                                          <th>Bonus</th>
                                          <th>Tax (%)</th>
                                          <th>Draw balance</th>
                                          <th>Edit</th>
                                      </tr>
                                    </thead>
                                    <tbody>';
        $Counter = 1;
        foreach ($user_payroll_breakdowns as $p_details) {
            $PayPeriodId = $p_details->id;
            $Earnings = "$" . $p_details->earnings;
            $Bonus = "$" . $p_details->bonus;
            $Tax = $p_details->tax . "%";
            $DrawBalance = "$" . $p_details->draw_balance;

            $data .= '<tr>';
            $data .= '<td>' . $Counter . '</td>';
            $data .= '<td>' . $p_details->firstname . ' ' . $p_details->lastname . '</td>';
            $data .= '<td>' . $Earnings . '</td>';
            $data .= '<td>' . $Bonus . '</td>';
            $data .= '<td>' . $Tax . '</td>';
            $data .= '<td>' . $DrawBalance . '</td>';
            $data .= '<td><button class="btn btn-primary" onclick="EditPayPeriodEarning(\'' . $PayPeriodId . '\')">Edit</button></td>';
            $data .= '</tr>';
            $Counter++;
        }
        $data .=
            '                   </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        echo json_encode($data);
    }

    function EditPayPeriodEarning(Request $request){
        $PayPeriodId = $request->post('PayPeriodId');
        $PayPeriodData = DB::table('pay_periods')
            ->where('id', '=', $PayPeriodId)
            ->get();
        echo json_encode($PayPeriodData);
        exit();
    }

    function UpdatePayPeriodEarning(Request $request){
        $PayPeriodId = $request->post('id');
        $Earnings = $request->post('Earnings');
        $Bonus = $request->post('Bonus');
        $Tax = $request->post('tax');
        $DrawBalance = $request->post('drawBalance');
        $GrossIncome = $Earnings + $Bonus;
        $TaxAmount = ($GrossIncome) * floatval($Tax)/100;
        $NetIncome = $GrossIncome - (floatval($TaxAmount) + floatval($DrawBalance));

        $Affected = DB::update("UPDATE pay_periods SET earnings = :earnings, bonus = :bonus, grossIncome = :grossIncome, tax = :tax, taxAmount = :taxAmount, draw_balance = :draw_balance, net_income = :net_income, submitted_at = :submitted_at WHERE id = :id",
            array($Earnings, $Bonus, $GrossIncome, $Tax, $TaxAmount, $DrawBalance, $NetIncome, Carbon::now(), $PayPeriodId));
        if($Affected){
            DB::commit();
            echo json_encode(['status' => 'success']);
            exit();
        }
        else{
            DB::rollBack();
            echo json_encode(['status' => 'failed']);
            exit();
        }
    }

    function GeneratePayroll(Request $request){
        $Id = $request->post('id');
        DB::beginTransaction();
        $Affected = DB::table('earning_ds')
            ->where('sale_id', '=', $Id)
            ->update([
                'approve_status' => 1,
                'submitted_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        if($Affected){
            DB::commit();
            if(Auth::user()->role_id == 1){
                return redirect(url('admin/sales'))->with('message', 'Payroll Generated Successfully');
            }
            elseif(Auth::user()->role_id == 2){
                return redirect(url('global_manager/sales'))->with('message', 'Payroll Generated Successfully');
            }
        }
        else{
            DB::rollBack();
            if(Auth::user()->role_id == 1){
                return redirect(url('admin/sales'))->with('error', 'An unhandled error occurred');
            }
            elseif(Auth::user()->role_id == 2){
                return redirect(url('global_manager/sales'))->with('error', 'An unhandled error occurred');
            }
        }
    }

    function Rollback(Request $request){
        $Id = $request->post('id');
        DB::beginTransaction();
        $Affected = DB::table('earning_ds')
            ->where('sale_id', '=', $Id)
            ->update([
                'approve_status' => null,
                'submitted_at' => null,
                'updated_at' => Carbon::now()
            ]);
        if($Affected){
            DB::commit();
            if(Auth::user()->role_id == 1){
                return redirect(url('admin/sales'))->with('message', 'Payroll Rollback Successfully');
            }
            elseif(Auth::user()->role_id == 2){
                return redirect(url('global_manager/sales'))->with('message', 'Payroll Rollback Successfully');
            }
        }
        else{
            DB::rollBack();
            if(Auth::user()->role_id == 1){
                return redirect(url('admin/sales'))->with('error', 'An unhandled error occurred');
            }
            elseif(Auth::user()->role_id == 2){
                return redirect(url('global_manager/sales'))->with('error', 'An unhandled error occurred');
            }
        }
    }
}