<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class TeamsController extends Controller
{
    public function AdminTeam()
    {
        $page = 'teams';
        $Role = Session::get('user_role');
        if ($Role == 4) {
          return view('admin/teams/supervisor-teams', compact('page', 'Role'));
      }
      else {
          return view('admin/teams/teams', compact('page', 'Role'));
      }
  }

  public function AdminTeamsload(Request $request)
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
            $fetch_data = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->offset($start)
            ->limit($limit)
            ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->count();
        } else {
            $fetch_data = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where(function ($query) use ($searchTerm) {
                $query->orWhere('teams.title', 'LIKE', '%' . $searchTerm . '%');
            })
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->offset($start)
            ->limit($limit)
            ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where(function ($query) use ($searchTerm) {
                $query->orWhere('teams.title', 'LIKE', '%' . $searchTerm . '%');
            })
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $team_supervisor = $this->getSupervisorDetails($item->team_supervisor);
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            if ($item->team_type == 1) {
              $sub_array['team_type'] = "Representative";
            }
            elseif ($item->team_type == 2) {
              $sub_array['team_type'] = "Confirmation Agent";
            }
            $sub_array['title'] = $item->title;
            $sub_array['team_supervisor'] = $team_supervisor;
            $sub_array['members'] = count(explode(',', $item->members));
            $sub_array['created_at'] = Carbon::parse($item->created_at)->format('d-m-Y');
            if($Role == 4){
                $sub_array['action'] = '<span><i class="fa fa-eye" id="view_' . $item->id . '" style="color: #4caf50; margin-right:10px; cursor:pointer; font-size:18px;" onclick="ViewTeam(this.id);"></i></span>';
            }
            else{
              if ($Role == 1) {
                $sub_array['action'] = '<span><button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="EditTeam(this.id);"><i class="fas fa-edit"></i></button><button class="btn btn-info mr-2" id="delete_' . $item->id . '" onclick="DeleteTeam(this.id);"><i class="fas fa-trash"></i></button></span>';
            }
            elseif ($Role == 2) {
                $sub_array['action'] = '<span><button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="EditManagerTeam(this.id);"><i class="fas fa-edit"></i></button><button class="btn btn-info mr-2" id="delete_' . $item->id . '" onclick="DeleteTeam(this.id);"><i class="fas fa-trash"></i></button></span>';
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

public function AdminTeamEdit($Id)
{
    $page = 'teams';
    $Role = Session::get('user_role');

    // Manager
    $Managers = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 6)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Confirmation Agent
    $ConfirmationAgent = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 3)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Supervisor
    $Supervisors = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 4)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Representative
    $Users = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 5)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    $Teams = DB::table('teams')
    ->where('id', '=', $Id)
    ->get();

    return view('admin/teams/edit', compact('page', 'Id', 'ConfirmationAgent', 'Users', 'Managers', 'Supervisors', 'Teams', 'Role'));
}

public function AdminTeamUpdate(Request $request)
{
    $Role = Session::get('user_role');
    $Id = $request->post('id');
    $TeamType = $request['editTeamType'];
    $TeamManager = $request['editTeamManager'];
    $TeamSupervisor = $request['editTeamSupervisor'];
    $Title = $request['editTeamName'];
    $Members = $request['editTeamMembers'];
    $ConfirmationAgentTeamMembers = $request['editConfirmationAgentTeamMembers'];
    $_Members = array();

    if ($TeamType == 1) {
      for ($i = 0; $i < count($Members); $i++) {
          $_Members[] = $Members[$i];
      }
      $_Members = implode(',', $_Members);
    }
    elseif ($TeamType == 2) {
      for ($i = 0; $i < count($ConfirmationAgentTeamMembers); $i++) {
          $_Members[] = $ConfirmationAgentTeamMembers[$i];
      }
      $_Members = implode(',', $_Members);
    }

    $affected = null;
    DB::beginTransaction();
    $affected = DB::table('teams')
    ->where('id', '=', $Id)
    ->update([
        'team_type' => $TeamType,
        'title' => $Title,
        'team_manager' => $TeamManager,
        'team_supervisor' => $TeamSupervisor,
        'members' => $_Members,
        'updated_at' => Carbon::now()
    ]);

    if ($affected) {
        Session::flash('message', 'Team updated successfully!');
        DB::commit();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    } else {
        Session::flash('error', 'An unhandled exception occurred!');
        DB::rollBack();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    }
}

public function AdminTeamDelete(Request $request)
{
    $Role = Session::get('user_role');
    $Id = $request->post('id');
    $affected = DB::table('teams')
    ->where('id', '=', $Id)
    ->update(['updated_at' => Carbon::now(),  'deleted_at' => Carbon::now()]);

    if ($affected) {
        Session::flash('message', 'Team deleted successfully!');
        DB::commit();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    } else {
        Session::flash('error', 'An unhandled exception occurred!');
        DB::rollBack();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    }
}

