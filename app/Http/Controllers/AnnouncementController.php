<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
use App\Announcement;
use App\ReadAnnouncement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $page = "users";
        $Role = Session::get('user_role');
        return view('admin.announcement.index', compact('page', 'Role'));
    }

    public function AddNewAnnouncement()
    {
        $page = "users";
        $Role = Session::get('user_role');
        return view('admin.announcement.add-new-announcement', compact('page', 'Role'));
    }

    public function Store(Request $request)
    {
        $AnnouncementType = $request['type'];
        $AnnouncementMessage = $request['message'];

        DB::beginTransaction();
        DB::table('announcements')
            ->update([
                'status' => 0,
                'updated_at' => Carbon::now()
            ]);

        $affected = Announcement::create([
            'type' => $AnnouncementType,
            'message' => $AnnouncementMessage,
            'expiration' => $request->post('_appointmentTime'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/announcements'))->with('message', 'Announcement has been added successfully.');
        } else {
            DB::rollback();
            return redirect(url('/admin/announcements'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function LoadAdminAllAnnouncements(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = DB::table('announcements')
                ->where('announcements.deleted_at', '=', null)
                ->select('announcements.*')
                ->orderBy('created_at', 'DESC')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('announcements')
                ->where('announcements.deleted_at', '=', null)
                ->select('announcements.*')
                ->orderBy('created_at', 'DESC')
                ->count();
        } else {
            $fetch_data = DB::table('announcements')
                ->where('announcements.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('announcements.message', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('announcements.*')
                ->orderBy('created_at', 'DESC')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('announcements')
                ->where('announcements.deleted_at', '=', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('announcements.message', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('announcements.*')
                ->orderBy('created_at', 'DESC')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $AnnouncementType = "Website";
            $status = '<span class="badge badge-success">Active</span>';
            $active_ban = '<button class="btn btn-danger mr-2" id="deactive_' . $item->id . '" onclick="deactiveAnnouncement(this.id);" data-toggle="tooltip" title="Deactive Announcement"><i class="fas fa-ban"></i></button>';
            if ($item->type == 2) {
                $AnnouncementType = "CRM";
            }
            if ($item->status == 0) {
                $status = '<span class="badge badge-danger">Deactive</span>';
                $active_ban = '<button class="btn btn-success mr-2" id="active_' . $item->id . '" onclick="activeAnnouncement(this.id);" data-toggle="tooltip" title="Active Announcement"><i class="fas fa-check"></i></button>';
            }
            $sub_array = array();
            $sub_array['sr_no'] = $SrNo;
            // $sub_array['announcement_type'] = $AnnouncementType;
            $sub_array['message'] = wordwrap($item->message, 100, "<br>");
            $sub_array['expiration'] = '<span>' . Carbon::parse($item->expiration)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->expiration)->format('g:i a') . '</span>';
            $sub_array['status'] = $status;
            $sub_array['action'] = $active_ban . '<button class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editAnnouncement(this.id);" data-toggle="tooltip" title="Edit Announcement"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme mr-2" id="delete_' . $item->id . '" onclick="deleteAnnouncement(this.id);" data-toggle="tooltip" title="Delete Announcement"><i class="fas fa-trash"></i></button><button class="btn greenActionButtonTheme mr-2" onclick="window.location.href=\'' . url('admin/announcement/details/' . $item->id) . '\'" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
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

    public function active(Request $request)
    {
        $id = $request->post('AnnouncementId');
        $check = DB::table('announcements')
            ->where('status', '=', 1)
            ->where('deleted_at', '=', null)
            ->count();

        if ($check > 0) {
            echo "Failed";
        } else {
            DB::beginTransaction();
            $affected = DB::table('announcements')
                ->where('id', $id)
                ->update([
                    'status' => 1,
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
    }

    public function deactive(Request $request)
    {
        $id = $request->post('AnnouncementId');
        DB::beginTransaction();
        $affected = DB::table('announcements')
            ->where('id', $id)
            ->update([
                'status' => 0,
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

    public function AdminEditAnnouncement(Request $request)
    {
        $page = "users";
        $Role = Session::get('user_role');
        $AnnouncementId = $request['id'];
        $announcement_details = DB::table('announcements')->where('id', $AnnouncementId)->get();
        return view('admin.announcement.edit-announcement', compact('page', 'Role', 'announcement_details', 'AnnouncementId'));
    }

    public function Update(Request $request)
    {
        $AnnouncementId = $request['announcement_id'];
        $AnnouncementType = $request['type'];
        $AnnouncementMessage = $request['message'];

        DB::beginTransaction();
        $affected = DB::table('announcements')
            ->where('id', '=', $AnnouncementId)
            ->update([
                'type' => $AnnouncementType,
                'message' => $AnnouncementMessage,
                'expiration' => $request->post('_appointmentTime'),
                'updated_at' => Carbon::now()
            ]);

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/announcements'))->with('message', 'Record has been updated successfully.');
        } else {
            DB::rollback();
            return redirect(url('/admin/announcements'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function Delete(Request $request)
    {
        $AnnouncementId = $request['id'];
        DB::beginTransaction();
        $affected = DB::table('announcements')
            ->where('id', '=', $AnnouncementId)
            ->update([
                'deleted_at' => Carbon::now()
            ]);

        if ($affected) {
            DB::commit();
            return redirect(url('/admin/announcements'))->with('message', 'Record has been deleted successfully.');
        } else {
            DB::rollback();
            return redirect(url('/admin/announcements'))->with('error', 'Error! An unhandled exception occurred');
        }
    }

    // User who read announcement
    public function Read(Request $request)
    {
        $AnnouncementId = $request['AnnouncementId'];
        // Check if this user have already read this announcement or not
        $ReadAnnouncementDetails = DB::table('read_announcements')
            ->where('announcement_id', '=', $AnnouncementId)
            ->where('user_id', '=', Auth::id())
            ->count();

        if ($ReadAnnouncementDetails == 0) {
          DB::beginTransaction();
          $affected = ReadAnnouncement::create([
            'announcement_id' => $AnnouncementId,
            'user_id' => Auth::id()
          ]);

          if ($affected) {
              DB::commit();
              echo "Success";
          } else {
              DB::rollback();
              echo "Failed";
          }
        } else {
          echo "Success";
        }
    }

    public function ViewDetails($AnnouncementId) {
      $page = "users";
      $Role = Session::get('user_role');
      return view('admin.announcement.details', compact('page', 'Role', 'AnnouncementId'));
    }

    public function LoadAdminAllAnnouncementDetails(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];
        $AnnouncementId = $request->post("AnnouncementId");

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = DB::table('read_announcements')
                ->join('profiles', 'read_announcements.user_id', '=', 'profiles.user_id')
                ->where("read_announcements.announcement_id", $AnnouncementId)
                ->select('read_announcements.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('read_announcements')
                ->join('profiles', 'read_announcements.user_id', '=', 'profiles.user_id')
                ->where("read_announcements.announcement_id", $AnnouncementId)
                ->select('read_announcements.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
                ->count();
        } else {
            $fetch_data = DB::table('read_announcements')
                ->join('profiles', 'read_announcements.user_id', '=', 'profiles.user_id')
                ->where("read_announcements.announcement_id", $AnnouncementId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('read_announcements.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('read_announcements')
                ->join('profiles', 'read_announcements.user_id', '=', 'profiles.user_id')
                ->where("read_announcements.announcement_id", $AnnouncementId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.middlename', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('read_announcements.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
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
