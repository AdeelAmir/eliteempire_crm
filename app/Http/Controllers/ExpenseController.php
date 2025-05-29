<?php

namespace App\Http\Controllers;

use Carbon\Traits\Date;
use Illuminate\Http\Request;
use App\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ExpenseController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function AdminAllExpense()
    {
        $page = "expenses";
        $Role = Session::get('user_role');
        return view('admin.expense.expenses', compact('page', 'Role'));
    }

    public function LoadAdminAllExpense(Request $request)
    {
        $Role = Session::get('user_role');
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];
        $StartDate = $request->post('StartDate');
        $EndDate = $request->post('EndDate');

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;

        if ($searchTerm == '') {
            $fetch_data = DB::table('expenses')
                ->where('expenses.deleted_at', '=', null)
                ->where(function ($query) use ($StartDate, $EndDate) {
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('expenses.expense_date', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('expenses.*')
                ->orderBy('expenses.expense_date', 'desc')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();

            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('expenses')
                ->where('expenses.deleted_at', '=', null)
                ->where(function ($query) use ($StartDate, $EndDate) {
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('expenses.expense_date', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('expenses.*')
                ->orderBy('expenses.expense_date', 'desc')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('expenses')
                ->where(function ($query) {
                    $query->where([
                        ['expenses.deleted_at', '=', null]
                    ]);
                })
                ->where(function ($query) use ($StartDate, $EndDate) {
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('expenses.expense_date', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('expenses.id', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.description', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.expense_date', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.vendor', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.location', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.currency', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.note', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('expenses.*')
                ->orderBy('expenses.expense_date', 'desc')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('expenses')
                ->where(function ($query) {
                    $query->where([
                        ['expenses.deleted_at', '=', null],
                    ]);
                })
                ->where(function ($query) use ($StartDate, $EndDate) {
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('expenses.expense_date', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('expenses.id', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.description', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.expense_date', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.vendor', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.location', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.currency', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('expenses.note', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('expenses.*')
                ->orderBy('expenses.expense_date', 'desc')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['description'] = wordwrap($item->description, 20, '<br>');
            if ($item->currency == "USD") {
              $sub_array['total'] = "$" . $item->total;
            } else {
              $sub_array['total'] = $item->other_currency_name . $item->total;
            }
            $sub_array['expense_date'] = Carbon::parse($item->expense_date)->format("m-d-Y");
            $sub_array['vendor'] = wordwrap($item->vendor, 10, '<br>');
            $sub_array['location'] = wordwrap($item->location, 10, '<br>');
            $sub_array['note'] = wordwrap($item->note, 20, '<br>');
            if ($Role == 1 || $Role == 2) {
                $sub_array['action'] = $active_ban . '<button class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editExpenses(this.id);"><i class="fas fa-edit"></i><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteExpense(this.id);"><i class="fas fa-trash"></i></button>';
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

    public function AdminAddNewExpense()
    {
        $page = "expenses";
        $maxDate = Carbon::now()->subYears(15);
        $maxDate = $maxDate->toDateString();
        $Role = Session::get('user_role');


        return view('admin.expense.add_new_expenses', compact('page', 'maxDate', 'Role'));
    }

    public function AdminExpenseStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $Description = $request['description'];
        $Total = $request['total'];
        $Date = $request['date'];
        $Vendor = $request['vendor'];
        $Location = $request['location'];
        $Currency = $request['currency'];
        $OtherCurrencyName = null;
        $Rate = null;
        $Notes = $request['notes'];

        if ($Currency == "Others") {
            $OtherCurrencyName = $request['other_currency_name'];
            $Rate = $request['rate'];
        }

        $Expense = Expense::create([
            'description' => $Description,
            'total' => $Total,
            'expense_date' => Carbon::parse($Date)->format('Y-m-d'),
            'vendor' => $Vendor,
            'location' => $Location,
            'currency' => $Currency,
            'other_currency_name' => $OtherCurrencyName,
            'exchange_rate' => $Rate,
            'note' => $Notes,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        if ($Expense) {
            if($UserRole == 1){
                return redirect(url('/admin/expenses'))->with('message', 'Expenses has been added successfully');
            }
            elseif ($UserRole == 2){
                return redirect(url('/global_manager/expenses'))->with('message', 'Expenses has been added successfully');
            }
        } else {
            if($UserRole == 1){
                return redirect(url('/admin/expenses'))->with('message', 'An unhandled error occurred');
            }
            elseif ($UserRole == 2){
                return redirect(url('/global_manager/expenses'))->with('message', 'An unhandled error occurred');
            }
        }
    }

    public function AdminEditExpense(Request $request)
    {
        $Role = Session::get('user_role');
        $page = "expenses";
        $expense_id = $request['id'];
        $expense_details = DB::table('expenses')
            ->where('expenses.id', $expense_id)
            ->where('expenses.deleted_at', '=', null)
            ->get();

        $role_details = DB::table('roles')
            ->where('deleted_at', '=', null)
            ->get();

        $maxDate = Carbon::now()->subYears(15);
        $maxDate = $maxDate->toDateString();

        return view('admin.expense.edit-expenses', compact('page', 'expense_id', 'expense_details', 'role_details', 'maxDate', 'Role'));
    }

    public function AdminUpdateExpense(Request $request)
    {
        $UserRole = Session::get('user_role');
        $Expense_id = $request['id'];
        $Description = $request['description'];
        $Total = $request['total'];
        $Date = $request['date'];
        $Vendor = $request['vendor'];
        $Location = $request['location'];
        $Currency = $request['currency'];
        $OtherCurrencyName = null;
        $Rate = null;
        $Notes = $request['notes'];

        if ($Currency == "Others") {
            $OtherCurrencyName = $request['other_currency_name'];
            $Rate = $request['rate'];
        }

        DB::beginTransaction();
        $affected = DB::table('expenses')
            ->where('id', $Expense_id)
            ->update([
                'description' => $Description,
                'total' => $Total,
                'expense_date' => Carbon::parse($Date)->format('Y-m-d'),
                'vendor' => $Vendor,
                'location' => $Location,
                'currency' => $Currency,
                'other_currency_name' => $OtherCurrencyName,
                'exchange_rate' => $Rate,
                'note' => $Notes,
                'updated_at' => Carbon::now()
            ]);

        if ($affected) {
            DB::commit();
            if($UserRole == 1){
                return redirect(url('/admin/expenses'))->with('message', 'Expense record has been updated successfully');
            }
            elseif ($UserRole == 2){
                return redirect(url('/global_manager/expenses'))->with('message', 'Expense record has been updated successfully');
            }
        } else {
            DB::rollBack();
            if($UserRole == 1){
                return redirect(url('/admin/expenses'))->with('message', 'An unhandled error occurred');
            }
            elseif ($UserRole == 2){
                return redirect(url('/global_manager/expenses'))->with('message', 'An unhandled error occurred');
            }
        }
    }

    public function AdminDeleteExpense(Request $request)
    {
        $Role = Session::get('user_role');
        $Expense_id = $request['id'];
        DB::beginTransaction();
        $affected = DB::table('expenses')
            ->where('id', $Expense_id)
            ->update([
                'updated_at' => Carbon::now(),
                'deleted_at' => Carbon::now()
            ]);
        if ($affected) {
            DB::commit();
            if ($Role == 1) {
                return redirect(url('/admin/expenses'))->with('message', 'Expenses has been deleted successfully');
            }
            elseif ($Role == 2) {
                return redirect(url('/global_manager/expenses'))->with('message', 'Expenses has been deleted successfully');
            }
        }
        else{
            DB::rollBack();
            if ($Role == 1) {
                return redirect(url('/admin/expenses'))->with('message', 'An unhandled error occurred');
            }
            elseif ($Role == 2) {
                return redirect(url('/global_manager/expenses'))->with('message', 'An unhandled error occurred');
            }
        }
    }
}
