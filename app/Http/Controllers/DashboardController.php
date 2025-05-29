<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $Role = Session::get('user_role');
        if ($Role == 1) {
            return redirect()->route('adminDashboard');
        } elseif ($Role == 2) {
            return redirect()->route('globalManagerDashboard');
        } elseif ($Role == 3) {
            // return redirect()->route('acquisitionManagerDashboard');
            return redirect(url('/acquisition_manager/dashboard'));
        } elseif ($Role == 4) {
            // return redirect()->route('dispositionManagerDashboard');
            return redirect(url('/disposition_manager/dashboard'));
        } elseif ($Role == 5) {
            return redirect()->route('acquisitionRepresentativeTraining');
        } elseif ($Role == 6) {
            return redirect()->route('dispositionRepresentativeDashboard');
        } elseif ($Role == 7) {
            return redirect()->route('coldCallerDashboard');
        } elseif ($Role == 8) {
            return redirect()->route('affiliateDashboard');
        } elseif ($Role == 9) {
            return redirect()->route('realtorDashboard');
        }
    }

    public function LoadDashboard()
    {
        $page = "dashboard";
        $Role = Session::get('user_role');

        // Announcement
        $Announcement = DB::table('announcements')
            ->where('announcements.deleted_at', '=', null)
            ->where('announcements.type', '=', 2)
            ->where('announcements.status', '=', 1)
            ->select('announcements.*')
            ->get();

        if ($Role == 1) {
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));

            // Todays New Leads
            $NewLeads = DB::table('leads')
                ->where('lead_type', '=', 1)
                ->whereDate('created_at', '=', $CurrentDate)
                ->where('deleted_at', '=', null)
                ->count();

            // Daily Confirmed Leads
            $DailyConfirmedLeads = DB::table('leads')
                ->whereIn('leads.lead_status', array(1, 4, 5))
                ->where('lead_type', '=', 1)
                ->whereDate('lead_date', '=', $CurrentDate)
                ->where('deleted_at', '=', null)
                ->count();

            // Monthly Confirmed Leads
            $MonthlyConfirmedLeads = DB::table('leads')
                ->whereIn('leads.lead_status', array(1, 4, 5))
                ->where('lead_type', '=', 1)
                ->whereBetween('lead_date', array($first_date, $last_date))
                ->where('deleted_at', '=', null)
                ->count();

            // Total Call Requests
            $TotalCallRequests = DB::table('leads')
                ->where('lead_type', '=', 2)
                ->whereBetween('created_at', array($first_date, $last_date))
                ->where('deleted_at', '=', null)
                ->count();

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 2) {

            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 3) {
            /*Acquisition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 4) {
            /*Disposition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 5) {
            return redirect(url('/acquisition_representative/training'));
        } elseif ($Role == 6) {
            /*Disposition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 7) {
            /*Disposition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 8) {
            /*Disposition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));

        } elseif ($Role == 9) {
            /*Disposition Manager Dashboard*/
            $CurrentDate = date('Y-m-d');
            $first_date = date('Y-m-d', strtotime('first day of this month'));
            $last_date = date('Y-m-d', strtotime('last day of this month'));
            $NewLeads = 0;
            $DailyConfirmedLeads = 0;
            $MonthlyConfirmedLeads = 0;
            $TotalCallRequests = 0;

            return view('admin.index', compact('page', 'Role', 'NewLeads', 'DailyConfirmedLeads', 'MonthlyConfirmedLeads', 'TotalCallRequests', 'Announcement'));
        }
    }

    public function TraineeDashboard(Request $request)
    {
        $page = "dashboard";
        $Role = Session::get('user_role');

        // Announcement
        $Announcement = DB::table('announcements')
            ->where('announcements.deleted_at', '=', null)
            ->where('announcements.type', '=', 2)
            ->where('announcements.status', '=', 1)
            ->select('announcements.*')
            ->get();

        // $TrainingAssignment = DB::table('training_assignments')
        //     ->where('user_id', Auth::id())
        //     ->where('status', 0)
        //     ->count();

        $TrainingAssignment = DB::table('training_assignment_folders')
            ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
            ->where('training_assignment_folders.user_id', '=', Auth::id())
            ->where('training_assignment_folders.completion_rate', '<', 100)
            ->where('folders.required', '=', 1)
            ->count();

        if ($Role == 3) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/acquisition_manager/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        } elseif ($Role == 4) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/disposition_manager/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        } elseif ($Role == 5) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/acquisition_representative/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        } elseif ($Role == 6) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/disposition_representative/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        } elseif ($Role == 7) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/cold_caller/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        } elseif ($Role == 8) {
            if ($TrainingAssignment > 0) {
                return redirect(url('/affiliate/training'));
            } else {
                return view('admin.index', compact('page', 'Role', 'Announcement'));
            }
        }
    }

    function Training()
    {
        $page = "training_room";
        $Role = Session::get('user_role');

        // $TrainingAssignment = DB::table('training_assignments')->where('user_id', Auth::id())
        //                       ->where('status', 0)->count();
        // $TrainingAssignment = DB::table('training_assignment_folders')
        //     ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
        //     ->where('training_assignment_folders.user_id', '=', Auth::id())
        //     ->where('training_assignment_folders.completion_rate', '<', 100)
        //     ->where('folders.required', '=', 1)
        //     ->count();
        /*After training, when login to acquisition rep. system will send you to dashboard by default, not the training room.*/
        // if ($Role == 5) {
        //     if ($TrainingAssignment == 0) {
        //         return redirect()->to('/acquisition_representative/dashboard');
        //     } else {
        //         // return view('admin.training-room.training', compact('page', 'Role'));
        //         return view('admin.training-room.training-folder', compact('page', 'Role'));
        //     }
        // }
        // return view('admin.training-room.training', compact('page', 'Role'));

        // Calculate user training room progress and update the completion rate
        $TotalAssignments = 0;
        $TotalCompleted = 0;
        $TrainingRoomFolder = DB::table('training_assignment_folders')
                              ->where('user_id', Auth::id())
                              ->where('deleted_at', null)
                              ->get();

        foreach ($TrainingRoomFolder as $key => $folder) {
            $TotalTrainingAssignments = DB::table('training_assignments')
                                  ->where('user_id', Auth::id())
                                  ->where('training_assignment_folder_id', $folder->id)
                                  ->count();
            $TotalCompletedAssignments = DB::table('training_assignments')
                                  ->where('user_id', Auth::id())
                                  ->where('training_assignment_folder_id', $folder->id)
                                  ->where('status', 1)
                                  ->count();
            $CompletionRate = (($TotalCompletedAssignments / $TotalTrainingAssignments) * 100);
            DB::beginTransaction();
            $Affected = DB::table('training_assignment_folders')
                        ->where('user_id', Auth::id())
                        ->where('id', $folder->id)
                        ->update([
                            'completion_rate' => $CompletionRate,
                            'updated_at' => Carbon::now()
                        ]);
            DB::commit();
        }
        return view('admin.training-room.training-folder', compact('page', 'Role'));
    }

    function TrainingCourse($CourseId)
    {
        $page = "training_room";
        $Role = Session::get('user_role');

        // Course Details
        $CourseDetails = \Illuminate\Support\Facades\DB::table('training_assignment_folders')
            ->join('folders', 'training_assignment_folders.folder_id', '=', 'folders.id')
            ->where('training_assignment_folders.id', '=', $CourseId)
            ->select('training_assignment_folders.*', 'folders.name AS FolderName', 'folders.picture', 'folders.required')
            ->get();

        $CourseName = $CourseDetails[0]->FolderName;
        $CourseCompletionRate = $CourseDetails[0]->completion_rate;

        return view('admin.training-room.training', compact('page', 'Role', 'CourseId', 'CourseName', 'CourseCompletionRate'));
    }

    function ConstantValues(){
        $page = 'constants';
        $Constants = DB::table('constants')->get();
        $Role = Session::get('user_role');
        return view('admin.constants', compact('page', 'Constants', 'Role'));
    }

    function SetConstantValues(Request $request)
    {
        DB::beginTransaction();
        $Affected1 = DB::table('constants')
            ->where('id', '=', 1)
            ->update([
                'value' => $request->post('ARV_SALES_CLOSING_COST_CONSTANT')
            ]);
        $Affected2 = DB::table('constants')
            ->where('id', '=', 2)
            ->update([
                'value' => $request->post('WHOLESALES_CLOSING_COST_CONSTANT')
            ]);
        $Affected3 = DB::table('constants')
            ->where('id', '=', 3)
            ->update([
                'value' => $request->post('INVESTOR_PROFIT_CONSTANT')
            ]);
        $Affected4 = DB::table('constants')
            ->where('id', '=', 4)
            ->update([
                'value' => $request->post('OFFER_LOWER_RANGE_CONSTANT')
            ]);
        $Affected5 = DB::table('constants')
            ->where('id', '=', 5)
            ->update([
                'value' => $request->post('OFFER_HIGHER_RANGE_CONSTANT')
            ]);

        DB::commit();
        return redirect()->to('admin/magicnumber')->with('message', 'Magic number updated successfully!');
    }
}
