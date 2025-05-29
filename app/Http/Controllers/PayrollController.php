<?php

namespace App\Http\Controllers;

use App\Earnings_m;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $page = "approvepayroll";
        $Role = Session::get('user_role');
        $CurrentDate = Carbon::now()->format("m/d/Y");
        $CurrentDate = Carbon::createFromFormat('m/d/Y', $CurrentDate);
        $PayrollStartDate = null;
        $PayrollEndDate = null;
        // Calculate start Date and end Date of first payroll breakdown
        $FirstPayrollBreakdown_StartDate = Carbon::now()->startOfMonth()->addDays(6)->format('m/d/Y');
        $FirstPayrollBreakdown_CompareStartDate = Carbon::createFromFormat('m/d/Y', $FirstPayrollBreakdown_StartDate);
        $FirstPayrollBreakdown_EndDate = Carbon::now()->startOfMonth()->addDays(21)->format('m/d/Y');
        $FirstPayrollBreakdown_CompareEndDate = Carbon::createFromFormat('m/d/Y', $FirstPayrollBreakdown_EndDate);

        // Calculate start Date and end Date of second payroll breakdown
        $SecondPayrollBreakdown_StartDate = Carbon::now()->startOfMonth()->addDays(22)->format('m/d/Y');
        $SecondPayrollBreakdown_CompareStartDate = Carbon::createFromFormat('m/d/Y', $SecondPayrollBreakdown_StartDate);
        $SecondPayrollBreakdown_EndDate = Carbon::now()->startOfMonth()->addMonth(1)->addDays(5)->format('m/d/Y');
        $SecondPayrollBreakdown_CompareEndDate = Carbon::createFromFormat('m/d/Y', $SecondPayrollBreakdown_EndDate);

        // Calculate start Date and end Date of third payroll breakdown
        $ThirdPayrollBreakdown_StartDate = Carbon::now()->startOfMonth()->subMonths(1)->addDays(22)->format('m/d/Y');
        $ThirdPayrollBreakdown_CompareStartDate = Carbon::createFromFormat('m/d/Y', $ThirdPayrollBreakdown_StartDate);
        $ThirdPayrollBreakdown_EndDate = Carbon::now()->startOfMonth()->addDays(5)->format('m/d/Y');
        $ThirdPayrollBreakdown_CompareEndDate = Carbon::createFromFormat('m/d/Y', $ThirdPayrollBreakdown_EndDate);

        if ($CurrentDate->gte($FirstPayrollBreakdown_CompareStartDate) && $CurrentDate->lte($FirstPayrollBreakdown_CompareEndDate)) {
          $PayrollStartDate = $FirstPayrollBreakdown_StartDate;
          $PayrollEndDate = $FirstPayrollBreakdown_EndDate;
        }
        elseif ($CurrentDate->gte($SecondPayrollBreakdown_CompareStartDate) && $CurrentDate->lte($SecondPayrollBreakdown_CompareEndDate)) {
          $PayrollStartDate = $SecondPayrollBreakdown_StartDate;
          $PayrollEndDate = $SecondPayrollBreakdown_EndDate;
        }
        elseif ($CurrentDate->gte($ThirdPayrollBreakdown_CompareStartDate) && $CurrentDate->lte($ThirdPayrollBreakdown_CompareEndDate)) {
          $PayrollStartDate = $ThirdPayrollBreakdown_StartDate;
          $PayrollEndDate = $ThirdPayrollBreakdown_EndDate;
        }

        return view('admin.payroll.payrolls', compact('page', 'Role', 'PayrollStartDate', 'PayrollEndDate'));
    }

    public function loadPayroll(Request $request)
    {
        $StartDate = Carbon::parse($request['StartDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['EndDate'])->addDays(1)->format("Y-m-d");

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
            $fetch_data = DB::table('earnings_ms')
                ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
                ->join('users', 'users.id', '=', 'earnings_ms.u_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('earnings_ms.deleted_at', '=', null)
                ->where('earning_ds.approve_status', '=', null)
                ->where('earning_ds.submitted_at', '=', null)
                ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
                ->select('earnings_ms.*', DB::raw('SUM(earning_ds.earning) AS earning'), DB::raw('SUM(earning_ds.bonus) AS bonus'), 'profiles.firstname', 'profiles.lastname', 'roles.title')
                ->groupBy('earning_ds.earning_id')
//                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('earnings_ms')
                ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
                ->join('users', 'users.id', '=', 'earnings_ms.u_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where('earnings_ms.deleted_at', '=', null)
                ->where('earning_ds.approve_status', '=', null)
                ->where('earning_ds.submitted_at', '=', null)
                ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
                ->select('earnings_ms.*', DB::raw('SUM(earning_ds.earning) AS earning'), DB::raw('SUM(earning_ds.bonus) AS bonus'), 'profiles.firstname', 'profiles.lastname', 'roles.title')
                ->groupBy('earning_ds.earning_id')
//                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('earnings_ms')
                ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
                ->join('users', 'users.id', '=', 'earnings_ms.u_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
                ->where(function ($query) {
                    $query->where([
                        ['earnings_ms.deleted_at', '=', null],
                        ['earning_ds.approve_status', '=', null],
                        ['earning_ds.submitted_at', '=', null]
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('earning_ds.lead_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.payout_type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.earning', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.bonus', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('earnings_ms.*', DB::raw('SUM(earning_ds.earning) AS earning'), DB::raw('SUM(earning_ds.bonus) AS bonus'), 'profiles.firstname', 'profiles.lastname', 'roles.title')
                ->groupBy('earning_ds.earning_id')
//                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('earnings_ms')
                ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
                ->join('users', 'users.id', '=', 'earnings_ms.u_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
                ->where(function ($query) {
                    $query->where([
                        ['earnings_ms.deleted_at', '=', null],
                        ['earning_ds.approve_status', '=', null],
                        ['earning_ds.submitted_at', '=', null]
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('earning_ds.lead_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.payout_type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.earning', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('earning_ds.bonus', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('earnings_ms.*', DB::raw('SUM(earning_ds.earning) AS earning'), DB::raw('SUM(earning_ds.bonus) AS bonus'), 'profiles.firstname', 'profiles.lastname', 'roles.title')
                ->groupBy('earning_ds.earning_id')
//                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $StartDate = Carbon::parse($request['StartDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['EndDate'])->format("Y-m-d");
        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->firstname . " " . $item->lastname;
            $sub_array['account'] = $item->title;
            $sub_array['earning'] = "$" . $item->earning;
            if ($item->bonus != "") {
                $sub_array['bonus'] = "$" . $item->bonus;
            } else {
                $sub_array['bonus'] = "";
            }
            $sub_array['view'] = '<button class="btn btn-info" id="details_' . $item->id . '" onclick="ViewEarningPayrollDetails(this.id)">View</button>';
            $sub_array['income'] = '<button class="btn btn-info" id="income_' . $item->id . '" onclick="ViewIncomeDetails(this.id)">Income</button>';
            if($this->CheckForPayPeriodEntry($StartDate, $EndDate, $item->u_id)){
                $sub_array['submit'] = '<button class="btn btn-success" id="submit_' . $item->id . '" onclick="SubmitEarningPayroll(this.id);">Submit</button>';
            }
            else{
                $sub_array['submit'] = '';
            }
            $sub_array['cancel'] = '<button class="btn btn-danger" id="reject_' . $item->id . '" onclick="RejectEarningPayroll(this.id)">Cancel</button>';
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

    function CheckForPayPeriodEntry($StartDate, $EndDate, $UserId){
        $Data = DB::table('pay_periods')
            ->where('user_id', '=', $UserId)
            ->where('startDate', '=', $StartDate)
            ->where('endDate', '=', $EndDate)
            ->get();
        if(sizeof($Data) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function rejectPayroll(Request $request)
    {
        $MasterId = $request->post('id');
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->addDays(1)->format("Y-m-d");
        $Earning_d = DB::table('earnings_ms')
            ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->where('earnings_ms.id', '=', $MasterId)
            ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
            ->select('earning_ds.*')
            ->get();
        $Affected = null;
        foreach ($Earning_d as $item) {
            $Affected = DB::table('earning_ds')
                ->where('id', $item->id)
                ->update([
                    'approve_status' => -1,
                    'updated_at' => Carbon::now(),
                ]);
        }
        // Set submitted Status of Pay Period
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->format("Y-m-d");
        $UserId = Earnings_m::find($MasterId)->u_id;
        $Affected1 = DB::table('pay_periods')
            ->where('user_id', '=', $UserId)
            ->where('startDate', '=', $StartDate)
            ->where('endDate', '=', $EndDate)
            ->update(array(
                'approve_status' => -1,
                'updated_at' => Carbon::now()
            ));

        if ($Affected) {
            DB::commit();
            echo json_encode(['status' => 'success']);
//            if (Auth::user()->role_id == 1) {
//                return redirect(url('/admin/payroll/approve'))->with('message', 'User payroll has been cancelled successfully');
//            } else {
//                return redirect(url('/general_manager/payroll/approve'))->with('message', 'User payroll has been cancelled successfully');
//            }
        } else {
            DB::rollback();
            echo json_encode(['status' => 'failed']);
//            if (Auth::user()->role_id == 1) {
//                return redirect(url('/admin/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
//            } else {
//                return redirect(url('/general_manager/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
//            }
        }
        exit();
    }

    public function addBonusPayroll(Request $request)
    {
        $bonusPayrollId = $request['id'];
        $bonusAmount = $request['bonus'];

        // Get Old Bonus Amount
        // $payroll_details = DB::table('earning_ds')
        //     ->where('id', '=', $bonusPayrollId)
        //     ->get();
        //
        // $OldBonusAmount = 0;
        // if ($payroll_details[0]->bonus != "") {
        //     $OldBonusAmount = $payroll_details[0]->bonus;
        // }
        //
        // $NewBonusAmount = $bonusAmount + $OldBonusAmount;

        DB::beginTransaction();
        $affected = DB::table('earning_ds')
            ->where('id', $bonusPayrollId)
            ->update([
                'bonus' => $bonusAmount,
                'updated_at' => Carbon::now(),
            ]);

        if ($affected) {
            DB::commit();
            echo "Success";
            // if(Auth::user()->role_id == 1){
            //     return redirect(url('/admin/payroll/approve'))->with('message', 'User bonus has been added successfully');
            // }
            // else{
            //     return redirect(url('/general_manager/payroll/approve'))->with('message', 'User bonus has been added successfully');
            // }
        } else {
            DB::rollback();
            echo "Error";
            // if(Auth::user()->role_id == 1){
            //     return redirect(url('/admin/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
            // }
            // else{
            //     return redirect(url('/general_manager/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
            // }
        }
    }

    public function approvedPayroll(Request $request)
    {
        $MasterId = $request->post('id');
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->addDays(1)->format("Y-m-d");
        $Earning_d = DB::table('earnings_ms')
            ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->where('earnings_ms.id', '=', $MasterId)
            ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
            ->select('earning_ds.*')
            ->get();
        $Affected = null;
        $Affected1 = null;
        DB::beginTransaction();
        foreach ($Earning_d as $item) {
            $earning_ds_details = DB::table('earning_ds')
                ->where('id', $item->id)
                ->get();

            $EarningMasterId = $earning_ds_details[0]->earning_id;
            $TotalAmount = 0;
            $Earning = $earning_ds_details[0]->earning;
            $Bonus = 0;
            if ($earning_ds_details[0]->bonus != "") {
                $Bonus = $earning_ds_details[0]->bonus;
            }
            $TotalAmount = $Earning + $Bonus;

            $earning_ms_details = DB::table('earnings_ms')
                ->where('id', $EarningMasterId)
                ->get();

            $OldEarningAmount = $earning_ms_details[0]->earnings;
            $NewEarningAmount = $OldEarningAmount + $TotalAmount;

            $Affected = DB::table('earnings_ms')
                ->where('id', $EarningMasterId)
                ->update([
                    'earnings' => $NewEarningAmount,
                    'updated_at' => Carbon::now(),
                ]);

            $Affected1 = DB::table('earning_ds')
                ->where('id', $item->id)
                ->update([
                    'approve_status' => 1,
                    'updated_at' => Carbon::now(),
                ]);
        }

        if ($Affected && $Affected1) {
            DB::commit();
            return redirect(url('/admin/payroll/submitted'))->with('message', 'User payroll has been approved successfully');
        } else {
            DB::rollback();
            return redirect(url('/admin/payroll/submitted'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function editEarningPayroll(Request $request)
    {
        $editEarningPayrollId = $request['id'];
        $earningAmount = $request['editEarningAmount'];

        DB::beginTransaction();
        $affected = DB::table('earning_ds')
            ->where('id', $editEarningPayrollId)
            ->update([
                'earning' => $earningAmount,
                'updated_at' => Carbon::now(),
            ]);

        if ($affected) {
            DB::commit();
            echo "Success";
            // if(Auth::user()->role_id == 1){
            //     return redirect(url('/admin/payroll/approve'))->with('message', 'User payroll earning has been updated successfully');
            // }
            // else{
            //     return redirect(url('/general_manager/payroll/approve'))->with('message', 'User payroll earning has been updated successfully');
            // }
        } else {
            DB::rollback();
            echo "Error";
            // if(Auth::user()->role_id == 1){
            //     return redirect(url('/admin/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
            // }
            // else{
            //     return redirect(url('/general_manager/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
            // }
        }
    }

    public function loadPayrollBreakdowns(Request $request)
    {
        $EarningMasterId = $request['EarningMasterId'];
        $StartDate = $request['StartDate'];
        $EndDate = $request['EndDate'];

        $user_payroll_breakdowns = DB::table('earnings_ms')
            ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->join('users', 'users.id', '=', 'earnings_ms.u_id')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('earnings_ms.id', '=', $EarningMasterId)
            ->where('earnings_ms.deleted_at', '=', null)
            ->where('earning_ds.approve_status', '=', null)
            ->where('earning_ds.submitted_at', '=', null)
            ->select('earnings_ms.*', 'earning_ds.id AS EarningDetailsId', 'earning_ds.lead_id', 'earning_ds.sale_id', 'earning_ds.lead_number', 'earning_ds.payout_type', 'earning_ds.earning', 'earning_ds.percentage', 'earning_ds.bonus', 'profiles.firstname', 'profiles.lastname', 'roles.title')
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
                                          <th>Account</th>
                                          <th>Lead Number</th>
                                          <th>Payout Type</th>
                                          <th>Commission</th>
                                          <th>Bonus</th>
                                          <th>Total Sale</th>
                                          <th>Percentage</th>
                                          <th>Edit Commission</th>
                                          <th>Add Bonus</th>
                                      </tr>
                                    </thead>
                                    <tbody>';
        $Counter = 1;
        foreach ($user_payroll_breakdowns as $p_details) {
            $Percentage = "";
            $Earning = 0;
            $Total = 0;
            $Bonus = "";
            if ($p_details->percentage != '') {
                $Percentage = $p_details->percentage . '%';
            }

            $Earning = "$" . $p_details->earning;
            $Total = floatval($p_details->earning);

            if ($p_details->bonus != "") {
                $Total = "$" . ($Total + floatval($p_details->bonus));
                $Bonus = "$" . $p_details->bonus;
            }
            else{
                $Total = "$" . ($Total);
            }

            $data .= '<tr>';
            $data .= '<td>' . $Counter . '</td>';
            $data .= '<td>' . $p_details->firstname . '</td>';
            $data .= '<td>' . $p_details->lastname . '</td>';
            $data .= '<td>' . $p_details->lead_number . '</td>';
            $data .= '<td>' . ucfirst($p_details->payout_type) . '</td>';
            $data .= '<td>' . $Earning . '</td>';
            $data .= '<td>' . $Bonus . '</td>';
            $data .= '<td>' . $Total . '</td>';
            $data .= '<td>' . $Percentage . '</td>';
            $data .= '<td><button class="btn btn-primary" id="editearning_' . $p_details->EarningDetailsId . '" onclick="EditEarningPayroll(this.id)">Edit Commission</button></td>';
            $data .= '<td><button class="btn btn-primary" id="addbonus_' . $p_details->EarningDetailsId . '" onclick="AddBonusEarningPayroll(this.id)">Add Bonus</button></td>';
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

    function Submit(Request $request)
    {
        $MasterId = $request->post('id');
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->addDays(1)->format("Y-m-d");
        $Earning_d = DB::table('earnings_ms')
            ->join('earning_ds', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->where('earnings_ms.id', '=', $MasterId)
            ->whereBetween('earning_ds.created_at', [$StartDate, $EndDate])
            ->select('earning_ds.*')
            ->get();
        $Affected = null;
        foreach ($Earning_d as $item) {
            $Affected = DB::table('earning_ds')
                ->where('id', $item->id)
                ->update([
                    'submitted_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
        }
        // Set submitted Status of Pay Period
        $StartDate = Carbon::parse($request['startDate'])->format("Y-m-d");
        $EndDate = Carbon::parse($request['endDate'])->format("Y-m-d");
        $UserId = Earnings_m::find($MasterId)->u_id;
        $Affected1 = DB::table('pay_periods')
            ->where('user_id', '=', $UserId)
            ->where('startDate', '=', $StartDate)
            ->where('endDate', '=', $EndDate)
            ->update(array(
                'submitted_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ));

        if ($Affected && $Affected1) {
            DB::commit();
            echo json_encode(['status' => 'success']);
//            if (Auth::user()->role_id == 1) {
//                return redirect(url('/admin/payroll/approve'))->with('message', 'User payroll has been submitted successfully');
//            } else {
//                return redirect(url('/general_manager/payroll/approve'))->with('message', 'User payroll has been submitted successfully');
//            }
        } else {
            DB::rollback();
            echo json_encode(['status' => 'failed']);
//            if (Auth::user()->role_id == 1) {
//                return redirect(url('/admin/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
//            } else {
//                return redirect(url('/general_manager/payroll/approve'))->with('error', 'Error! An unhandled exception occurred');
//            }
        }
        exit();
    }
}
