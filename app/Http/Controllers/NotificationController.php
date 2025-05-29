<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

  public function index()
  {

  }

	public function LoadAllUserNotifications() {
		$page = "notifications";
		$user_id = Auth::id();
		// Change the status of all reciever notifications to read
    DB::table('notifications')
            ->where('reciever_id', $user_id)
            ->update([
              'read_status' => 1,
              'updated_at' => Carbon::now()
            ]);

		$user_notifications = DB::table('notifications')
            ->where('reciever_id', $user_id)
            ->select('notifications.*')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

		return view('admin.notification.index', compact('page', 'user_notifications'));
	}

  	public function UnreadUserNotifications(Request $request)
  	{
		$user_id = Auth::id();
		$Role = Session::get('user_role');
		$Url = "";
		$TotalNotifications = 0;
		$ListItems = '';
		$user_notifications = DB::table('notifications')
					->where('reciever_id', $user_id)
					->where('read_status', 0)
					->select('notifications.*')
					->orderBy('created_at', 'desc')
					// ->limit(6)
					->get();

		$TotalNotifications = sizeof($user_notifications);

		if ($TotalNotifications > 0) {
			foreach($user_notifications as $notification)
			{
				$current_date = Carbon::now();
				$created_date = $notification->created_at;
				$created_date = Carbon::parse($created_date);
				$diff = $created_date->diffForHumans($current_date);
				$notification_page = url('all/notifications');

				if ($notification->type == 1) {
						if ($Role == 1) {
								$Url = url('admin/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 2){
								$Url = url('global_manager/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 3){
								$Url = url('acquisition_manager/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 4){
								$Url = url('acquisition_manager/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 5){
								$Url = url('acquisition_representative/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 6){
								$Url = url('disposition_representative/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 7){
								$Url = url('cold_caller/lead/edit/' . $notification->lead_id);
						} elseif ($Role == 9){
								$Url = url('affiliate/lead/edit/' . $notification->lead_id);
						}
				} elseif ($notification->type == 2) {
						if ($Role == 3){
								$Url = url('acquisition_manager/training');
						} elseif ($Role == 4){
								$Url = url('disposition_manager/training');
						} elseif ($Role == 5){
								$Url = url('acquisition_representative/training');
						} elseif ($Role == 6){
								$Url = url('disposition_representative/training');
						} elseif ($Role == 7){
								$Url = url('cold_caller/training');
						} elseif ($Role == 8){
								$Url = url('affiliate/training');
						}
				}

				$ListItems .=
				'<a href="javascript:void(0);" id="$notification_'. $notification->id .'" onclick="MarkAsRead(this.id);window.location.href=\'' . $Url . '\'" class="dropdown-item">
					<div class="icon">
						<i class="far fa-bell"></i>
					</div>
					<div class="content">
						<p>'. $notification->message .'</p>
						<p class="sub-text text-muted">'. $diff .'</p>
					</div>
				</a>';
			}
		}
		else {
			$ListItems .=
			'<a href="javascript:void(0);" class="dropdown-item">
					<div class="icon">
							<i class="far fa-bell"></i>
					</div>
					<div class="content">
							<p>We have zero notification.</p>
							<p class="sub-text text-muted">0 min ago</p>
					</div>
			</a>';
		}

		$Data['Total'] = $TotalNotifications;
		$Data['TotalNewNotifications'] = $TotalNotifications . " New Notifications";
		$Data['Items'] = $ListItems;
		return json_encode($Data);
    }

	public function MarkAsReadNotification(Request $request) {
		$NotificationId = $request['NotificationId'];
		// Get notification details
		$Affected = null;
		$FollowUpType = null;
		$LeadId = null;
		$NotificationDetails = DB::table('notifications')
				->where('id', '=', $NotificationId)
				->get();

		if ($NotificationDetails[0]->followup_type != "") {
			$FollowUpType = $NotificationDetails[0]->followup_type;
			$LeadId = $NotificationDetails[0]->lead_id;
		}

		DB::beginTransaction();
		if ($FollowUpType == 1 || $FollowUpType == 2 || $FollowUpType == 3) {
			$Affected = DB::table('notifications')
					->where('lead_id', '=', $LeadId)
					->where('followup_type', '=', $FollowUpType)
					->update([
							'read_status' => 1,
							'updated_at' => Carbon::now()
					]);
		}
		else {
			$Affected = DB::table('notifications')
					->where('id', '=', $NotificationId)
					->update([
							'read_status' => 1,
							'updated_at' => Carbon::now()
					]);
		}

		if ($Affected) {
			DB::commit();
			echo "Success";
		} else {
			DB::rollback();
			echo "Error";
		}
	}

	public function ClearAllNotification(Request $request) {
		DB::beginTransaction();
		$Affected = DB::table('notifications')
				->where('reciever_id', '=', Auth::id())
				->update([
						'read_status' => 1,
						'updated_at' => Carbon::now()
				]);
		if ($Affected) {
			DB::commit();
			echo "Success";
		} else {
			DB::rollback();
			echo "Error";
		}
	}
}
