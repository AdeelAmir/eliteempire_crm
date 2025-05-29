<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
use App\Broadcast;
use App\ReadBroadcast;
use App\Helpers\SiteHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BroadcastController extends Controller
{
	public function __construct()
	{
		  $this->middleware('auth');
	}

  public function index()
  {
		$page = "users";
		$Role = Session::get('user_role');
		return view('admin.broadcast.index', compact('page', 'Role'));
  }

  public function Store(Request $request)
  {
      $Role = Session::get('user_role');
      $SenderId = Auth::id();
      // $RecieverId = $request['id'];
			$Users = $request->post('checkAllBox');
      $BroadcastMessage = $request['broadcast_message'];

      DB::beginTransaction();
			$affected = null;
			$affected1 = null;
			foreach ($Users as $key => $RecieverId) {
				$affected = Broadcast::create([
						'sender_id' => $SenderId,
						'message' => $BroadcastMessage,
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
				]);

				$affected1 = ReadBroadcast::create([
						'broadcast_id' => $affected->id,
						'reciever_id' => $RecieverId,
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
				]);
			}

      if ($affected && $affected1) {
        DB::commit();
        if ($Role == 1) {
            return redirect(url('/admin/users'))->with('message', 'Broadcast has been sent successfully.');
        } elseif ($Role == 2) {
            return redirect(url('/global_manager/users'))->with('message', 'Broadcast has been sent successfully.');
        } elseif($Role == 3) {
            return redirect(url('/acquisition_manager/users'))->with('message', 'Broadcast has been sent successfully.');
        } elseif ($Role == 4) {
            return redirect(url('/disposition_manager/users'))->with('message', 'Broadcast has been sent successfully.');
        }
      } else {
        DB::rollback();
        if ($Role == 1) {
            return redirect(url('/admin/users'))->with('error', 'Error! An unhandled exception occurred.');
        } elseif ($Role == 2) {
            return redirect(url('/global_manager/users'))->with('error', 'Error! An unhandled exception occurred.');
        } elseif($Role == 3) {
            return redirect(url('/acquisition_manager/users'))->with('error', 'Error! An unhandled exception occurred.');
        } elseif ($Role == 4) {
            return redirect(url('/disposition_manager/users'))->with('error', 'Error! An unhandled exception occurred.');
        }
      }
   }

	public function SendBroadcastToAll(Request $request)
    {
       $Role = Session::get('user_role');
       $SenderId = Auth::id();
       $BroadcastMessage = $request['broadcast_message'];
			 $affected = null;

			 if ($Role == 1) {
					//Admin send broadcase to all the registered users
					$AllUsers = DB::table('users')
						 ->whereNotIn('role_id', array(1, 10, 11))
						 ->where('deleted_at', '=', null)
						 ->select('users.*')
						 ->get();

					 $affected = Broadcast::create([
 							'sender_id' => $SenderId,
 							'message' => $BroadcastMessage,
 							'created_at' => Carbon::now(),
 							'updated_at' => Carbon::now()
 					]);
					foreach ($AllUsers as $user) {
						$affected1 = ReadBroadcast::create([
			          		'broadcast_id' => $affected->id,
			          		'reciever_id' => $user->id,
			          		'created_at' => Carbon::now(),
			          		'updated_at' => Carbon::now()
			      		]);
					}
			 }
			 elseif ($Role == 2) {
           //Global Manager send broadcase to all the registered users
           $AllUsers = DB::table('users')
              ->whereNotIn('role_id', array(1,2,10,11))
              ->where('deleted_at', '=', null)
              ->select('users.*')
              ->get();

						$affected = Broadcast::create([
              'sender_id' => $SenderId,
              'message' => $BroadcastMessage,
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now()
            ]);
           foreach ($AllUsers as $user) {
						 $affected1 = ReadBroadcast::create([
 			          'broadcast_id' => $affected->id,
 			          'reciever_id' => $user->id,
 			          'created_at' => Carbon::now(),
 			          'updated_at' => Carbon::now()
 			       ]);
           }
			 }
			 elseif ($Role == 3) {
         $RoleIncluded = array(3,5);
         $UserState = SiteHelper::GetCurrentUserState();
         $AllUsers = DB::table('users')
             ->join('profiles', 'profiles.user_id', '=', 'users.id')
             ->where('users.deleted_at', '=', null)
             ->where('users.id', '!=', Auth::id())
             ->whereIn('users.role_id', $RoleIncluded)
             ->where(function ($query) use ($UserState) {
               $query->orWhere('users.parent_id', '=', Auth::id());
               $query->orWhere('profiles.state', '=', $UserState);
             })
             ->select('users.*')
             ->get();

				 $affected = Broadcast::create([
						'sender_id' => $SenderId,
						'message' => $BroadcastMessage,
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
				 ]);
         foreach ($AllUsers as $user) {
					 $affected1 = ReadBroadcast::create([
							'broadcast_id' => $affected->id,
							'reciever_id' => $user->id,
							'created_at' => Carbon::now(),
							'updated_at' => Carbon::now()
					 ]);
         }
			 }
			 elseif ($Role == 4) {
         $RoleIncluded = array(4,6);
         $UserState = SiteHelper::GetCurrentUserState();
         $AllUsers = DB::table('users')
             ->join('profiles', 'profiles.user_id', '=', 'users.id')
             ->where('users.deleted_at', '=', null)
             ->where('users.id', '!=', Auth::id())
             ->whereIn('users.role_id', $RoleIncluded)
             ->where(function ($query) use ($UserState) {
               $query->orWhere('users.parent_id', '=', Auth::id());
               $query->orWhere('profiles.state', '=', $UserState);
             })
             ->select('users.*')
             ->get();

				 $affected = Broadcast::create([
	 					'sender_id' => $SenderId,
	 					'message' => $BroadcastMessage,
	 					'created_at' => Carbon::now(),
	 					'updated_at' => Carbon::now()
	 			 ]);
         foreach ($AllUsers as $user) {
					 $affected1 = ReadBroadcast::create([
							'broadcast_id' => $affected->id,
							'reciever_id' => $user->id,
							'created_at' => Carbon::now(),
							'updated_at' => Carbon::now()
					 ]);
         }
			 }

	     if ($Role == 1) {
	         return redirect(url('/admin/broadcasts'))->with('message', 'Broadcast has been sent successfully.');
	     } elseif ($Role == 2) {
	         return redirect(url('/global_manager/broadcasts'))->with('message', 'Broadcast has been sent successfully.');
	     } elseif($Role == 3) {
	         return redirect(url('/acquisition_manager/broadcasts'))->with('message', 'Broadcast has been sent successfully.');
	     } elseif ($Role == 4) {
	         return redirect(url('/disposition_manager/broadcasts'))->with('message', 'Broadcast has been sent successfully.');
	     }
    }

	public function GetUserBroadcast() {
		 	$RecieverId = Auth::id();
			$BroadcastId = 0;
			$ReadBroadcastId = 0;
			$Message = "";
			$Total = 0;
			$broadcasts = DB::table('broadcasts')
				 ->join('read_broadcasts', 'broadcasts.id', '=', 'read_broadcasts.broadcast_id')
				 ->where('read_broadcasts.reciever_id', '=', $RecieverId)
				 ->where('read_broadcasts.read_status', '=', 0)
				 ->select('broadcasts.*', 'read_broadcasts.id AS ReadBroadcastId')
				 ->limit(1)
				 ->get();

			if ($broadcasts != "" && count($broadcasts) > 0) {
				$Total = 1;
				$BroadcastId = $broadcasts[0]->id;
				$ReadBroadcastId = $broadcasts[0]->ReadBroadcastId;
				$Message = $broadcasts[0]->message;
			}

			$Data['Total'] = $Total;
			$Data['BroadcastId'] = $BroadcastId;
			$Data['ReadBroadcastId'] = $ReadBroadcastId;
			$Data['RecieverId'] = $RecieverId;
	    $Data['Message'] = $Message;
	    return json_encode($Data);
	 }

	public function UpdateReadStatus(Request $request)
    {
       $BroadcastId = $request->post('BroadcastId');
			 $ReadBroadcastId = $request->post('ReadBroadcastId');
			 $BroadcastRecieverId =	$request->post('BroadcastRecieverId');

	     DB::beginTransaction();
	     $affected = DB::table('read_broadcasts')
					 ->where('id', $ReadBroadcastId)
	         ->update([
	             'read_status' => 1,
	             'updated_at' => Carbon::now()
	         ]);
	     if ($affected) {
	         DB::commit();
	         echo "Success";
	     } else {
	         DB::rollback();
	         echo "Failed";
	      }
    }

	public function LoadAllBroadcasts(Request $request)
	{
		 	 $Role = Session::get('user_role');
			 $limit = $request->post('length');
			 $start = $request->post('start');
			 $searchTerm = $request->post('search')['value'];

			 $fetch_data = null;
			 $recordsTotal = null;
			 $recordsFiltered = null;
			 if ($searchTerm == '') {
					 $fetch_data = DB::table('broadcasts')
							 ->where('broadcasts.deleted_at', '=', null)
							 ->where('broadcasts.sender_id', '=', Auth::id())
							 ->select('broadcasts.*')
							 ->orderBy('created_at', 'DESC')
							 ->offset($start)
							 ->limit($limit)
							 ->get();
					 $recordsTotal = sizeof($fetch_data);
					 $recordsFiltered = DB::table('broadcasts')
							 ->where('broadcasts.deleted_at', '=', null)
							 ->where('broadcasts.sender_id', '=', Auth::id())
							 ->select('broadcasts.*')
							 ->orderBy('created_at', 'DESC')
							 ->count();
			 } else {
					 $fetch_data = DB::table('broadcasts')
							 ->where('broadcasts.deleted_at', '=', null)
							 ->where('broadcasts.sender_id', '=', Auth::id())
							 ->where(function ($query) use ($searchTerm) {
									 $query->orWhere('broadcasts.message', 'LIKE', '%' . $searchTerm . '%');
							 })
							 ->select('broadcasts.*')
							 ->orderBy('created_at', 'DESC')
							 ->offset($start)
							 ->limit($limit)
							 ->get();
					 $recordsTotal = sizeof($fetch_data);
					 $recordsFiltered = DB::table('broadcasts')
							 ->where('broadcasts.deleted_at', '=', null)
							 ->where('broadcasts.sender_id', '=', Auth::id())
							 ->where(function ($query) use ($searchTerm) {
									 $query->orWhere('broadcasts.message', 'LIKE', '%' . $searchTerm . '%');
							 })
							 ->select('broadcasts.*')
							 ->orderBy('created_at', 'DESC')
							 ->count();
			 }

			 $data = array();
			 $SrNo = $start + 1;
			 foreach ($fetch_data as $row => $item) {
					 $sub_array = array();
					 $sub_array['sr_no'] = $SrNo;
					 $sub_array['message'] = wordwrap($item->message, 100, "<br>");
					 if ($Role == 1) {
						 $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/broadcast/details/' . $item->id) . '\'" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
					 } elseif ($Role == 2) {
						 $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('global_manager/broadcast/details/' . $item->id) . '\'" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
					 } elseif ($Role == 3) {
						 $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('acquisition_manager/broadcast/details/' . $item->id) . '\'" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
					 } elseif ($Role == 4) {
						 $sub_array['action'] = '<button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('disposition_manager/broadcast/details/' . $item->id) . '\'" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
					 }
					 $SrNo++;
					 $data[] = $sub_array;
			 }

			 $json_data = array(
					 "draw" => intval($request->post('draw')),
					 "recordsTotal" => $recordsTotal,
					 "recordsFiltered" => $recordsFiltered,
					 "data" => $data
			 );

			 echo json_encode($json_data);
	 }

	 public function ViewDetails($BroadcastId) {
		 $page = "users";
		 $Role = Session::get('user_role');
		 return view('admin.broadcast.details', compact('page', 'Role', 'BroadcastId'));
	 }

	 public function LoadAllBroadcastDetails(Request $request)
	 {
			 $limit = $request->post('length');
			 $start = $request->post('start');
			 $searchTerm = $request->post('search')['value'];
			 $BroadcastId = $request->post("BroadcastId");

			 $fetch_data = null;
			 $recordsTotal = null;
			 $recordsFiltered = null;
			 if ($searchTerm == '') {
					 $fetch_data = DB::table('broadcasts')
					 		 ->join('read_broadcasts', 'broadcasts.id', '=', 'read_broadcasts.broadcast_id')
							 ->join('profiles', 'read_broadcasts.reciever_id', '=', 'profiles.user_id')
							 ->where("broadcasts.id", $BroadcastId)
							 ->where("read_broadcasts.read_status", 1)
							 ->select('broadcasts.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
							 ->offset($start)
							 ->limit($limit)
							 ->get();
					 $recordsTotal = sizeof($fetch_data);
					 $recordsFiltered = DB::table('broadcasts')
					 		 ->join('read_broadcasts', 'broadcasts.id', '=', 'read_broadcasts.broadcast_id')
							 ->join('profiles', 'read_broadcasts.reciever_id', '=', 'profiles.user_id')
							 ->where("broadcasts.id", $BroadcastId)
							 ->where("read_broadcasts.read_status", 1)
							 ->select('broadcasts.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
							 ->count();
			 } else {
					 $fetch_data = DB::table('broadcasts')
					 		 ->join('read_broadcasts', 'broadcasts.id', '=', 'read_broadcasts.broadcast_id')
							 ->join('profiles', 'read_broadcasts.reciever_id', '=', 'profiles.user_id')
							 ->where("broadcasts.id", $BroadcastId)
							 ->where("read_broadcasts.read_status", 1)
							 ->where(function ($query) use ($searchTerm) {
									 $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
									 $query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
									 $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
							 })
							 ->select('broadcasts.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
							 ->offset($start)
							 ->limit($limit)
							 ->get();
					 $recordsTotal = sizeof($fetch_data);
					 $recordsFiltered = DB::table('broadcasts')
					 		 ->join('read_broadcasts', 'broadcasts.id', '=', 'read_broadcasts.broadcast_id')
							 ->join('profiles', 'read_broadcasts.reciever_id', '=', 'profiles.user_id')
							 ->where("broadcasts.id", $BroadcastId)
							 ->where("read_broadcasts.read_status", 1)
							 ->where(function ($query) use ($searchTerm) {
									 $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
									 $query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
									 $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
							 })
							 ->select('broadcasts.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
							 ->count();
			 }

			 $data = array();
			 $SrNo = $start + 1;
			 foreach ($fetch_data as $row => $item) {
					 $FullName = $item->firstname . " " . $item->middlename . " " . $item->lastname;
					 $sub_array = array();
					 $sub_array['sr_no'] = $SrNo;
					 $sub_array['user'] = $FullName;
					 $SrNo++;
					 $data[] = $sub_array;
			 }

			 $json_data = array(
					 "draw" => intval($request->post('draw')),
					 "recordsTotal" => $recordsTotal,
					 "recordsFiltered" => $recordsFiltered,
					 "data" => $data
			 );

			 echo json_encode($json_data);
	 }
}