public function AdminTeamAdd()
{
    $page = 'teams';
    $Role = Session::get('user_role');

    // Manager
    $Managers = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 6)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Confirmation Agent
    $ConfirmationAgent = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 3)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Supervisor
    $Supervisors = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 4)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    // Representative
    $Users = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 5)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    return view('admin/teams/add', compact('page', 'ConfirmationAgent', 'Users', 'Managers', 'Supervisors', 'Role'));
}

public function AdminTeamStore(Request $request)
{
    $Role = Session::get('user_role');
    $TeamType = $request['addTeamType'];
    $Title = $request['addTeamName'];
    $TeamManager = $request['addTeamManager'];
    $TeamSupervisor = $request['addTeamSupervisor'];
    $Members = $request['addTeamMembers'];
    $ConfirmationAgentTeamMembers = $request['addConfirmationAgentTeamMembers'];
    $_Members = array();

    if ($TeamType == 1) {
      for ($i = 0; $i < count($Members); $i++) {
          $_Members[] = $Members[$i];
      }
      $_Members = implode(',', $_Members);
    }
    elseif ($TeamType == 2) {
      for ($i = 0; $i < count($ConfirmationAgentTeamMembers); $i++) {
          $_Members[] = $ConfirmationAgentTeamMembers[$i];
      }
      $_Members = implode(',', $_Members);
    }

    $affected = null;
    DB::beginTransaction();
    $affected = \App\Teams::create([
        'team_type' => $TeamType,
        'title' => $Title,
        'team_manager' => $TeamManager,
        'team_supervisor' => $TeamSupervisor,
        'members'    => $_Members,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);

    if ($affected) {
        Session::flash('message', 'Team created successfully!');
        DB::commit();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    } else {
        Session::flash('error', 'An unhandled exception occurred!');
        DB::rollBack();
        if($Role == 1){
            return redirect()->route('admin-teams');
        }
        elseif($Role == 2){
            return redirect()->route('generalManager-teams');
        }
    }
}

public function getSupervisorDetails($id)
{
    $supervisor_name = "";
    $supervisor = DB::table('profiles')
    ->where('user_id', '=', $id)
    ->get();
    if (sizeof($supervisor) > 0) {
        $supervisor_name = $supervisor[0]->firstname . " " . $supervisor[0]->lastname;
    }

    return $supervisor_name;
}

public function SupervisorTeamView($Id)
{
    $page = 'teams';
    $Users = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 5)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

        // Supervisor
    $Supervisors = DB::table('users')
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->where('role_id', '=', 4)
    ->select('users.*', 'profiles.firstname', 'profiles.lastname')
    ->get();

    $Teams = DB::table('teams')
    ->where('id', '=', $Id)
    ->get();

    return view('admin/teams/view', compact('page', 'Id', 'Users', 'Supervisors', 'Teams'));
}

public function SupervisorTeamsload(Request $request)
{
    $Role = Session::get('user_role');
    $user_id = Auth::id();
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
            $fetch_data = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where('teams.team_supervisor', '=', $user_id)
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->offset($start)
            ->limit($limit)
            ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where('teams.team_supervisor', '=', $user_id)
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->count();
        } else {
            $fetch_data = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where('teams.team_supervisor', '=', $user_id)
            ->where(function ($query) use ($searchTerm) {
                $query->orWhere('teams.title', 'LIKE', '%' . $searchTerm . '%');
            })
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->offset($start)
            ->limit($limit)
            ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('teams')
            ->where('teams.deleted_at', '=', null)
            ->where('teams.team_supervisor', '=', $user_id)
            ->where(function ($query) use ($searchTerm) {
                $query->orWhere('teams.title', 'LIKE', '%' . $searchTerm . '%');
            })
            ->select('teams.*')
            ->orderBy($columnName, $columnSortOrder)
            ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $team_supervisor = $this->getSupervisorDetails($item->team_supervisor);
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['title'] = $item->title;
            $sub_array['team_supervisor'] = $team_supervisor;
            $sub_array['members'] = count(explode(',', $item->members));
            $sub_array['created_at'] = Carbon::parse($item->created_at)->format('d-m-Y');
            if($Role == 4){
                $sub_array['action'] = '<span><i class="fa fa-eye" id="view_' . $item->id . '" style="color: #4caf50; margin-right:10px; cursor:pointer; font-size:18px;" onclick="ViewTeam(this.id);"></i></span>';
            }
            else{
                $sub_array['action'] = '<span><button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="EditTeam(this.id);"><i class="fas fa-edit"></i></button><button class="btn btn-info mr-2" id="delete_' . $item->id . '" onclick="DeleteTeam(this.id);"><i class="fas fa-trash"></i></button></span>';
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
