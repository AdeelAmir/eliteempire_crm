<?php

namespace App\Http\Controllers;

use App\HistoryNote;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Sale;
use App\Earnings_m;
use App\Earning_d;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function AllSales()
    {
        $page = "sales";
        $Role = Session::get('user_role');
        // Get List of Members
        $user_id = Auth::id();
        $payee_users = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->whereIn('users.role_id', array(2, 3, 4, 5, 6))
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '<>', $user_id)
            ->select('users.*', 'profiles.firstname', 'profiles.lastname')
            ->get();
        return view('admin.sale.sales', compact('page', 'Role', 'payee_users'));
    }

    public function loadSales(Request $request)
    {
        $Role = Session::get('user_role');
        if ($Role == 1 || $Role == 2) {
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('sales', 'sales.lead_id', '=', 'leads.id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.lead_status', '=', 21)
                    ->select('leads.*', 'profiles.user_id AS UserId', 'profiles.firstname AS FN', 'profiles.lastname AS LN', 'sales.sale_type', 'sales.contract_amount', 'sales.contract_date', 'sales.net_profit', 'sales.net_profit_amount', 'sales.id AS saleId')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('sales', 'sales.lead_id', '=', 'leads.id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.lead_status', '=', 21)
                    ->select('leads.*', 'profiles.user_id AS UserId', 'profiles.firstname AS FN', 'profiles.lastname AS LN', 'sales.sale_type', 'sales.contract_amount', 'sales.contract_date', 'sales.net_profit', 'sales.net_profit_amount', 'sales.id AS saleId')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('sales', 'sales.lead_id', '=', 'leads.id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('sales.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.sale_type', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.contract_amount', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.contract_date', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.net_profit', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.user_id AS UserId', 'profiles.firstname AS FN', 'profiles.lastname AS LN', 'sales.sale_type', 'sales.contract_amount', 'sales.contract_date', 'sales.net_profit', 'sales.net_profit_amount', 'sales.id AS saleId')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('sales', 'sales.lead_id', '=', 'leads.id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('sales.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.sale_type', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.contract_amount', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.contract_date', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('sales.net_profit', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.user_id AS UserId', 'profiles.firstname AS FN', 'profiles.lastname AS LN', 'sales.sale_type', 'sales.contract_amount', 'sales.contract_date', 'sales.net_profit', 'sales.net_profit_amount', 'sales.id AS saleId')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $LeadStatus = 1;
                $LeadsController = new LeadController();
                if ($item->sale_type == "Closed Won") {
                    $LeadStatus = 21;
                }
                $sub_array = array();
                $sub_array['id'] = $SrNo;
                $sub_array['UserId'] = $item->FN . ' ' . $item->LN;
                $sub_array['lead_number'] = $item->lead_number;
                $sub_array['sale_type'] = $LeadsController->GetLeadStatusColor(21);
                $sub_array['contract_amount'] =  $item->contract_amount == ''? "" : "$" . number_format($item->contract_amount);
                $sub_array['contract_date'] = Carbon::parse($item->contract_date)->format('m-d-Y');
                $sub_array['net_profit'] = $item->net_profit == ""? "" : $item->net_profit . "%";
                $sub_array['net_profit_amount'] = $item->net_profit_amount == ""? "" : "$" . number_format($item->net_profit_amount);
                if($item->saleId == ''){
                    /*Display Add Sale Button*/
                    $sub_array['action'] = '<button class="btn btn-primary mr-2" id="addSale_' . $item->id . '_' . $item->lead_number . '_' . $item->contract_amount . '" onclick="AddSaleForm(this.id);"><i class="fas fa-plus-square mr-2"></i>SALE</button>';
                }
                else{
                    $Action = "<button type='button' class='btn btn-primary' id='saleId_" . $item->saleId . "' onclick='ViewSaleDetails(this.id);'>PAYEES</button>";
                    if($this->CheckSaleApproveStatus($item->saleId)){
                        /*Approved Entry*/
                        /*ROLL BACK*/
                        $Action .= "<button type='button' class='btn btn-danger ml-2' id='rollBack_" . $item->saleId . "' onclick='Rollback(this.id);'>ROLL BACK</button>";
                    }
                    else{
                        /*UnApproved Entry*/
                        /*GENERATE*/
                        $Action .= "<button type='button' class='btn btn-primary ml-2' id='generate_" . $item->saleId . "' onclick='GeneratePayroll(this.id);'>GENERATE</button>";
                    }
                    $sub_array['action'] = $Action;
                }
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
    }

    function CheckSaleApproveStatus($SaleId){
        $EarningD = DB::table('earning_ds')
            ->where('sale_id', '=', $SaleId)
            ->where('deleted_at', '=', null)
            ->get();
        $Check = false;
        foreach ($EarningD as $item){
            if($item->approve_status == 1){
                $Check = true;
            }
        }
        return $Check;
    }

    public function add()
    {
        $page = "sale";
        $Role = Session::get('user_role');
        $user_id = Auth::id();
        // Get List of Members
        $payee_users = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->whereIn('users.role_id', array(2, 3, 4, 5, 6))
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '<>', $user_id)
            ->select('users.*', 'profiles.firstname', 'profiles.lastname')
            ->get();

        return view('admin.sale.add-sale', compact('page', 'Role', 'payee_users'));
    }

    public function LeadSaleStore(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadId = $request['leadId'];
        $LeadNumber = $request['leadLeadNumber'];
        $ProductId = $request['leadProductId'];
        $SaleType = $request['addsale_type'];
        $ContractAmount = $request['addsale_contractamount'];
        $ContractDate = $request['addsale_contractdate'];
        $NetProfit = $request['addsale_netprofit'];
//        $Payees = $request['addsale_totalpayeesamount'];
//        $PayeeId = $request['addsale_payees'];
        $LeadStatus = 1;
        $CurrentDate = date('Y-m-d');
        $CalculateSaleNetProfit = (($ContractAmount / 100) * $NetProfit);

        if ($SaleType == "Closed Won") {
            $LeadStatus = 21;
        }

        // Update Lead Status to Closed Won
        DB::beginTransaction();
        $Affected = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->update([
                'lead_status' => $LeadStatus,
                'updated_at' => Carbon::now()
            ]);

        /*History Note Work*/
        $LeadsController = new LeadController();
        $OldLeadStatus = $LeadsController->GetLeadStatusName(DB::table('leads')->where('id', '=', $LeadId)->get()[0]->lead_status);
        $CurrentLeadStatus = $LeadsController->GetLeadStatusName($LeadStatus);
        $HistoryNote = "Changed the status of lead from " . $OldLeadStatus . " to " . $CurrentLeadStatus;
        HistoryNote::create([
            'user_id' => Auth::id(),
            'lead_id' => $LeadId,
            'history_note' => $HistoryNote,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Add Lead Sale Entry in Sale Table
        $Affected1 = Sale::create([
            'lead_id' => $LeadId,
            'lead_number' => $LeadNumber,
            'sale_type' => $SaleType,
            'contract_amount' => $ContractAmount,
            'contract_date' => $ContractDate,
            'product' => $ProductId,
            'net_profit' => $NetProfit,
            'net_profit_amount' => $CalculateSaleNetProfit,
            'sale_date' => $CurrentDate,
        ]);
        $SaleId = $Affected1->id;

        // Add Sale Amount Net Profit into Admin Earning
        // Check if Admin earning entry is avaliable in master table or not
        $user_id = 1;
        $Check_Admin_Master_Earning = DB::table('earnings_ms')
            ->where('u_id', '=', $user_id)
            ->get();

        if (sizeof($Check_Admin_Master_Earning) > 0) {
            $Earning_master_id = $Check_Admin_Master_Earning[0]->id;
            // Update Earning Amount into earning table
            $CalculateSaleNetProfit = (($ContractAmount / 100) * $NetProfit);
            $PreviousEarningAmount = $Check_Admin_Master_Earning[0]->earnings;
            $NewEarningAmount = $PreviousEarningAmount + $CalculateSaleNetProfit;

            $Affected2 = DB::table('earnings_ms')
                ->where('id', '=', $Earning_master_id)
                ->update([
                    'earnings' => $NewEarningAmount,
                    'updated_at' => Carbon::now()
                ]);
        } else {
            // Add Entry in Earning Master Table and Earning Details Table
            $CalculateSaleNetProfit = (($ContractAmount / 100) * $NetProfit);
            $Earning_Master_Affected = Earnings_m::create([
                'u_id' => $user_id,
                'earnings' => $CalculateSaleNetProfit,
            ]);
        }

        // Add Amount to Payee Account
        // Check if Payee earning entry is avaliable in master table or not
        if ($request['payee'] != "") {
            foreach ($request['payee'] as $index => $item) {
                $PayeeId = $item['addsale_payees'];
                $AmountType = $item['addsale_amountType'];
                $Payees = $item['addsale_totalpayeesamount'];
                $Percentage = null;
                $Amount = 0;
                if ($AmountType == 'flat') {
                    $Percentage = null;
                    $Amount = $Payees;
                } else {
                    // $Amount = floatval($ContractAmount) - (floatval($ContractAmount) * floatval($Payees))/100;
                    $Amount = (floatval($ContractAmount) * floatval($Payees)) / 100;
                    $Percentage = $Payees;
                }

                if ($Payees != "" && $Payees != 0 && $PayeeId != "") {
                    $Check_Payee_Master_Earning = DB::table('earnings_ms')
                        ->where('u_id', '=', $PayeeId)
                        ->get();

                    if (sizeof($Check_Payee_Master_Earning) > 0) {
                        $Earning_master_id = $Check_Payee_Master_Earning[0]->id;
                        // Only Entry in Earning Details Table
                        $Affected2 = Earning_d::create([
                            'earning_id' => $Earning_master_id,
                            'sale_id' => $SaleId,
                            'lead_number' => $LeadNumber,
                            'payout_type' => strtolower($SaleType),
                            'percentage' => $Percentage,
                            'earning' => $Amount,
                        ]);
                    } else {
                        // Add Entry in Earning Master Table and Earning Details Table
                        $Earning_Master_Affected = Earnings_m::create([
                            'u_id' => $PayeeId,
                        ]);

                        // Only Entry in Earning Details Table
                        $Affected1 = Earning_d::create([
                            'earning_id' => $Earning_Master_Affected->id,
                            'sale_id' => $SaleId,
                            'lead_number' => $LeadNumber,
                            'payout_type' => strtolower($SaleType),
                            'percentage' => $Percentage,
                            'earning' => $Amount,
                        ]);
                    }
                }
            }
        }

        if ($Affected || $Affected1) {
            DB::commit();
            if ($Role == 1) {
                return redirect(url('/admin/sales'))->with('message', 'Sale has been added successfully');
            } elseif ($Role == 2) {
                return redirect(url('/global_manager/sales'))->with('message', 'Sale has been added successfully');
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/sales'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/global_manager/sales'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    function ViewSaleDetails(Request $request){
        $SaleId = $request->post('SaleId');

        $SaleDetails = DB::table('earning_ds')
            ->join('earnings_ms', 'earning_ds.earning_id', '=', 'earnings_ms.id')
            ->leftJoin('users', 'users.id', '=', 'earnings_ms.u_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            /*->join('sales', 'earning_ds.sale_id', '=', 'sales.id')*/
            /*->join('leads', 'sales.lead_id', '=', 'leads.id')*/
            /*->join('users', 'users.id', '=', 'leads.user_id', 'left')*/
            ->where('earning_ds.sale_id', '=', $SaleId)
            ->where('earning_ds.deleted_at', '=', null)
            ->select( 'earning_ds.id AS EarningDetailsId', 'earning_ds.lead_id', 'earning_ds.sale_id', 'earning_ds.lead_number', 'earning_ds.payout_type', 'earning_ds.earning', 'earning_ds.percentage', 'profiles.firstname', 'profiles.lastname', 'roles.title AS RoleTitle')
            ->get();

        $data = "";
        $Counter = 1;
        foreach ($SaleDetails as $p_details) {
            $Percentage = "";
            $Earning = 0;
            $Total = 0;
            if ($p_details->percentage != '') {
                $Percentage = $p_details->percentage . '%';
            }

            $Earning = "$" . $p_details->earning;
            $Total = floatval($p_details->earning);
            $Total = "$" . ($Total);

            $data .= '<tr>';
            $data .= '<td>' . $Counter . '</td>';
            $data .= '<td>' . $p_details->firstname . ' ' . $p_details->lastname . '</td>';
            $data .= '<td>' . $p_details->RoleTitle . '</td>';
            $data .= '<td>' . $p_details->lead_number . '</td>';
            $data .= '<td>' . ucfirst($p_details->payout_type) . '</td>';
            $data .= '<td>' . $Earning . '</td>';
            $data .= '<td>' . $Total . '</td>';
            $data .= '<td>' . $Percentage . '</td>';
            $data .= '</tr>';
            $Counter++;
        }
        echo json_encode($data);
    }
}