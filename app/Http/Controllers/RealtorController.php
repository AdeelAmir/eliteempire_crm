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

class RealtorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

    }

    /* Admin Section - Start */
    public function AdminAllRealtors()
    {
        $Role = Session::get('user_role');
        $page = "buissness_accounts";
        return view('admin.realtors.realtors', compact('page', 'Role'));
    }

    public function AdminAddNewRealtor()
    {
        $Role = Session::get('user_role');
        $page = "buissness_accounts";

        // State list
        $states = DB::table('states')->get();
        $_states = json_encode($states);

        return view('admin.realtors.add-new-realtor', compact('page', 'states', '_states', 'Role'));
    }

    public function AdminRealtorStore(Request $request)
    {
        $UserRole = Session::get('user_role');
        $FirstName = ucwords(strtolower($request['firstname']));
        $MiddleName = ucwords(strtolower($request['middlename']));
        $LastName = ucwords(strtolower($request['lastname']));
        $Buisnessname = $request['buisness_name'];
        $BuisnessAddress = $request['buisness_address'];
        $Phone1 = $request['phone'];
        $Phone2 = $request['phone2'];
        $Email = $request['email'];
        $SecondaryEmail = $request['secondary_email'];
        $State = $request['state'];
        $County = $request['county'];
        $City = $request['city'];
        $PropertyClassification = $request['propertyClassification'];
        $PropertyType = null;
        $MultiFamilyType = null;
        $ConstructionType = $request['constructionType'];
        // $UserId = substr($FirstName, 0, 1) . substr($LastName, 0, 1) . rand(10000, 99999);
        $UserId = "";
        if ($FirstName != "" && $LastName != "") {
          $UserId = substr($FirstName, 0, 1) . substr($LastName, 0, 1) . rand(10000, 99999);
        } else {
          $UserId = strtoupper(substr($Buisnessname, 0, 2)) . rand(10000, 99999);
        }
        $Role = 9;
        $Password = rand(10000000, 100000000);
        $PropertyClassification = json_decode($request['propertyClassification']);
        $PropertyType = json_decode($request['propertyType']);
        $MultiFamilyType = json_decode($request['multiFamilyType']);
        $ConstructionType = json_decode($request['constructionType']);
        $Serving_State = json_decode($request['serving_state']);
        $Serving_City = json_decode($request['serving_city']);
        $Serving_County = json_decode($request['serving_county']);
        $Serving_ZipCode = json_decode($request['serving_zipcode']);

        // Check this email already exists or not
        $check_user_email = DB::table('users')
            ->where('email', '=', $Email)
            ->count();

        if ($check_user_email > 0) {
            if ($UserRole == 1) {
                return redirect(url('/admin/realtor/add'))->with('error', 'Error! Email already exists');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/realtor/add'))->with('error', 'Error! Email already exists');
            }
        } else {
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
                'phone' => $Phone1,
                'phone2' => $Phone2,
                'buisnesss_name' => $Buisnessname,
                'buisnesss_address' => $BuisnessAddress,
                'secondary_email' => $SecondaryEmail,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if (isset($request['serving_state'])) {
                for ($i = 0; $i < count($Serving_State); $i++) {
                    $_PropertyClassification = implode(',', $PropertyClassification[$i]);
                    $_PropertyType = implode(',', $PropertyType[$i]);
                    $_MultiFamilyType = implode(',', $MultiFamilyType[$i]);
                    $_ConstructionType = implode(',', $ConstructionType[$i]);
                    $_County = implode(',', $Serving_County[$i]);
                    $_City = implode(',', $Serving_City[$i]);
                    $affected1 = ServingLocation::create([
                        'user_id' => $affected->id,
                        'property_classification' => $_PropertyClassification,
                        'property_type' => $_PropertyType,
                        'multi_family' => $_MultiFamilyType,
                        'construction_type' => $_ConstructionType,
                        'state' => $Serving_State[$i],
                        'city' => $_City,
                        'county' => $_County,
                        'zipcode' => $Serving_ZipCode[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            // Get user first name and last name of person who create this user
            $GetUser = DB::table('profiles')
                ->where('user_id', Auth::id())
                ->where('deleted_at', null)
                ->get();

            $GetUserDetails = "";
            if ($GetUser[0]->middlename != "") {
                $GetUserDetails = $GetUser[0]->firstname . " " . $GetUser[0]->lastname;
            } else {
                $GetUserDetails = $GetUser[0]->firstname . " " . $GetUser[0]->middlename . " " . $GetUser[0]->lastname;
            }

            // Add an entry in user activity how create this user
            $affected5 = UserActivity::create([
                'user_id' => $affected->id,
                'message' => "This user was added by " . $GetUserDetails,
                'created_at' => Carbon::now(),
            ]);

            if ($affected && $affected1) {
                DB::commit();
                if ($UserRole == 1) {
                    return redirect(url('/admin/buissness_accounts'))->with('message', 'Realtor Account has been added successfully');
                } elseif ($UserRole == 2) {
                    return redirect(url('/global_manager/buissness_accounts'))->with('message', 'Realtor Account has been added successfully');
                } elseif ($UserRole == 6) {
                    return redirect(url('/disposition_representative/buissness_accounts'))->with('message', 'Realtor Account has been added successfully');
                }
            } else {
                DB::rollback();
                if ($UserRole == 1) {
                    return redirect(url('/admin/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
                } elseif ($UserRole == 2) {
                    return redirect(url('/global_manager/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
                } elseif ($UserRole == 6) {
                    return redirect(url('/disposition_representative/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
                }
            }
        }
    }

    public function AdminDeleteRealtor(Request $request)
    {
        $UserRole = Session::get('user_role');
        $user_id = $request['id'];
        DB::beginTransaction();
        $affected = DB::table('users')
            ->where('id', $user_id)
            ->update([
                'updated_at' => Carbon::now(),
                'deleted_at' => Carbon::now(),
            ]);
        if ($affected) {
            DB::commit();
            if ($UserRole == 1) {
                return redirect(url('/admin/buissness_accounts'))->with('message', 'Realtor account has been deleted successfully');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/buissness_accounts'))->with('message', 'Realtor account has been deleted successfully');
            } elseif ($UserRole == 6) {
                return redirect(url('/disposition_representative/buissness_accounts'))->with('message', 'Realtor account has been deleted successfully');
            }
        } else {
            DB::rollback();
            if ($UserRole == 1) {
                return redirect(url('/admin/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($UserRole == 2) {
                return redirect(url('/global_manager/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($UserRole == 6) {
                return redirect(url('/disposition_representative/buissness_accounts'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function AdminEditRealtor(Request $request)
    {
        $page = "buissness_accounts";
        $Role = Session::get('user_role');
        $user_id = $request['id'];

        $u_details = DB::table('users')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            ->where('users.deleted_at', '=', null)
            ->select('users.id AS id', 'users.userId AS userId', 'users.email AS email', 'users.role_id AS role', 'profiles.firstname AS firstname', 'profiles.middlename AS middlename', 'profiles.lastname AS lastname', 'profiles.phone AS phone', 'profiles.phone2 AS phone2', 'profiles.state AS state', 'profiles.county AS county', 'profiles.city AS city', 'profiles.buisnesss_name AS buisnesss_name', 'profiles.secondary_email AS secondary_email')
            ->get();

        $serving_locations = DB::table('serving_locations')
            ->where('serving_locations.user_id', '=', $user_id)
            ->select('serving_locations.*')
            ->get();

        $states = DB::table('states')->get();
        $counties = DB::table('counties')->get();
        $cities = DB::table('cities')->get();

        $_states = json_encode($states);

        return view('admin.realtors.edit-realtor', compact('page', 'cities', 'counties', 'states', 'serving_locations', '_states', 'user_id', 'u_details', 'Role'));
    }

    public function AdminUpdateRealtor(Request $request)
    {
        $UserRole = Session::get('user_role');
        $user_id = $request['id'];
        $FirstName = ucwords(strtolower($request['firstname']));
        $MiddleName = ucwords(strtolower($request['middlename']));
        $LastName = ucwords(strtolower($request['lastname']));
        $Buisnessname = $request['buisness_name'];
        $BuisnessAddress = $request['buisness_address'];
        $Phone1 = $request['phone'];
        $Phone2 = $request['phone2'];
        $OldEmail = $request['old_email'];
        $Email = $request['email'];
        $SecondaryEmail = $request['secondary_email'];
        $PropertyClassification = json_decode($request['propertyClassification']);
        $PropertyType = json_decode($request['propertyType']);
        $MultiFamilyType = json_decode($request['multiFamilyType']);
        $ConstructionType = json_decode($request['constructionType']);
        $Serving_State = json_decode($request['serving_state']);
        $Serving_City = json_decode($request['serving_city']);
        $Serving_County = json_decode($request['serving_county']);
        $Serving_ZipCode = json_decode($request['serving_zipcode']);

        // Check this email already exists or not
        if ($OldEmail != $Email) {
            $check_user_email = DB::table('users')
                ->where('email', '=', $Email)
                ->count();

            if ($check_user_email > 0) {
                if ($UserRole == 1) {
                    return redirect(url('/admin/realtors'))->with('error', 'Error! Email already exists');
                } elseif ($UserRole == 2) {
                    return redirect(url('/global_manager/realtors'))->with('error', 'Error! Email already exists');
                }
            }
        }

        DB::beginTransaction();
        $affected = DB::table('users')
            ->where('id', $user_id)
            ->update([
                'email' => $Email,
                'updated_at' => Carbon::now(),
            ]);

        $affected1 = DB::table('profiles')
            ->where('user_id', $user_id)
            ->update([
                'firstname' => $FirstName,
                'middlename' => $MiddleName,
                'lastname' => $LastName,
                'phone' => $Phone1,
                'phone2' => $Phone2,
                'buisnesss_name' => $Buisnessname,
                'buisnesss_address' => $BuisnessAddress,
                'secondary_email' => $SecondaryEmail,
                'updated_at' => Carbon::now(),
            ]);

        // Delete old serving locations and add new locations
        DB::table('serving_locations')->where('user_id', $user_id)->delete();
        if (isset($request['serving_state'])) {
            for ($i = 0; $i < count($Serving_State); $i++) {
                $_PropertyClassification = implode(',', $PropertyClassification[$i]);
                $_PropertyType = implode(',', $PropertyType[$i]);
                $_MultiFamilyType = implode(',', $MultiFamilyType[$i]);
                $_ConstructionType = implode(',', $ConstructionType[$i]);
                $_County = implode(',', $Serving_County[$i]);
                $_City = implode(',', $Serving_City[$i]);
                $affected2 = ServingLocation::create([
                    'user_id' => $user_id,
                    'property_classification' => $_PropertyClassification,
                    'property_type' => $_PropertyType,
                    'multi_family' => $_MultiFamilyType,
                    'construction_type' => $_ConstructionType,
                    'state' => $Serving_State[$i],
                    'city' => $_City,
                    'county' => $_County,
                    'zipcode' => $Serving_ZipCode[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        DB::commit();
        if ($UserRole == 1) {
            return redirect(url('/admin/buissness_accounts'))->with('message', 'Realtor record has been updated successfully');
        } elseif ($UserRole == 2) {
            return redirect(url('/global_manager/buissness_accounts'))->with('message', 'Realtor record has been updated successfully');
        } elseif ($UserRole == 6) {
            return redirect(url('/disposition_representative/buissness_accounts'))->with('message', 'Realtor record has been updated successfully');
        }
    }
}
