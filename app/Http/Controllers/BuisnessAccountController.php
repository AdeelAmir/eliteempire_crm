<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\ServingLocation;
use App\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BuisnessAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* Admin Section - Start */
    public function AdminAllBuisnessAccounts()
    {
        $Role = Session::get('user_role');
        $page = "buissness_account";
        return view('admin.business-account.buissness_accounts', compact('page', 'Role'));
    }

    public function LoadAdminAllBuisnessAccounts(Request $request)
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

        $RoleIncluded = array(9, 10, 11);

        if ($Role == 6) {
          if ($searchTerm == '') {
              $fetch_data = DB::table('users')
                  ->where('users.deleted_at', '=', null)
                  ->where('users.parent_id', '=', Auth::id())
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->offset($start)
                  ->limit($limit)
                  ->get();
              $recordsTotal = sizeof($fetch_data);
              $recordsFiltered = DB::table('users')
                  ->where('users.deleted_at', '=', null)
                  ->where('users.parent_id', '=', Auth::id())
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->count();
          } else {
              $fetch_data = DB::table('users')
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->where(function ($query) {
                      $query->where([
                          ['users.deleted_at', '=', null],
                          ['users.parent_id', '=', Auth::id()],
                      ]);
                  })
                  ->where(function ($query) use ($searchTerm) {
                      $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->offset($start)
                  ->limit($limit)
                  ->get();
              $recordsTotal = sizeof($fetch_data);
              $recordsFiltered = DB::table('users')
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->where(function ($query) {
                      $query->where([
                          ['users.deleted_at', '=', null],
                          ['users.parent_id', '=', Auth::id()],
                      ]);
                  })
                  ->where(function ($query) use ($searchTerm) {
                      $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->count();
          }
        }
        else
        {
          if ($searchTerm == '') {
              $fetch_data = DB::table('users')
                  ->where('users.deleted_at', '=', null)
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->offset($start)
                  ->limit($limit)
                  ->get();
              $recordsTotal = sizeof($fetch_data);
              $recordsFiltered = DB::table('users')
                  ->where('users.deleted_at', '=', null)
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->count();
          } else {
              $fetch_data = DB::table('users')
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->where(function ($query) {
                      $query->where([
                          ['users.deleted_at', '=', null]
                      ]);
                  })
                  ->where(function ($query) use ($searchTerm) {
                      $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->offset($start)
                  ->limit($limit)
                  ->get();
              $recordsTotal = sizeof($fetch_data);
              $recordsFiltered = DB::table('users')
                  ->join('profiles', 'profiles.user_id', '=', 'users.id')
                  ->join('roles', 'roles.id', '=', 'users.role_id')
                  ->whereIn('users.role_id', $RoleIncluded)
                  ->where(function ($query) {
                      $query->where([
                          ['users.deleted_at', '=', null]
                      ]);
                  })
                  ->where(function ($query) use ($searchTerm) {
                      $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                      $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->select('users.id AS id', 'users.userId AS userId', 'users.role_id AS role_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.buisnesss_name', 'profiles.phone AS phone', 'users.status')
                  ->orderBy($columnName, $columnSortOrder)
                  ->count();
          }
        }

        $data = array();
        $SrNo = $start + 1;
        $status = "";
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
            if ($Role == 1 || $Role == 2 || $Role == 6) {
                if ($item->status == 1) {
                    $status = '<span class="badge badge-success">Active</span>';
                    if ($item->last_logged_in != "") {
                        $lastlogindate = Carbon::parse($item->last_logged_in);
                        if ($lastlogindate->isToday()) {
                            $status .= "<br><br><span style='font-size:13px;'>Today</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        } elseif ($lastlogindate->isYesterday()) {
                            $status .= "<br><br><span style='font-size:13px;'>Yesterday</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        } else {
                            $status .= "<br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('m/d/Y') . "</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        }
                    }
                    $active_ban = '<button class="btn btn-danger mr-2" id="ban_' . $item->id . '" onclick="banUser(this.id);" data-toggle="tooltip" title="Ban User"><i class="fas fa-ban"></i></button>';
                } else {
                    $status = '<span class="badge badge-danger">Ban</span>';
                    if ($item->last_logged_in != "") {
                        $lastlogindate = Carbon::parse($item->last_logged_in);
                        if ($lastlogindate->isToday()) {
                            $status .= "<br><br><span style='font-size:13px;'>Today</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        } elseif ($lastlogindate->isYesterday()) {
                            $status .= "<br><br><span style='font-size:13px;'>Yesterday</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        } else {
                            $status .= "<br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('m/d/Y') . "</span><br><br><span style='font-size:13px;'>" . Carbon::parse($item->last_logged_in)->format('g:i a') . "</span>";
                        }
                    }
                    $active_ban = '<button class="btn btn-success mr-2" id="active_' . $item->id . '" onclick="activeUser(this.id);" data-toggle="tooltip" title="Active User"><i class="fas fa-check"></i></button>';
                }
            }
            $Phone_Email = "";
            $Phone_Email .= "<b><a href='tel: " . $item->phone . "' style='color: black;'>" . $item->phone . "</a></b><br><br>";
            $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a><br><br>";
            $sub_array = array();
            $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox allAccountsCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualAccountCheckbox();" />';
            $sub_array['id'] = $SrNo;
            if ($item->firstname != "" && $item->lastname != "") {
                $sub_array['user_information'] = '<span>' . wordwrap("<strong>" . $item->firstname . " " . $item->lastname . "</strong><br><br>" . $item->userId . "<br><br>" . $item->role, 30, '<br>') . '</span>';
            } else {
                $sub_array['user_information'] = '<span>' . wordwrap("<strong>" . $item->buisnesss_name . "</strong><br><br>" . $item->userId . "<br><br>" . $item->role, 30, '<br>') . '</span>';
            }
            $sub_array['contact'] = '<span>' . $Phone_Email . '</span>';
            $sub_array['status'] = $status;
            if ($Role == 1 || $Role == 2 || $Role == 6) {
                if ($item->role_id == 9) {
                    if ($Role == 1) {
                      // Realtor
                      $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editRealtor(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="filter_' . $item->id . '" onclick="window.location.href=\'' . url('admin/user/state/filter/' . $item->id) . '\'" data-toggle="tooltip" title="User State Filter"><i class="fa fa-filter"></i></button>';
                    } elseif ($Role == 2) {
                      // Realtor
                      $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editRealtor(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                    } elseif ($Role == 6) {
                      // Realtor
                      $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editRealtor(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                    }
                } elseif ($item->role_id == 10) {
                    // Investor
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editInvestor(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                } elseif ($item->role_id == 11) {
                    // Title Company
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editTitleCompany(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                }
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
