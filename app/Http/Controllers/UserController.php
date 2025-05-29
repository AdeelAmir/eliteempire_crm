<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\TrainingAssignment;
use App\VerificationDocuments;
use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\UserActivity;
use App\UserDepartmentFilter;
use App\TrainingAssignmentFolder;
use App\Mail\UserRegistrationEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function AdminAllUsers()
    {
        $page = "users";
        $Role = Session::get('user_role');
        $states = DB::table('states')->get();
        $cities = DB::table('cities')->get();
        $roles = DB::table('roles')->whereNotIn('id', array(1,9,10,11))->get();
        return view('admin.users.users', compact('page', 'Role', 'states', 'cities', 'roles'));
    }

    public function AdminAddNewUsers()
    {
        $page = "users";
        $maxDate = Carbon::now()->subYears(15);
        $maxDate = $maxDate->toDateString();
        $Role = Session::get('user_role');

        // State list
        $states = DB::table('states')
            ->get();

        // Counties list
        $counties = DB::table('counties')
            ->get();

        // Cities list
        $cities = DB::table('cities')
            ->get();

        // Roles list
        $roles = DB::table('roles')
            ->where('deleted_at', null)
            ->get();

        return view('admin.users.add-new-user', compact('page', 'cities', 'counties', 'states', 'roles', 'maxDate', 'Role'));
    }

    public function AdminUserStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $FirstName = ucwords(strtolower($request['firstname']));
        $MiddleName = ucwords(strtolower($request['middlename']));
        $LastName = ucwords(strtolower($request['lastname']));
        $Dob = $request['dob'];
        $Email = $request['email'];
        $Phone = $request['phone'];
        $Phone2 = $request['phone2'];
        $Street = $request['street'];
        $City = $request['city'];
        $State = $request['state'];
        $ZipCode = $request['zipcode'];
        $DocumentName = $request['documentname'];
        $DocumentNumbers = $request['documentnumbers'];
        /*Password Work*/
        $RandomString = \Illuminate\Support\Str::random(8) . '!@^*';
        $RandomString = substr(str_shuffle($RandomString), 0, 8);
        $Password = $RandomString;
        $Role = $request['role'];
        $UserId = substr($FirstName, 0, 1) . substr($LastName, 0, 1) . rand(10000, 99999);
        $DocumentNumbers = array();
        $DocumentNames = array();
        $FileNames = array();

        if ($Role == 4) {
            // first check if there is already a dispotion in this state then not allowed to create
            $DispositionManagers = DB::table('users')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where('users.role_id', $Role)
                ->where('profiles.state', $State)
                ->where('users.deleted_at', '=', null)
                ->count();

            if ($DispositionManagers > 0) {
                if ($UserRole == 1) {
                    return redirect(url('/admin/users'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                } elseif ($UserRole == 2) {
                    return redirect(url('/global_manager/users'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                } elseif ($UserRole == 4) {
                    return redirect(url('disposition_manager/add/user'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                }
            }
        }

        if ($request->has('documents')) {
            foreach ($request->post('documents') as $index => $document) {
                if (isset($request['documents'][$index]['documentFile'])) {
                    $CurrentFile = $request['documents'][$index]['documentFile'];
                    $FileStoragePath = '/public/user-documents/';
                    $Extension = $CurrentFile->extension();
                    $file = $CurrentFile->getClientOriginalName();
                    $FileName = pathinfo($file, PATHINFO_FILENAME);
                    $FileName = $FileName . '-' . date('Y-m-d') . rand(100, 1000) . '.' . $Extension;
                    $result = $CurrentFile->storeAs($FileStoragePath, $FileName);
                    $FileNames[] = $FileName;
                    $DocumentNames[] = $document['documentname'];
                    $DocumentNumbers[] = $document['documentnumbers'];
                }
            }
        }

        DB::beginTransaction();
        $affected = User::create([
            'userId' => $UserId,
            'parent_id' => Auth::id(),
            'email' => $Email,
            'password' => bcrypt($Password),
            'role_id' => $Role,
            'created_at' => Carbon::now(),
        ]);

        $NewUserId = $affected->id;
        $affected1 = Profile::create([
            'user_id' => $NewUserId,
            'firstname' => $FirstName,
            'middlename' => $MiddleName,
            'lastname' => $LastName,
            'dob' => $Dob,
            'phone' => $Phone,
            'phone2' => $Phone2,
            'street' => $Street,
            'city' => $City,
            'state' => $State,
            'zipcode' => $ZipCode,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        foreach ($FileNames as $index => $fileName) {
            $Affected6 = VerificationDocuments::create([
                'user_id' => $NewUserId,
                'document_name' => $DocumentNames[$index],
                'document_number' => $DocumentNumbers[$index],
                'document' => $fileName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        // Get user first name and last name of person who create this user
        $UserFullName = "";
        if ($MiddleName != "") {
            $UserFullName = $FirstName . " " . $LastName;
        } else {
            $UserFullName = $FirstName . " " . $MiddleName . " " . $LastName;
        }

        // Add an entry in user activity how create this user
        $affected5 = UserActivity::create([
            'user_id' => $affected->id,
            'sender_id' => Auth::id(),
            'message' => $UserFullName . " added successfully!",
            'created_at' => Carbon::now(),
        ]);

        // TRAINING ROOM ASSIGNMENT WORK
        $TrainingRoomFolders = DB::table('folders')
            ->where('role_id', $Role)
            ->where('deleted_at', '=', null)
            ->orderBy('order_no', 'ASC')
            ->get();

        foreach ($TrainingRoomFolders as $folder) {
            $Affected2 = TrainingAssignmentFolder::create([
                'user_id' => $NewUserId,
                'folder_id' => $folder->id,
                'completion_rate' => 0,
                'created_at' => Carbon::now(),
            ]);

            $TrainingRoom = DB::table('training_rooms')
                ->where('role_id', $Role)
                ->where('folder_id', $folder->id)
                ->where('deleted_at', '=', null)
                ->orderBy('order_no', 'ASC')
                ->get();

            foreach ($TrainingRoom as $room) {
                $Affected3 = TrainingAssignment::create([
                    'user_id' => $NewUserId,
                    'assignment_type' => $room->type,
                    'training_assignment_folder_id' => $Affected2->id,
                    'assignment_id' => $room->id,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        /* Send email to user with login credientials - Start */
        $data = array(
            'Name' => $FirstName,
            'Email' => $Email,
            'Password' => $Password
        );
        // Mail::send('email.user-registration-email', $data, function ($message) use ($Email) {
        //     $message->to($Email, 'Elite Empire')->subject('User Registration');
        //     $message->from($_ENV['MAIL_FROM_ADDRESS'], 'Elite Empire');
        // });

        // Mail::to($Email)->send(new UserRegistrationEmail($data));

        /* Send email to user with login credientials - End */

        if ($affected && $affected1 && $affected5) {
            DB::commit();
            if ($UserRole == 1) {
                return redirect(url('/admin/users'))->with('message', 'User has been registered successfully');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/users'))->with('message', 'User has been registered successfully');
            } elseif ($UserRole == 3) {
                return redirect(url('acquisition_manager/users'))->with('message', 'User has been registered successfully');
            } elseif ($UserRole == 4) {
                return redirect(url('disposition_manager/users'))->with('message', 'User has been registered successfully');
            }
        } else {
            DB::rollback();
            if ($UserRole == 1) {
                return redirect(url('/admin/users'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/users'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($UserRole == 3) {
                return redirect(url('acquisition_manager/users'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($UserRole == 4) {
                return redirect(url('disposition_manager/users'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function LoadAdminAllUsers(Request $request)
    {
        $Role = Session::get('user_role');

        $filter_city = $request['city'];
        $filter_state = $request['state'];
        $filter_status = $request['status'];
        $filter_role = $request['role'];

        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;

        $data = array();
        $SrNo = 1;
        $status = "";
        $active_ban = "";

        if ($Role == 1 || $Role == 2) {
            $RoleNotIncluded = array(1, 9, 10, 11);
            if ($searchTerm == '') {
                $fetch_data = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereNotIn('users.role_id', $RoleNotIncluded)
                    ->where(function ($query) use ($filter_city, $filter_state, $filter_status, $filter_role) {
                        if ($filter_city != "") {
                            $query->where('profiles.city', '=', $filter_city);
                        }
                        if ($filter_state != "") {
                            $query->where('profiles.state', '=', $filter_state);
                        }
                        if ($filter_status != "") {
                            $query->where('users.status', '=', $filter_status);
                        }
                        if ($filter_role != "") {
                            $query->where('users.role_id', '=', $filter_role);
                        }
                    })
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereNotIn('users.role_id', $RoleNotIncluded)
                    ->where(function ($query) use ($filter_city, $filter_state, $filter_status, $filter_role) {
                        if ($filter_city != "") {
                            $query->where('profiles.city', '=', $filter_city);
                        }
                        if ($filter_state != "") {
                            $query->where('profiles.state', '=', $filter_state);
                        }
                        if ($filter_status != "") {
                            $query->where('users.status', '=', $filter_status);
                        }
                        if ($filter_role != "") {
                            $query->where('users.role_id', '=', $filter_role);
                        }
                    })
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('users')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->whereNotIn('users.role_id', $RoleNotIncluded)
                    ->where(function ($query) {
                        $query->where([
                            ['users.deleted_at', '=', null],
                            ['users.id', '!=', Auth::id()]
                        ]);
                    })
                    ->where(function ($query) use ($filter_city, $filter_state, $filter_status, $filter_role) {
                        if ($filter_city != "") {
                            $query->where('profiles.city', '=', $filter_city);
                        }
                        if ($filter_state != "") {
                            $query->where('profiles.state', '=', $filter_state);
                        }
                        if ($filter_status != "") {
                            $query->where('users.status', '=', $filter_status);
                        }
                        if ($filter_role != "") {
                            $query->where('users.role_id', '=', $filter_role);
                        }
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->whereNotIn('users.role_id', $RoleNotIncluded)
                    ->where(function ($query) {
                        $query->where([
                            ['users.deleted_at', '=', null],
                            ['users.id', '!=', Auth::id()]
                        ]);
                    })
                    ->where(function ($query) use ($filter_city, $filter_state, $filter_status, $filter_role) {
                        if ($filter_city != "") {
                            $query->where('profiles.city', '=', $filter_city);
                        }
                        if ($filter_state != "") {
                            $query->where('profiles.state', '=', $filter_state);
                        }
                        if ($filter_status != "") {
                            $query->where('users.status', '=', $filter_status);
                        }
                        if ($filter_role != "") {
                            $query->where('users.role_id', '=', $filter_role);
                        }
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('roles.title', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.dob', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            foreach ($fetch_data as $row => $item) {
                if ($Role == 1 || $Role == 2) {
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
                        $active_ban = '<button type="button" class="btn btn-danger mr-2" id="ban_' . $item->id . '" onclick="banUser(this.id);" data-toggle="tooltip" title="Ban User"><i class="fas fa-ban"></i></button>';
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
                        $active_ban = '<button type="button" class="btn btn-success mr-2" id="active_' . $item->id . '" onclick="activeUser(this.id);" data-toggle="tooltip" title="Active User"><i class="fas fa-check"></i></button>';
                    }
                }
                $Phone_Email = "";
                $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a><br><br>";
                if($item->city != ''){
                    $Phone_Email .= "<span class='text-black'>" . $item->city . ", " . "</span>";
                }
                if($item->state != ''){
                    $Phone_Email .= "<span class='text-black'>" . $item->state . "</span>";
                }
                $sub_array = array();
                $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox allUsersCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualUserCheckbox();" />';
                $sub_array['id'] = $SrNo;
                $sub_array['user_information'] = '<span>' . wordwrap("<strong>" . $item->firstname . " " . $item->lastname . "</strong><br><br>" . $item->userId . "<br><br>" . $item->role, 30, '<br>') . '</span>';
                $sub_array['contact'] = '<span>' . $Phone_Email . '</span>';
                $sub_array['status'] = $status;
                if ($Role == 1) {
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editUser(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="filter_' . $item->id . '" onclick="window.location.href=\'' . url('admin/user/state/filter/' . $item->id) . '\'" data-toggle="tooltip" title="User State Filter"><i class="fa fa-filter"></i></button>';
                } elseif ($Role == 2) {
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);" data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editUser(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                } else {
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="changePassword_' . $item->id . '" onclick="ChangePassword(this);"  data-toggle="tooltip" title="Change Password"><i class="fas fa-user-lock"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editManagerUser(this.id);"  data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button>';
                }
                $SrNo++;
                $data[] = $sub_array;
            }
        }
        elseif ($Role == 3) {
            $RoleIncluded = array(3, 5);
            if ($searchTerm == '') {
                $fetch_data = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
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
                            ['users.id', '!=', Auth::id()]
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
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->where(function ($query) {
                        $query->where([
                            ['users.deleted_at', '=', null],
                            ['users.id', '!=', Auth::id()]
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
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $UserState = SiteHelper::GetCurrentUserState();
            foreach ($fetch_data as $row => $item) {
                if ($item->parent_id == Auth::id() || $UserState == $item->state) {
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
                    }
                    $Phone_Email = "";
                    $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a><br><br>";
                    if($item->city != ''){
                        $Phone_Email .= "<span class='text-black'>" . $item->city . ", " . "</span>";
                    }
                    if($item->state != ''){
                        $Phone_Email .= "<span class='text-black'>" . $item->state . "</span>";
                    }
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox allUsersCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualUserCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_information'] = '<span>' . wordwrap("<strong>" . $item->firstname . " " . $item->lastname . "</strong><br><br>" . $item->userId . "<br><br>" . $item->role, 30, '<br>') . '</span>';
                    $sub_array['contact'] = '<span>' . $Phone_Email . '</span>';
                    $sub_array['status'] = $status;
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editUser(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }
        }
        elseif ($Role == 4) {
            $RoleIncluded = array(4, 6);
            if ($searchTerm == '') {
                $fetch_data = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->where('users.deleted_at', '=', null)
                    ->where('users.id', '!=', Auth::id())
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
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
                            ['users.id', '!=', Auth::id()]
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
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('users')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->whereIn('users.role_id', $RoleIncluded)
                    ->where(function ($query) {
                        $query->where([
                            ['users.deleted_at', '=', null],
                            ['users.id', '!=', Auth::id()]
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
                        $query->orWhere('profiles.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('users.id AS id', 'users.userId AS userId', 'users.parent_id AS parent_id', 'users.email AS email', 'users.last_logged_in', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.state AS state', 'profiles.city AS city', 'users.status')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $UserState = SiteHelper::GetCurrentUserState();
            foreach ($fetch_data as $row => $item) {
                if ($item->parent_id == Auth::id() || $UserState == $item->state) {
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
                    }
                    $Phone_Email = "";
                    $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a><br><br>";
                    if($item->city != ''){
                        $Phone_Email .= "<span class='text-black'>" . $item->city . ", " . "</span>";
                    }
                    if($item->state != ''){
                        $Phone_Email .= "<span class='text-black'>" . $item->state . "</span>";
                    }
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox allUsersCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualUserCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    $sub_array['user_information'] = '<span>' . wordwrap("<strong>" . $item->firstname . " " . $item->lastname . "</strong><br><br>" . $item->userId . "<br><br>" . $item->role, 30, '<br>') . '</span>';
                    $sub_array['contact'] = '<span>' . $Phone_Email . '</span>';
                    $sub_array['status'] = $status;
                    $sub_array['action'] = $active_ban . '<button type="button" class="btn greenActionButtonTheme mr-2" id="edit_' . $item->id . '" onclick="editUser(this.id);" data-toggle="tooltip" title="Edit User"><i class="fas fa-edit"></i></button><button type="button" class="btn greenActionButtonTheme mr-2" id="activity_' . $item->id . '" onclick="MakeUserActivityTable(this.id);" data-toggle="tooltip" title="User Activity"><i class="fa fa-tasks"></i></button>';
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }
        }

        // Custom Pagination
        $Count = 0;
        $recordsFiltered = sizeof($fetch_data);
        $SubFetchData = array();
        foreach ($data as $key => $value) {
            if ($Count >= $start) {
                $SubFetchData[] = $value;
            }
            if (sizeof($SubFetchData) == $limit) {
                break;
            }
            $Count++;
        }
        $recordsTotal = sizeof($SubFetchData);

        $json_data = array(
            "draw" => intval($request->post('draw')),
            "iTotalRecords" => $recordsTotal,
            "iTotalDisplayRecords" => $recordsFiltered,
            "aaData" => $SubFetchData
        );

        echo json_encode($json_data);
    }

    public function CalculateUserProgress($user_id)
    {
        $UserProgress = 0;
        //Total User Assigned Leads
        $TotalAssignedLeads = DB::table('lead_assignments')
            ->where('user_id', $user_id)
            ->count();

        if ($TotalAssignedLeads > 0) {
            // Total User Completed Leads
            $TotalCompletedAssignedLeads = DB::table('lead_assignments')
                ->where('user_id', $user_id)
                ->where('status', '=', 1)
                ->count();

            $UserProgress = (($TotalCompletedAssignedLeads / $TotalAssignedLeads) * 100);
            return '<span class="badge badge-primary pl-2 pr-2">' . bcdiv($UserProgress, 1, 0) . '%</span>';
        } else {
            return '';
        }
    }

    public function AdminDeleteUser(Request $request)
    {
        $Role = Session::get('user_role');
        $Users = $request->post('checkAllBox');
        DB::beginTransaction();
        $affected = null;
        foreach ($Users as $key => $user_id) {
          $affected = DB::table('users')
              ->where('id', $user_id)
              ->update([
                  'updated_at' => Carbon::now(),
                  'deleted_at' => Carbon::now()
              ]);
        }
        if ($affected) {
            DB::commit();
            return redirect()->back()->with('message', 'User has been deleted successfully');
        } else {
            DB::rollback();
            return redirect()->back()->with('error', 'Error! An unhandled exception occurred');
        }
    }

    public function AdminEditUser(Request $request)
    {
        $Role = Session::get('user_role');
        $page = "users";
        $user_id = $request['id'];

        $user_details = DB::table('users')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            ->where('users.deleted_at', '=', null)
            ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'users.role_id AS role', 'profiles.firstname AS firstname', 'profiles.middlename AS middlename', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.phone2 AS phone2', 'profiles.state AS state', 'profiles.county AS county', 'profiles.city AS city', 'profiles.street AS street', 'profiles.zipcode AS zipcode', 'profiles.identity1 AS identity1', 'profiles.identity2 AS identity2', 'profiles.document_name AS document_name', 'profiles.document_numbers AS document_numbers', 'profiles.secondary_email AS secondary_email')
            ->get();

        $VerificationDocuments = DB::table('verification_documents')
            ->where('user_id', '=', $user_id)
            ->get();
        $maxDate = Carbon::now()->subYears(15);
        $maxDate = $maxDate->toDateString();

        // States list
        $states = DB::table('states')
            ->get();

        // Counties list
        $counties = DB::table('locations')
            ->where('state_name', '=', $user_details[0]->state)
            ->orderBy("county_name", "ASC")
            ->get()
            ->unique("county_name");

        // Cities list
        $cities = DB::table('locations')
            ->where('state_name', '=', $user_details[0]->state)
            ->orderBy("city", "ASC")
            ->get()
            ->unique("city");

        // Roles list
        $roles = DB::table('roles')
            ->where('deleted_at', null)
            ->get();

        return view('admin.users.edit-user', compact('page', 'cities', 'counties', 'states', 'user_id', 'user_details', 'maxDate', 'roles', 'Role', 'VerificationDocuments'));
    }

    public function AdminUpdateUser(Request $request)
    {
        $UserRole = Session::get('user_role');
        $user_id = $request['id'];
        $FirstName = ucwords(strtolower($request['firstname']));
        $MiddleName = ucwords(strtolower($request['middlename']));
        $LastName = ucwords(strtolower($request['lastname']));
        $Dob = $request['dob'];
        $Email = $request['email'];
        $Phone = $request['phone'];
        $Phone2 = $request['phone2'];
        $Street = $request['street'];
        $City = $request['city'];
        $State = $request['state'];
        $County = $request['county'];
        $ZipCode = $request['zipcode'];
        $OldRole = $request['userRole_Old'];
        $Role = $request['role'];
        $SecondaryEmail = '';

        if ($request->has('secondary_email')) {
            $SecondaryEmail = $request->post('secondary_email');
        }

        DB::beginTransaction();
        /* Old Documents Handling */
        DB::table('verification_documents')
            ->where('user_id', '=', $user_id)
            ->delete();
        $FileNames = array();
        $DocumentNames = array();
        $DocumentNumbers = array();

        if ($OldRole != $Role) {
            if ($Role == 4) {
                // first check if there is already a dispotion in this state then not allowed to create
                $DispositionManagers = DB::table('users')
                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->where('users.role_id', $Role)
                    ->where('profiles.state', $State)
                    ->where('deleted_at', '=', null)
                    ->count();

                if ($DispositionManagers > 0) {
                    if ($UserRole == 1) {
                        return redirect(url('/admin/users'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                    } elseif ($UserRole == 2) {
                        return redirect(url('/global_manager/users'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                    } elseif ($UserRole == 4) {
                        return redirect(url('disposition_manager/add/user'))->with('error', 'Error! Only one disposition manager is allowed in this state');
                    }
                }
            }
        }

        if ($request->has('documentOld') && $request->has('documentOld') && $request->has('documentNumberOld')) {
            // Unlink Old Files
            if ($request->has('fileToDelete')) {
                foreach ($request->post('fileToDelete') as $file) {
                    $Path = base_path() . '\\' . 'public\\storage\\user-documents\\' . $file;
                    unlink($Path);
                }
            }
            $FileNames = $request->post('documentOld');
            $DocumentNames = $request->post('documentNameOld');
            $DocumentNumbers = $request->post('documentNumberOld');
            foreach ($FileNames as $index => $fileName) {
                $Affected6 = VerificationDocuments::create([
                    'user_id' => $user_id,
                    'document_name' => $DocumentNames[$index],
                    'document_number' => $DocumentNumbers[$index],
                    'document' => $fileName,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        /*Old Documents Handling*/

        /* New Documents Handling */
        $FileNames = array();
        $DocumentNames = array();
        $DocumentNumbers = array();
        if ($request->has('documents')) {
            foreach ($request->post('documents') as $index => $document) {
                if (isset($request['documents'][$index]['documentFile'])) {
                    $CurrentFile = $request['documents'][$index]['documentFile'];
                    $FileStoragePath = '/public/user-documents/';
                    $Extension = $CurrentFile->extension();
                    $file = $CurrentFile->getClientOriginalName();
                    $FileName = pathinfo($file, PATHINFO_FILENAME);
                    $FileName = $FileName . '-' . date('Y-m-d') . rand(100, 1000) . '.' . $Extension;
                    $result = $CurrentFile->storeAs($FileStoragePath, $FileName);
                    $FileNames[] = $FileName;
                    $DocumentNames[] = $document['documentname'];
                    $DocumentNumbers[] = $document['documentnumbers'];
                }
            }
            foreach ($FileNames as $index => $fileName) {
                $Affected6 = VerificationDocuments::create([
                    'user_id' => $user_id,
                    'document_name' => $DocumentNames[$index],
                    'document_number' => $DocumentNumbers[$index],
                    'document' => $fileName,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        /* New Documents Handling */

        // Check if user role is changed create training room for this user
        if ($Role != $OldRole) {
            // Delete Training Room
            DB::table('training_assignments')->where('user_id', $user_id)->delete();

            // Create new training room for user
            $TrainingRoom = DB::table('training_rooms')
                ->where('role_id', $Role)
                ->where('deleted_at', '=', null)
                ->get();

            foreach ($TrainingRoom as $room) {
                $Affected2 = TrainingAssignment::create([
                    'user_id' => $user_id,
                    'assignment_type' => $room->type,
                    'assignment_id' => $room->id,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        if ($UserRole == 1) {
            $affected = DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'email' => $Email,
                    'role_id' => $Role,
                    'updated_at' => Carbon::now(),
                ]);

            $affected1 = DB::table('profiles')
                ->where('user_id', $user_id)
                ->update([
                    'firstname' => $FirstName,
                    'middlename' => $MiddleName,
                    'lastname' => $LastName,
                    'dob' => $Dob,
                    'phone' => $Phone,
                    'phone2' => $Phone2,
                    'street' => $Street,
                    'city' => $City,
                    'state' => $State,
                    'county' => $County,
                    'zipcode' => $ZipCode,
                    'secondary_email' => $SecondaryEmail,
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            $affected1 = DB::table('profiles')
                ->where('user_id', $user_id)
                ->update([
                    'phone' => $Phone,
                    'phone2' => $Phone2,
                    'street' => $Street,
                    'city' => $City,
                    'state' => $State,
                    'county' => $County,
                    'zipcode' => $ZipCode,
                    'secondary_email' => $SecondaryEmail,
                    'updated_at' => Carbon::now(),
                ]);
        }

        DB::commit();
        if ($UserRole == 1) {
            return redirect(url('/admin/users'))->with('message', 'User record has been updated successfully');
        } elseif ($UserRole == 2) {
            return redirect(url('/global_manager/users'))->with('message', 'User record has been updated successfully');
        } elseif ($UserRole == 3) {
            return redirect(url('/acquisition_manager/users'))->with('message', 'User record has been updated successfully');
        } elseif ($UserRole == 4) {
            return redirect(url('/disposition_manager/users'))->with('message', 'User record has been updated successfully');
        }
    }

    function ChangePassword(Request $request)
    {
        $UserRole = Session::get('user_role');
        $UserId = $request->post('user_id');
        $Password = $request->post('newPassword');
        DB::beginTransaction();
        DB::table('users')->where('id', '=', $UserId)->update([
            'password' => bcrypt($Password),
            'updated_at' => Carbon::now()
        ]);
        DB::commit();

        /*if ($UserRole == 1) {
            // return redirect(url('/admin/users'))->with('message', 'User Password has been updated successfully');
            return redirect()->back()->with('message', 'User Password has been updated successfully');
        } elseif ($UserRole == 2) {
            // return redirect(url('/global_manager/users'))->with('message', 'User Password has been updated successfully');
            return redirect()->back()->with('message', 'User Password has been updated successfully');
        }*/
        return redirect()->back()->with('message', 'User Password has been updated successfully');
    }

    function EditProfile()
    {
        $page = 'dashboard';
        $Role = Session::get('user_role');
        $Profile = DB::table('profiles')->where('user_id', '=', Auth::id())->get();
        return view('admin.profile', compact('page', 'Role', 'Profile'));
    }

    function UpdatePersonalDetails(Request $request)
    {
        $UserRole = Session::get('user_role');
        $FirstName = $request['firstName'];
        $MiddleName = $request['middleName'];
        $LastName = $request['lastName'];
        $DOB = $request['dob'];
        $Phone = $request['phone'];
        $Phone2 = $request['phone2'];
        /*$County = $request['county'];*/
        $City = $request['city'];
        $Street = $request['street'];
        $State = $request['state'];
        $ZipCode = $request['zipcode'];
        $OldProfilePicture = $request['oldProfilePicture'];
        $NewProfilePicture = "";

        // userProfileUpdate
        if ($request->file('userProfileUpdate')) {
            // Removing Old File if Exists
            if ($OldProfilePicture != '' && $OldProfilePicture != null) {
                unlink(base_path() . '/public/storage/profile-pics/' . $OldProfilePicture);
            }
            //Storing new file
            $Filename = substr($FirstName, 0, 1) . substr($LastName, 0, 1);
            $Extension = $request->file('userProfileUpdate')->extension();
            $Filename = $Filename . '-' . mt_rand(1000000, 9999999) . '.' . $Extension;
            $NewProfilePicture = $Filename;
            $result = $request->file('userProfileUpdate')->storeAs('/public/profile-pics/', $Filename);
        } else {
            $NewProfilePicture = $OldProfilePicture;
        }

        if ($UserRole == 1 || $UserRole == 2 || $UserRole == 4 || $UserRole == 5) {
            DB::beginTransaction();
            DB::table('profiles')
                ->where('user_id', '=', Auth::id())
                ->update([
                    'firstname' => $FirstName,
                    'middlename' => $MiddleName,
                    'lastname' => $LastName,
                    'dob' => $DOB,
                    'phone' => $Phone,
                    'phone2' => $Phone2,
                    /*'county' => $County,*/
                    'city' => $City,
                    'street' => $Street,
                    'state' => $State,
                    'zipcode' => $ZipCode,
                    'profile_picture' => $NewProfilePicture,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        } else {
            DB::beginTransaction();
            DB::table('profiles')
                ->where('user_id', '=', Auth::id())
                ->update([
                    'phone' => $Phone,
                    'phone2' => $Phone2,
                    /*'county' => $County,*/
                    'city' => $City,
                    'street' => $Street,
                    'state' => $State,
                    'zipcode' => $ZipCode,
                    'profile_picture' => $NewProfilePicture,
                    'updated_at' => Carbon::now()
                ]);
            DB::commit();
        }

        if ($UserRole == 1) {
            return redirect(url('/admin/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 2) {
            return redirect(url('/global_manager/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 3) {
            return redirect(url('/acquisition_manager/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 4) {
            return redirect(url('/disposition_manager/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 5) {
            return redirect(url('/acquisition_representative/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 6) {
            return redirect(url('/disposition_representative/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 7) {
            return redirect(url('/cold_caller/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 8) {
            return redirect(url('/affiliate/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        } elseif ($UserRole == 9) {
            return redirect(url('/realtor/edit-profile'))->with('message', 'Personal Details has been updated successfully');
        }

    }

    function UpdateAccountSecurity(Request $request)
    {
        $UserId = $request->post('user_id');
        $Password = $request->post('newPassword');
        DB::beginTransaction();
        DB::table('users')->where('id', '=', $UserId)->update([
            'password' => bcrypt($Password),
            'updated_at' => Carbon::now()
        ]);
        DB::commit();

        return redirect(url('logout'))->with('message', 'User Password has been updated successfully. Please login again with new password.');
    }

    public function ban(Request $request)
    {
        $UserRole = Session::get('user_role');
        $id = $request['UserId'];
        $Reason = $request['ban_reason'];
        DB::beginTransaction();
        $affected = DB::table('users')
            ->where('id', $id)
            ->update([
                'status' => 0,
                'updated_at' => Carbon::now()
            ]);

        $affected1 = UserActivity::create([
            'user_id' => $id,
            'sender_id' => Auth::id(),
            'message' => $Reason
        ]);

        if ($affected && $affected1) {
            DB::commit();
            /*if ($UserRole == 1) {
                // return redirect(url('/admin/users'))->with('message', 'User has been banned successfully');
                return redirect()->back()->with('message', 'User has been banned successfully');
            } elseif ($UserRole == 2) {
                // return redirect(url('/global_manager/users'))->with('message', 'User has been banned successfully');
                return redirect()->back()->with('message', 'User has been banned successfully');
            }*/
            return redirect()->back()->with('message', 'User has been banned successfully');
        } else {
            DB::rollback();
            /*if ($UserRole == 1) {
                // return redirect(url('/admin/users'))->with('error', 'Error! An unhandled exception occurred.');
                return redirect()->back()->with('error', 'Error! An unhandled exception occurred.');
            } elseif ($UserRole == 2) {
                // return redirect(url('/global_manager/users'))->with('message', 'User has been banned successfully');
                return redirect()->back()->with('message', 'User has been banned successfully');
            }*/
            return redirect()->back()->with('message', 'User has been banned successfully');
        }
    }

    public function active(Request $request)
    {
        $id = $request->post('UserId');
        DB::beginTransaction();
        $affected = DB::table('users')
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

    public function UsersProgress()
    {
        $page = "usersProgress";
        return view('admin.users.progress', compact('page'));
    }

    public function UsersProgressAll(Request $request)
    {
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
            $fetch_data = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.role_id', '!=', 1)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.country AS country', 'profiles.city AS city', 'profiles.facebook AS facebook', 'profiles.bank_account_number AS bank_account_number', 'profiles.bank_name AS bank_name', 'users.status')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            // return json_encode($fetch_data);
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('users')
                ->where('users.deleted_at', '=', null)
                ->where('users.role_id', '!=', 1)
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.country AS country', 'profiles.city AS city', 'profiles.facebook AS facebook', 'profiles.bank_account_number AS bank_account_number', 'profiles.bank_name AS bank_name', 'users.status')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where(function ($query) {
                    $query->where([
                        ['users.deleted_at', '=', null],
                        ['users.role_id', '!=', 1]
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
                    $query->orWhere('profiles.country', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.facebook', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.bank_account_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.bank_name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.country AS country', 'profiles.city AS city', 'profiles.facebook AS facebook', 'profiles.bank_account_number AS bank_account_number', 'profiles.bank_name AS bank_name', 'users.status')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('users')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where(function ($query) {
                    $query->where([
                        ['users.deleted_at', '=', null],
                        ['users.role_id', '!=', 1]
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
                    $query->orWhere('profiles.country', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.city', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.facebook', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.bank_account_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.bank_name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'roles.title as role', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname', 'profiles.dob AS dob', 'profiles.phone AS phone', 'profiles.country AS country', 'profiles.city AS city', 'profiles.facebook AS facebook', 'profiles.bank_account_number AS bank_account_number', 'profiles.bank_name AS bank_name', 'users.status')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        $status = "";
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
            $Progress = $this->CalculateUserProgress($item->id);
            if ($Progress != "") {
                $sub_array = array();
                $sub_array['id'] = $SrNo;
                $sub_array['role'] = $item->role;
                $sub_array['userId'] = $item->userId;
                $sub_array['name'] = $item->firstname . " " . $item->lastname;
                $sub_array['progress'] = $this->CalculateUserProgress($item->id);
                $SrNo++;
                $data[] = $sub_array;
            }
        }

        $json_data = array(
            "draw" => intval($request->post('draw')),
            "iTotalRecords" => $recordsTotal,
            "iTotalDisplayRecords" => $recordsFiltered,
            "aaData" => $data
        );

        echo json_encode($json_data);
    }

    public function UserActivitiesAll(Request $request)
    {
        $Role = Session::get('user_role');
        $UserId = $request->post('UserId');
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
            $fetch_data = DB::table('user_activities')
                ->where('user_activities.user_id', '=', $UserId)
                ->join('profiles', 'profiles.user_id', '=', 'user_activities.sender_id')
                ->select('user_activities.*', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname')
                ->orderBy('user_activities.id', 'DESC')
                ->offset($start)
                ->limit($limit)
                ->get();

            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('user_activities')
                ->where('user_activities.user_id', '=', $UserId)
                ->join('profiles', 'profiles.user_id', '=', 'user_activities.sender_id')
                ->select('user_activities.*', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname')
                ->orderBy('user_activities.id', 'DESC')
                ->count();
        } else {
            $fetch_data = DB::table('user_activities')
                ->where('user_activities.user_id', '=', $UserId)
                ->join('profiles', 'profiles.user_id', '=', 'user_activities.sender_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('user_activities.message', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('user_activities.*', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname')
                ->orderBy('user_activities.id', 'DESC')
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('user_activities')
                ->where('user_activities.user_id', '=', $UserId)
                ->join('profiles', 'profiles.user_id', '=', 'user_activities.sender_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('user_activities.message', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('user_activities.*', 'profiles.firstname AS firstname', 'profiles.lastname AS lastname')
                ->orderBy('user_activities.id', 'DESC')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        $status = "";
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['user'] = '<span>' . wordwrap($item->firstname . " " . $item->lastname, 30, '<br>') . '<br><br>' . Carbon::parse($item->created_at)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->created_at)->format('g:i a') . '</span>';
            $sub_array['message'] = '<span>' . $item->message . '</span>';
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

    public function UserUpgradeAccount(Request $request)
    {
        $UserRole = Session::get('user_role');
        // $id = $request['id'];
        $Users = $request->post('checkAllBox');
        DB::beginTransaction();
        $affected = null;
        $affected1 = null;
        foreach ($Users as $key => $id) {
          $affected = DB::table('training_assignment_folders')
              ->where('user_id', $id)
              ->update([
                  'completion_rate' => 100,
                  'updated_at' => Carbon::now()
              ]);
          $affected1 = DB::table('training_assignments')
              ->where('user_id', $id)
              ->update([
                  'status' => 1,
                  'updated_at' => Carbon::now()
              ]);
        }

        if ($affected && $affected1) {
            DB::commit();
            if ($UserRole == 1) {
                return redirect(url('/admin/users'))->with('message', 'User account has been upgraded successfully');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/users'))->with('message', 'User account has been upgraded successfully');
            }
        } else {
            DB::rollback();
            if ($UserRole == 1) {
                return redirect(url('/admin/users'))->with('error', 'Error! An unhandled exception occurred.');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/users'))->with('message', 'Error! An unhandled exception occurred.');
            }
        }
    }

    public function LoadCounties(Request $request)
    {
        $State = $request['State'];
        // Counties list
        $counties = DB::table('locations')
            ->where('state_name', '=', $State)
            ->orderBy("county_name", "ASC")
            ->get()
            ->unique("county_name");
        $options = '';
        if($request->has('ServingLocation')){
            $options = '<option value="">Select County</option>';
        }
        else{
            $options = '<option value="">Select County</option>';
        }
        foreach ($counties as $county) {
            $options .= '<option value="' . $county->county_name . '">' . $county->county_name . '</option>';
        }

        echo json_encode($options);
    }

    public function LoadCities(Request $request)
    {
        $State = $request['State'];
        // Cities list
        $cities = DB::table('locations')
            ->where('state_name', '=', $State)
            ->orderBy("city", "ASC")
            ->get()
            ->unique("city");
        $options = '';
        if($request->has('ServingLocation')){
            $options = '<option value="" selected>Select City</option>';
        }
        else{
            $options = '<option value="" selected>Select City</option>';
        }
        foreach ($cities as $city) {
            $options .= '<option value="' . $city->city . '">' . $city->city . '</option>';
        }

        echo json_encode($options);
    }

    function UserStateCheck(Request $request)
    {
        $State = $request->post('State');
        $Role = $request->post('Role');
        $Users = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.deleted_at', '=', null)
            ->where('users.role_id', '=', $Role)
            ->where('profiles.state', '=', $State)
            ->get();
        if (sizeof($Users) > 0) {
            echo 'failed';
        } else {
            echo 'success';
        }
        exit();
    }

    /* User Department Filter Section - Start */
    public function UserStateFilterPage($UserId)
    {
        $page = "users";
        $Role = Session::get('user_role');

        $UserDepartmentFilterDetails = DB::table('user_department_filters')
                       ->where('user_id', $UserId)
                       ->where('deleted_at', null)
                       ->select('user_department_filters.*')
                       ->get();

        $states = DB::table('states')
                       ->select('states.*')
                       ->get();

        return view('admin.users.user-state-filter', compact('page', 'UserId', 'UserDepartmentFilterDetails', 'states', 'Role'));
    }

    public function UserDepartmentFilterStore(Request $request)
    {
        $UserId     = $request['user_id'];
        $LeadStatus = $request['status'];
        $State      = $request['state'];
        $StartDate  = $request['startDateFilter'];
        $EndDate    = $request['endDateFilter'];

        if ($LeadStatus != "") {
          $LeadStatus = implode(",",$LeadStatus);
        }
        if ($State != "") {
          $State = implode(",",$State);
        }

        // Get user role
        $UserDetails = DB::table('users')
                       ->where('id', $UserId)
                       ->where('deleted_at', null)
                       ->get();

        $UserDepartmentFilterDetails = DB::table('user_department_filters')
                       ->where('user_id', $UserId)
                       ->where('deleted_at', null)
                       ->count();

        if ($UserDepartmentFilterDetails > 0) {
          DB::beginTransaction();
          $affected = DB::table('user_department_filters')
             ->where('user_id', $UserId)
             ->update([
                'lead_status' => $LeadStatus,
                'state'       => $State,
                'start_date'  => $StartDate,
                'end_date'    => $EndDate,
                'updated_at'  => Carbon::now()
             ]);

             DB::commit();
             if ($UserDetails[0]->role_id == 9) {
                return redirect(url('/admin/buissness_accounts'))->with('message', 'User filter has been updated successfully');
             } else {
                return redirect(url('/admin/users'))->with('message', 'User filter has been updated successfully');
             }
        }
        else {
          DB::beginTransaction();
          $affected = UserDepartmentFilter::create([
              'user_id'     => $UserId,
              'lead_status' => $LeadStatus,
              'state'       => $State,
              'start_date'  => $StartDate,
              'end_date'    => $EndDate,
              'created_at'  => Carbon::now(),
          ]);

          if ($affected) {
            DB::commit();
            if ($UserDetails[0]->role_id == 9) {
              return redirect(url('/admin/buissness_accounts'))->with('message', 'User filter has been updated successfully');
            } else {
              return redirect(url('/admin/users'))->with('message', 'User filter has been updated successfully');
            }
          }
          else {
            DB::rollBack();
            if ($UserDetails[0]->role_id == 9) {
              return redirect(url('/admin/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
            } else {
              return redirect(url('/admin/users'))->with('error', 'Error! An unhandled exception occurred');
            }
          }
        }
    }
    /* User Department Filter Section - End */
}
