<?php


namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use App\PayoutSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayOutController extends Controller
{
    //
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function AdminAllPayOut()
	{
		$page = "payouts";
        $Settings = Settings::find(1);
		$Role = Session::get('user_role');
		return view('admin.payout.payouts', compact('page','Role', 'Settings'));
	}

	public function LoadAdminAllPayout(Request $request)
	{
			$Role = Session::get('user_role');
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
        	$fetch_data = DB::table('payout_settings')
                ->join('roles', 'payout_settings.role_id', '=', 'roles.id')
                ->where('payout_settings.deleted_at', '=', null)
                ->select('payout_settings.*', 'roles.title AS role_title')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();

        	$recordsTotal = sizeof($fetch_data);
        	$recordsFiltered = DB::table('payout_settings')
                ->join('roles', 'payout_settings.role_id', '=', 'roles.id')
                ->where('payout_settings.deleted_at', '=', null)
                ->select('payout_settings.*', 'roles.title AS role_title')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('payout_settings')
                ->join('roles', 'payout_settings.role_id', '=', 'roles.id')
                ->where(function ($query) {
                    $query->where([
                        ['payout_settings.deleted_at', '=', null]
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.id', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.payout_type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.amount', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.percentage', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('payout_settings.*', 'roles.title AS role_title')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('payout_settings')
                ->join('roles', 'payout_settings.role_id', '=', 'roles.id')
                ->where(function ($query) {
                    $query->where([
                        ['payout_settings.deleted_at', '=', null],
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.id', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.payout_type', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.amount', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('payout_settings.percentage', 'LIKE', '%' . $searchTerm . '%');

                })
                ->select('payout_settings.*', 'roles.title AS role_title')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
        	$sub_array = array();
        	$sub_array['id'] = $SrNo;
            $sub_array['role_title'] = $item->role_title;
        	$sub_array['payout_type'] = $item->payout_type;
        	$sub_array['amount'] = $item->amount . "$";
        	$sub_array['percentage'] = $item->percentage;
        	if($Role == 1) {
        		$sub_array['action'] = $active_ban . '<button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="editPayout(this.id);"><i class="fas fa-edit"></i>';
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

    public function AdminEditPayout(Request $request)
    {
    	$Role = Session::get('user_role');
    	$page = "payouts";
    	$payout_settings_id = $request['id'];
    	$payout_details = DB::table('payout_settings')
    	->where('payout_settings.id', $payout_settings_id)
    	->where('payout_settings.deleted_at', '=', null)
    	->select('payout_settings.id AS id', 'payout_settings.payout_type AS payout_type','payout_settings.amount AS amount' , 'payout_settings.percentage AS percentage')
    	->get();

    	$role_details = DB::table('roles')
    	->where('deleted_at', '=', null)
    	->get();

    	$maxDate = Carbon::now()->subYears(15);
    	$maxDate = $maxDate->toDateString();

    	return view('admin.payout.edit-payout', compact('page', 'payout_settings_id', 'payout_details', 'role_details', 'maxDate', 'Role'));
    }

    public function AdminUpdatePayout(Request $request)
    {
    	$UserRole = Session::get('user_role');
    	$Payout_id = $request['id'];
			$PayoutAmount = $request['amount'];

    	$affected = DB::table('payout_settings')
    	->where('id', $Payout_id)
    	->update([
    		'amount' => $PayoutAmount,
    		'updated_at' => Carbon::now(),
    	]);

    	return redirect(url('/admin/payout'))->with('message', 'Payout record has been updated successfully');

    }
}
