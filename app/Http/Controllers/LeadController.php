<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\LeadAssignment;
use App\location_coordinates;
use App\VirtualLeadAssignment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Promise\exception_for;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Lead;
use App\Earnings_m;
use App\Earning_d;
use App\HistoryNote;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Excel;
use App\Imports\LeadsImport;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function RepresentativeAllLeads()
    {
        $page = "leads";
        $Role = Session::get('user_role');
        // All States
        $States = DB::table('states')
            ->get();
        $Investors = array();
        $Realtors = array();
        $TitleCompanies = array();
        if ($Role == 6) {
          $Investors = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 10)
              ->where('users.deleted_at', '=', null)
              ->where('users.parent_id', '=', Auth::id())
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();

          $Realtors = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 9)
              ->where('users.deleted_at', '=', null)
              ->where('users.parent_id', '=', Auth::id())
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();

          $TitleCompanies = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 11)
              ->where('users.deleted_at', '=', null)
              ->where('users.parent_id', '=', Auth::id())
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();
        } else {
          $Investors = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 10)
              ->where('users.deleted_at', '=', null)
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();

          $Realtors = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 9)
              ->where('users.deleted_at', '=', null)
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();

          $TitleCompanies = DB::table('users')
              ->join('profiles', 'users.id', '=', 'profiles.user_id')
              ->where('users.role_id', '=', 11)
              ->where('users.deleted_at', '=', null)
              ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
              ->get();
        }

        return view('admin.lead.leads', compact('page', 'Role', 'States', 'Investors', 'Realtors', 'TitleCompanies'));
    }

    public function RepresentativeAddNewLead()
    {
        $page = "leads";
        $Role = Session::get('user_role');
        $user_id = Auth::id();

        // All States
        $states = DB::table('states')
            ->get();

        return view('admin.lead.add-new-lead', compact('page', 'states', 'Role'));
    }

    public function RepresentativeLeadStore(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadType = 1;
        $LeadStatus = 3; // Lead in
        $user_id = Auth::id();
        $LeadNumber = rand(1000000, 9999999);
        $Phone1 = $request->has('phone') ? $request->post('phone') : null;
        $Phone2 = $request->has('phone2') ? $request->post('phone2') : null;
        $Phone3 = $request->has('phone3') ? $request->post('phone3') : null;
        $Phone4 = $request->has('phone4') ? $request->post('phone4') : null;
        $Phone5 = $request->has('phone5') ? $request->post('phone5') : null;
        $IsDuplicated = 0;
        $City = null;
        if ($request->post('state') != "") {
          // get city from zip code if city is not selected
          if ($request->post('city') != "") {
            $City = $request->post('city');
          } else {
            $City = SiteHelper::GetCityFromZipCode($request->post('zipcode'));
          }
        }

        /*Check for exactly same lead*/
        $Check = DB::table('leads')
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($request, $Phone1) {
                if (ucwords(strtolower($request->post('firstName'))) != null) {
                    $query->where('leads.firstname', '=', ucwords(strtolower($request->post('firstName'))));
                }
                if (ucwords(strtolower($request->post('lastName'))) != null) {
                    $query->where('leads.lastname', '=', ucwords(strtolower($request->post('lastName'))));
                }
                if ($Phone1 != null) {
                    $query->where('leads.phone', '=', $Phone1);
                }
                if ($request->post('street') != null) {
                    $query->where('leads.street', '=', $request->post('street'));
                }
                if ($request->post('zipcode') != null) {
                    $query->where('leads.zipcode', '=', $request->post('zipcode'));
                }
            })
            ->get();
        if (sizeof($Check) > 0) {
            $LeadNumber = $Check[0]->lead_number;
            $Message = '<p class="text-center mb-0" style="font-style: italic;">Sorry!</p>';
            $Message .= '<p class="text-center mb-0" style="font-style: italic;">The Lead you are trying to send is already in our database.</p>';
            $Message .= '<p class="text-center mb-0" style="font-style: italic;"><b>#' . $LeadNumber . '</b></p>';
            if ($Role == 1) {
                return redirect(url('/admin/lead/add'))->with('error', $Message);
            } elseif ($Role == 2) {
                return redirect(url('/global_manager/lead/add'))->with('error', $Message);
            } elseif ($Role == 3) {
                return redirect(url('/acquisition_manager/lead/add'))->with('error', $Message);
            } elseif ($Role == 4) {
                return redirect(url('/disposition_manager/lead/add'))->with('error', $Message);
            } elseif ($Role == 5) {
                return redirect(url('/acquisition_representative/lead/add'))->with('error', $Message);
            } elseif ($Role == 6) {
                return redirect(url('/disposition_representative/lead/add'))->with('error', $Message);
            } elseif ($Role == 7) {
                return redirect(url('/cold_caller/lead/add'))->with('error', $Message);
            } elseif ($Role == 8) {
                return redirect(url('/affiliate/lead/add'))->with('error', $Message);
            }  elseif ($Role == 9) {
                return redirect(url('/realtor/lead/add'))->with('error', $Message);
            }
        }

        // Checking for duplicated
        $Check = DB::table('leads')
            ->whereIn('lead_type', array(1))
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($Phone1, $Phone2, $Phone3, $Phone4, $Phone5) {
                if ($Phone1 != null) {
                    $query->orWhere('leads.phone', '=', $Phone1);
                    $query->orWhere('leads.phone2', '=', $Phone1);
                    $query->orWhere('leads.phone3', '=', $Phone1);
                    $query->orWhere('leads.phone4', '=', $Phone1);
                    $query->orWhere('leads.phone5', '=', $Phone1);
                }
                if ($Phone2 != null) {
                    $query->orWhere('leads.phone', '=', $Phone2);
                    $query->orWhere('leads.phone2', '=', $Phone2);
                    $query->orWhere('leads.phone3', '=', $Phone2);
                    $query->orWhere('leads.phone4', '=', $Phone2);
                    $query->orWhere('leads.phone5', '=', $Phone2);
                }
                if ($Phone3 != null) {
                    $query->orWhere('leads.phone', '=', $Phone3);
                    $query->orWhere('leads.phone2', '=', $Phone3);
                    $query->orWhere('leads.phone3', '=', $Phone3);
                    $query->orWhere('leads.phone4', '=', $Phone3);
                    $query->orWhere('leads.phone5', '=', $Phone3);
                }
                if ($Phone4 != null) {
                    $query->orWhere('leads.phone', '=', $Phone4);
                    $query->orWhere('leads.phone2', '=', $Phone4);
                    $query->orWhere('leads.phone3', '=', $Phone4);
                    $query->orWhere('leads.phone4', '=', $Phone4);
                    $query->orWhere('leads.phone5', '=', $Phone4);
                }
                if ($Phone5 != null) {
                    $query->orWhere('leads.phone', '=', $Phone5);
                    $query->orWhere('leads.phone2', '=', $Phone5);
                    $query->orWhere('leads.phone3', '=', $Phone5);
                    $query->orWhere('leads.phone4', '=', $Phone5);
                    $query->orWhere('leads.phone5', '=', $Phone5);
                }
            })
            ->get();

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $LeadNumber = $Check[0]->lead_number;
        }

        $HomeFeatures = null;
        if($request->has('homeFeature') && $request->post('homeFeature') != ''){
            $HomeFeatures = implode(',', $request->post('homeFeature'));
        }

        DB::beginTransaction();
        $affected = Lead::create([
            'user_id' => $user_id,
            'lead_number' => $LeadNumber,
            'owner_occupy' => $request->post('ownersOccupy'),
            'occupancy_status' => $request->post('occupancyStatus'),
            'firstname' => ucwords(strtolower($request->post('firstName'))),
            'middlename' => ucwords(strtolower($request->post('middleName'))),
            'lastname' => ucwords(strtolower($request->post('lastName'))),
            'phone' => $Phone1,
            'phone2' => $Phone2,
            'phone3' => $Phone3,
            'phone4' => $Phone4,
            'phone5' => $Phone5,
            'martial_status' => $request->post('martial_status'),
            'spouce' => $request->has('spouce') ? $request->post('spouce') : null,
            'language' => $request->post('language'),
            'state' => $request->post('state'),
            'city' => $City,
            'street' => $request->post('street'),
            'zipcode' => $request->post('zipcode'),
            'property_classification' => $request->post('propertyClassification'),
            'property_type' => $request->has('propertyType') ? $request->post('propertyType') : null,
            'multi_family' => $request->has('multiFamilyType') ? $request->post('multiFamilyType') : null,
            'construction_type' => $request->post('constructionType'),
            'year_built' => $request->post('yearBuilt'),
            'building_size' => $request->post('buildingSize'),
            'bedroom' => $request->post('bedroom'),
            'bathroom' => $request->post('bathroom'),
            'lot_size' => $request->post('lotSize'),
            'home_feature' => $HomeFeatures,
            'num_of_stories' => $request->post('storiesNo'),
            'association_fee' => $request->post('associationFee'),
            'reason_to_sale' => $request->post('reasonsToSales'),
            'picture' => $request->post('pictureLink'),
            'lead_condition' => $request->post('conditions'),
            'lead_source' => $request->post('leadSources'),
            'data_source' => $request->has('data_source')? implode(",", $request->post('data_source')) : null,
            'asking_price' => $request->post('askingPrice'),
            'arv' => $request->post('arv'),
            'assignment_fee' => $request->post('assignment_fee'),
            'rehab_cost' => $request->post('rehab_cost'),
            'arv_rehab_cost' => $request->post('arv_rehab_cost'),
            'arv_sales_closingcost' => $request->post('arv_sales_closing_cost'),
            'property_total_value' => $request->post('property_total_value'),
            'wholesales_closing_cost' => $request->post('wholesales_closing_cost'),
            'all_in_cost' => $request->post('all_in_cost'),
            'investor_profit' => $request->post('investor_profit'),
            'sales_price' => $request->post('sales_price'),
            'maximum_allow_offer' => $request->post('m_a_o'),
            'offer_range_low' => $request->post('offer_range_low'),
            'offer_range_high' => $request->post('offer_range_high'),
            'email' => $request->post('email'),
            'lead_type' => $LeadType,
            'lead_status' => $LeadStatus,
            'is_duplicated' => $IsDuplicated,
            'lead_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::commit();

        /* Lead Creation Email */
//        // User Email
//        $User = DB::table('profiles')->where('user_id', '=', $user_id)->get();
//        $Email = Auth::user()->email;
//        $data = array(
//            'Name' => $User[0]->firstname,
//            'LeadNumber' => $LeadNumber
//        );
//        Mail::send('email.lead-creation-email', $data, function ($message) use ($Email) {
//            $message->to($Email, 'Elite Empire')->subject('New Lead Created');
//            $message->from($_ENV['MAIL_FROM_ADDRESS'], 'Empire Empire');
//        });
//
//        // Admin Email
//        $User = DB::table('profiles')->where('user_id', '=', 1)->get();
//        $Email = 'leads@dynamicempire.net';
//        $data = array(
//            'Name' => $User[0]->firstname,
//            'LeadNumber' => $LeadNumber,
//            'FirstName' => $FirstName,
//            'LastName' => $LastName,
//            'Phone' => $Phone,
//            'Phone2' => $Phone2,
//            'Marital' => ucwords($MartialStatus),
//            'Spouse' => $Spouce,
//            'Language' => ucwords($Language),
//            'State' => ucwords($State),
//            'City' => ucwords($City),
//            'Street' => $Street,
//            'Zipcode' => $ZipCode,
//            'Product' => $Product,
//            'Appointment' => Carbon::parse($AppointmentTime)->format('m/d/Y H:i:s'),
//            'Split' => $Split,
//            'Email' => $Split,
//            'Note' => $Note
//        );
//        Mail::send('email.lead-creation-admin-email', $data, function ($message) use ($Email) {
//            $message->to($Email, 'Dynamic Empire')->subject('New Lead Created');
//            $message->from($_ENV['MAIL_FROM_ADDRESS'], 'Elite Empire');
//        });
//        /* Lead Creation Email */
//
        if ($affected) {
            DB::commit();
            if (sizeof($Check) > 0) {
                if ($Role == 1) {
                    return redirect(url('/admin/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 2) {
                    return redirect(url('/global_manager/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 3) {
                    return redirect(url('/acquisition_manager/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 4) {
                    return redirect(url('/disposition_manager/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 5) {
                    return redirect(url('/acquisition_representative/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 6) {
                    return redirect(url('/disposition_representative/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 7) {
                    return redirect(url('/cold_caller/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                } elseif ($Role == 8) {
                    return redirect(url('/affiliate/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                }  elseif ($Role == 9) {
                    return redirect(url('/realtor/lead/add'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
                }
            } else {
                if ($Role == 1) {
                    return redirect(url('/admin/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 2) {
                    return redirect(url('/global_manager/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 3) {
                    return redirect(url('/acquisition_manager/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 4) {
                    return redirect(url('/disposition_manager/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 5) {
                    return redirect(url('/acquisition_representative/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 6) {
                    return redirect(url('/disposition_representative/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 7) {
                    return redirect(url('/cold_caller/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 8) {
                    return redirect(url('/affiliate/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                } elseif ($Role == 9) {
                    return redirect(url('/realtor/lead/add'))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
                }
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/global_manager/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/acquisition_manager/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 4) {
                return redirect(url('/disposition_manager/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 5) {
                return redirect(url('/acquisition_representative/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 6) {
                return redirect(url('/disposition_representative/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 7) {
                return redirect(url('/cold_caller/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 8) {
                return redirect(url('/affiliate/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 9) {
                return redirect(url('/realtor/lead/add'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function RepresentativeEditLead($Id)
    {
        $page = "leads";
        $Role = Session::get('user_role');

        $Lead = DB::table('leads')
            ->where('id', '=', $Id)
            ->get();
        $Profile = DB::table('profiles')
            ->where('user_id', '=', $Lead[0]->user_id)
            ->get();

        $AppointmentTime = Carbon::parse($Lead[0]->appointment_time)->format('m/d/Y - g:i a');

        // All States
        $states = DB::table('states')
            ->get();
        // Cities list
        $cities = DB::table('locations')
            ->where('state_name', '=', $Lead[0]->state)
            ->orderBy("city", "ASC")
            ->get()
            ->unique("city");
        // Counties list
        $counties = DB::table('locations')
            ->where('state_name', '=', $Lead[0]->state)
            ->orderBy("county_name", "ASC")
            ->get()
            ->unique("county_name");

        // Lead duplicate status
        $LeadDuplicateStatus = $Lead[0]->is_duplicated;

        return view('admin.lead.edit-lead', compact('page', 'Lead', 'Role', 'states', 'AppointmentTime', 'LeadDuplicateStatus', 'Profile', 'cities', 'counties'));
    }

    public function RepresentativeUpdateLead(Request $request)
    {
        // dd("k");
        $Role = Session::get('user_role');
        $LeadId = $request['id'];
        $LeadNumber = $request['leadNumber'];
        $LeadType = 1;
        $Phone1 = $request->has('phone') ? $request->post('phone') : null;
        $Phone2 = $request->has('phone2') ? $request->post('phone2') : null;
        $Phone3 = $request->has('phone3') ? $request->post('phone3') : null;
        $Phone4 = $request->has('phone4') ? $request->post('phone4') : null;
        $Phone5 = $request->has('phone5') ? $request->post('phone5') : null;
        $SecondaryEmail = $request['secondary_email'];
        $IsDuplicated = 0;
        $City = null;

        if ($request->post('state') != "") {
          // get city from zip code if city is not selected
          if ($request->post('city') != "") {
            $City = $request->post('city');
          } else {
            $City = SiteHelper::GetCityFromZipCode($request->post('zipcode'));
          }
        }

        // Checking for duplicated
        $Check = DB::table('leads')
            ->whereIn('lead_type', array(1))
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($Phone1, $Phone2, $Phone3, $Phone4, $Phone5) {
                if ($Phone1 != null) {
                    $query->orWhere('leads.phone', '=', $Phone1);
                    $query->orWhere('leads.phone2', '=', $Phone1);
                    $query->orWhere('leads.phone3', '=', $Phone1);
                    $query->orWhere('leads.phone4', '=', $Phone1);
                    $query->orWhere('leads.phone5', '=', $Phone1);
                }
                if ($Phone2 != null) {
                    $query->orWhere('leads.phone', '=', $Phone2);
                    $query->orWhere('leads.phone2', '=', $Phone2);
                    $query->orWhere('leads.phone3', '=', $Phone2);
                    $query->orWhere('leads.phone4', '=', $Phone2);
                    $query->orWhere('leads.phone5', '=', $Phone2);
                }
                if ($Phone3 != null) {
                    $query->orWhere('leads.phone', '=', $Phone3);
                    $query->orWhere('leads.phone2', '=', $Phone3);
                    $query->orWhere('leads.phone3', '=', $Phone3);
                    $query->orWhere('leads.phone4', '=', $Phone3);
                    $query->orWhere('leads.phone5', '=', $Phone3);
                }
                if ($Phone4 != null) {
                    $query->orWhere('leads.phone', '=', $Phone4);
                    $query->orWhere('leads.phone2', '=', $Phone4);
                    $query->orWhere('leads.phone3', '=', $Phone4);
                    $query->orWhere('leads.phone4', '=', $Phone4);
                    $query->orWhere('leads.phone5', '=', $Phone4);
                }
                if ($Phone5 != null) {
                    $query->orWhere('leads.phone', '=', $Phone5);
                    $query->orWhere('leads.phone2', '=', $Phone5);
                    $query->orWhere('leads.phone3', '=', $Phone5);
                    $query->orWhere('leads.phone4', '=', $Phone5);
                    $query->orWhere('leads.phone5', '=', $Phone5);
                }
            })
            ->get();

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $LeadNumber = $Check[0]->lead_number;
        }

        $HomeFeatures = null;
        if($request->has('homeFeature') && $request->post('homeFeature') != ''){
            $HomeFeatures = implode(',', $request->post('homeFeature'));
        }

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $leadNumber = $Check[0]->lead_number;
        }

        // Get Lead Previous Appointment Time
        $lead_details = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->get();

        // Updating Record
        DB::beginTransaction();

        $Affected = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->update([
                'lead_number' => $LeadNumber,
                'owner_occupy' => $request->post('ownersOccupy'),
                'occupancy_status' => $request->post('occupancyStatus'),
                'firstname' => ucwords(strtolower($request->post('firstName'))),
                'middlename' => ucwords(strtolower($request->post('middleName'))),
                'lastname' => ucwords(strtolower($request->post('lastName'))),
                'phone' => $Phone1,
                'phone2' => $Phone2,
                'phone3' => $Phone3,
                'phone4' => $Phone4,
                'phone5' => $Phone5,
                'martial_status' => $request->post('martial_status'),
                'spouce' => $request->has('spouce') ? $request->post('spouce') : null,
                'language' => $request->post('language'),
                'state' => $request->post('state'),
                'city' => $City,
                'street' => $request->post('street'),
                'zipcode' => $request->post('zipcode'),
                'property_classification' => $request->post('propertyClassification'),
                'property_type' => $request->has('propertyType') ? $request->post('propertyType') : null,
                'multi_family' => $request->has('multiFamilyType') ? $request->post('multiFamilyType') : null,
                'construction_type' => $request->post('constructionType'),
                'year_built' => $request->post('yearBuilt'),
                'building_size' => $request->post('buildingSize'),
                'bedroom' => $request->post('bedroom'),
                'bathroom' => $request->post('bathroom'),
                'lot_size' => $request->post('lotSize'),
                'home_feature' => $HomeFeatures,
                'num_of_stories' => $request->post('storiesNo'),
                'association_fee' => $request->post('associationFee'),
                'reason_to_sale' => $request->post('reasonsToSales'),
                'picture' => $request->post('pictureLink'),
                'lead_condition' => $request->post('conditions'),
                'lead_source' => $request->post('leadSources'),
                'data_source' => $request->has('data_source')? implode(",", $request->post('data_source')) : null,
                'asking_price' => $request->post('askingPrice'),
                'arv' => $request->post('arv'),
                'assignment_fee' => $request->post('assignment_fee'),
                'rehab_cost' => $request->post('rehab_cost'),
                'arv_rehab_cost' => $request->post('arv_rehab_cost'),
                'arv_sales_closingcost' => $request->post('arv_sales_closing_cost'),
                'property_total_value' => $request->post('property_total_value'),
                'wholesales_closing_cost' => $request->post('wholesales_closing_cost'),
                'all_in_cost' => $request->post('all_in_cost'),
                'investor_profit' => $request->post('investor_profit'),
                'sales_price' => $request->post('sales_price'),
                'maximum_allow_offer' => $request->post('m_a_o'),
                'offer_range_low' => $request->post('offer_range_low'),
                'offer_range_high' => $request->post('offer_range_high'),
                'email' => $request->post('email'),
                'lead_type' => $LeadType,
                'is_duplicated' => $IsDuplicated,
                'updated_at' => Carbon::now()
            ]);

        if ($Affected) {
            DB::commit();
            if (sizeof($Check) > 0) {
                if ($Role == 1) {
                    return redirect(url('/admin/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 2) {
                    return redirect(url('/global_manager/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 3) {
                    return redirect(url('/acquisition_manager/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 4) {
                    return redirect(url('/disposition_manager/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 5) {
                    return redirect(url('/acquisition_representative/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 6) {
                    return redirect(url('/disposition_representative/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 7) {
                    return redirect(url('/cold_caller/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 8) {
                    return redirect(url('/affiliate/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 9) {
                    return redirect(url('/realtor/dashboard'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                }

            } else {
                if ($Role == 1) {
                    return redirect(url('/admin/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 2) {
                    return redirect(url('/global_manager/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 3) {
                    return redirect(url('/acquisition_manager/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 4) {
                    return redirect(url('/disposition_manager/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 5) {
                    return redirect(url('/acquisition_representative/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 6) {
                    return redirect(url('/disposition_representative/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 7) {
                    return redirect(url('/cold_caller/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 8) {
                    return redirect(url('/affiliate/dashboard'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 9) {
                    return redirect(url('/realtor/dashboard'))->with('message', 'Lead has been updated successfully');
                }
            }
        } else {
            DB::rollback();
            if ($Role == 1) {
                return redirect(url('/admin/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 2) {
                return redirect(url('/global_manager/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/acquisition_manager/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 4) {
                return redirect(url('/disposition_manager/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 5) {
                return redirect(url('/acquisition_representative/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 6) {
                return redirect(url('/disposition_representative/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 8) {
                return redirect(url('/affiliate/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 9) {
                return redirect(url('/realtor/dashboard'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function Delete(Request $request)
    {
      $LeadId = $request['LeadId'];
      DB::beginTransaction();
      $Affected = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->update([
              'deleted_at' => Carbon::now(),
          ]);

      if ($Affected) {
        DB::commit();
        echo "Success";
      } else {
        DB::rollback();
        echo "Failed";
      }
    }

    public function RepresentativeUpdateAssignedLead(Request $request)
    {

        $Role = Session::get('user_role');
        $LeadId = $request['id'];
        $leadNumber = $request['leadNumber'];
        $LeadType = $request['leadType'];
        $FirstName = $request['firstName'];
        $LastName = $request['lastName'];
        $MartialStatus = $request['martial_status'];
        $Language = $request['language'];
        $Phone = $request['phone'];
        $Phone2 = $request['phone2'];
        $State = $request['state'];
        $City = $request['city'];
        $Street = $request['street'];
        $ZipCode = $request['zipcode'];
        $Product = $request['product'];
        $Email = $request['email'];
        $Split = $request['split'];
        $Electricbill = $request['electricbill_Old'];
        $AppointmentTime = $request['appointmenttime'];
        $Note = $request['note'];
        $IsDuplicated = 0;
        $Spouce = null;
        $ProductDescription = null;
        $WindowsDoorsCount = null;
        $OldRoof = null;
        $HistoryNote = "Dispo lead is converted into full lead";

        if ($MartialStatus == "married") {
            $Spouce = $request['spouce'];
        }
        if ($Product == 1) {
            $WindowsDoorsCount = $request['windows_doors_count'];
        } elseif ($Product == 2) {
            $OldRoof = $request['old_roof_duration'];
        } elseif ($Product == 6) {
            $ProductDescription = $request['product_desc'];
        }

        if ($request['phone2'] == "") {
            $Phone2 = null;
        }

        if ($request->hasFile('electricbill')) {
            if ($Electricbill != "") {
                unlink(base_path() . '/public/storage/leads/' . $Electricbill);
            }
            $FileStoragePath = '/public/leads/';
            $Extension = $request->file('electricbill')->extension();
            $file = $request->file('electricbill')->getClientOriginalName();
            $FileName = pathinfo($file, PATHINFO_FILENAME);
            $OnlyFileName = $FileName;
            $FileName = $FileName . '-' . date('Y-m-d') . rand(100, 1000) . '.' . $Extension;
            $result = $request->file('electricbill')->storeAs($FileStoragePath, $FileName);
            $Electricbill = $FileName;
        }

        // Checking for duplicated leads
        $Check = DB::table('leads')
            ->whereIn('lead_type', array(1, 2, 3))
            ->where('deleted_at', '=', null)
            ->where('id', '!=', $LeadId)
            ->where(function ($query) use ($Phone, $Phone2) {
                if ($Phone != "") {
                    $query->orWhere('leads.phone', '=', $Phone);
                    $query->orWhere('leads.phone2', '=', $Phone);
                }
                if ($Phone2 != "") {
                    $query->orWhere('leads.phone', '=', $Phone2);
                    $query->orWhere('leads.phone2', '=', $Phone2);
                }
            })
            ->get();

        if (sizeof($Check) > 0) {
            $IsDuplicated = 1;
            $leadNumber = $Check[0]->lead_number;
        }

        // Get Lead Previous Appointment Time
        $lead_details = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->get();

        // Updating Record
        DB::beginTransaction();

        // Check if it is dispo lead and all required information is filled up then convert it into full lead and change ownership
        if ($LeadType == 3) {
            if ($FirstName != "" && $LastName != "" && $Phone != "" && $Street != "" && $City != "" && $State != "" && $ZipCode != "" && $Product != "" && $AppointmentTime != "") {
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'user_id' => Auth::id(),
                        'lead_number' => $leadNumber,
                        'firstname' => $FirstName,
                        'lastname' => $LastName,
                        'martial_status' => $MartialStatus,
                        'spouce' => $Spouce,
                        'language' => $Language,
                        'phone' => $Phone,
                        'phone2' => $Phone2,
                        'state' => $State,
                        'city' => $City,
                        'street' => $Street,
                        'zipcode' => $ZipCode,
                        'product' => $Product,
                        'product_desc' => $ProductDescription,
                        'windows_doors_count' => $WindowsDoorsCount,
                        'old_roof' => $OldRoof,
                        'appointment_time' => $AppointmentTime,
                        'split' => $Split,
                        'email' => $Email,
                        'electricbill' => $Electricbill,
                        'company' => null,
                        'is_duplicated' => $IsDuplicated,
                        'note' => $Note,
                        'lead_type' => 1,
                        'updated_at' => Carbon::now()
                    ]);

                HistoryNote::create([
                    'user_id' => Auth::id(),
                    'lead_id' => $LeadId,
                    'history_note' => $HistoryNote,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            } else {
                if ($lead_details[0]->appointment_time != $AppointmentTime) {
                    $Affected = DB::table('leads')
                        ->where('id', '=', $LeadId)
                        ->update([
                            'lead_number' => $leadNumber,
                            'firstname' => $FirstName,
                            'lastname' => $LastName,
                            'martial_status' => $MartialStatus,
                            'spouce' => $Spouce,
                            'language' => $Language,
                            'phone' => $Phone,
                            'phone2' => $Phone2,
                            'state' => $State,
                            'city' => $City,
                            'street' => $Street,
                            'zipcode' => $ZipCode,
                            'product' => $Product,
                            'product_desc' => $ProductDescription,
                            'windows_doors_count' => $WindowsDoorsCount,
                            'old_roof' => $OldRoof,
                            'appointment_time' => $AppointmentTime,
                            'lead_status' => 3,
                            'split' => $Split,
                            'email' => $Email,
                            'electricbill' => $Electricbill,
                            'company' => null,
                            'is_duplicated' => $IsDuplicated,
                            'note' => $Note,
                            'updated_at' => Carbon::now()
                        ]);
                } else {
                    $Affected = DB::table('leads')
                        ->where('id', '=', $LeadId)
                        ->update([
                            'lead_number' => $leadNumber,
                            'firstname' => $FirstName,
                            'lastname' => $LastName,
                            'martial_status' => $MartialStatus,
                            'spouce' => $Spouce,
                            'language' => $Language,
                            'phone' => $Phone,
                            'phone2' => $Phone2,
                            'state' => $State,
                            'city' => $City,
                            'street' => $Street,
                            'zipcode' => $ZipCode,
                            'product' => $Product,
                            'product_desc' => $ProductDescription,
                            'windows_doors_count' => $WindowsDoorsCount,
                            'old_roof' => $OldRoof,
                            'appointment_time' => $AppointmentTime,
                            'split' => $Split,
                            'email' => $Email,
                            'electricbill' => $Electricbill,
                            'company' => null,
                            'is_duplicated' => $IsDuplicated,
                            'note' => $Note,
                            'updated_at' => Carbon::now()
                        ]);
                }
            }
        } else {
            if ($lead_details[0]->appointment_time != $AppointmentTime) {
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_number' => $leadNumber,
                        'firstname' => $FirstName,
                        'lastname' => $LastName,
                        'martial_status' => $MartialStatus,
                        'spouce' => $Spouce,
                        'language' => $Language,
                        'phone' => $Phone,
                        'phone2' => $Phone2,
                        'state' => $State,
                        'city' => $City,
                        'street' => $Street,
                        'zipcode' => $ZipCode,
                        'product' => $Product,
                        'product_desc' => $ProductDescription,
                        'windows_doors_count' => $WindowsDoorsCount,
                        'old_roof' => $OldRoof,
                        'appointment_time' => $AppointmentTime,
                        'lead_status' => 3,
                        'split' => $Split,
                        'email' => $Email,
                        'electricbill' => $Electricbill,
                        'company' => null,
                        'is_duplicated' => $IsDuplicated,
                        'note' => $Note,
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_number' => $leadNumber,
                        'firstname' => $FirstName,
                        'lastname' => $LastName,
                        'martial_status' => $MartialStatus,
                        'spouce' => $Spouce,
                        'language' => $Language,
                        'phone' => $Phone,
                        'phone2' => $Phone2,
                        'state' => $State,
                        'city' => $City,
                        'street' => $Street,
                        'zipcode' => $ZipCode,
                        'product' => $Product,
                        'product_desc' => $ProductDescription,
                        'windows_doors_count' => $WindowsDoorsCount,
                        'old_roof' => $OldRoof,
                        'appointment_time' => $AppointmentTime,
                        'split' => $Split,
                        'email' => $Email,
                        'electricbill' => $Electricbill,
                        'company' => null,
                        'is_duplicated' => $IsDuplicated,
                        'note' => $Note,
                        'updated_at' => Carbon::now()
                    ]);
            }
        }

        $Affected1 = DB::table('lead_assignments')
            ->where('lead_id', '=', $LeadId)
            ->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);

        // Delete Store Products
        $Affected2 = DB::table('virtual_lead_assignments')
            ->where('lead_id', $LeadId)
            ->where('user_id', Auth::id())
            ->delete();

        if ($Affected && $Affected1 && $Affected2) {
            DB::commit();
            if (sizeof($Check) > 0) {
                if ($Role == 2) {
                    return redirect(url('/global_manager/assigned-leads'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/assigned-leads'))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information');
                }
            } else {
                if ($Role == 2) {
                    return redirect(url('/global_manager/assigned-leads'))->with('message', 'Lead has been updated successfully');
                } elseif ($Role == 3) {
                    return redirect(url('/confirmationAgent/assigned-leads'))->with('message', 'Lead has been updated successfully');
                }
            }
        } else {
            DB::rollback();
            if ($Role == 2) {
                return redirect(url('/global_manager/assigned-leads'))->with('error', 'Error! An unhandled exception occurred');
            } elseif ($Role == 3) {
                return redirect(url('/confirmationAgent/assigned-leads'))->with('error', 'Error! An unhandled exception occurred');
            }
        }
    }

    public function LoadRepresentativeAllLeads(Request $request)
    {
        $Role = Session::get('user_role');
        // Filter Page
        $FullName = $request->post('FullName');
        $Phone = $request->post('Phone');
        $CityFilter = $request->post('CityFilter');
        $StateFilter = $request->post('StateFilter');
        $ZipcodeFilter = $request->post('ZipcodeFilter');
        $FollowUpTime = $request->post('FollowUpTime');
        $LeadCreationDate = $request->post('LeadCreationDate');
        $Investor = json_decode($request->post('Investor'));
        $Realtor = array();
        $TitleCompany = $request->post('TitleCompany');
        $LeadSource = json_decode($request->post('LeadSource'));
        $DataSource = json_decode($request->post('DataSource'));
        $SearchType = $request->post('SearchType');
        $SearchSubType = $request->post('SearchSubType');
        $LeadSearchStartDate = $request->post('LeadSearchStartDate');
        $LeadSearchEndDate = $request->post('LeadSearchEndDate');

        /* Get User State Filter Data - Start */
        $UserFilterLeadStatus  = array();
        $UserFilterState       = array();
        $UserFilterStartDate   = "";
        $UserFilterEndDate     = "";

        $UserDepartmentFilterRecord = DB::table('user_department_filters')
            ->where('user_id', '=', Auth::id())
            ->where('deleted_at', '=', null)
            ->get();

        if ($UserDepartmentFilterRecord != "" && count($UserDepartmentFilterRecord) > 0) {

            $UserFilterStartDate = $UserDepartmentFilterRecord[0]->start_date;
            $UserFilterEndDate = $UserDepartmentFilterRecord[0]->end_date;

            if ($UserDepartmentFilterRecord[0]->lead_status != "") {
                $UserFilterLeadStatus = $UserDepartmentFilterRecord[0]->lead_status;
                $UserFilterLeadStatus = explode(",", $UserFilterLeadStatus);
            }
            if ($UserDepartmentFilterRecord[0]->state != "") {
                $UserFilterState = $UserDepartmentFilterRecord[0]->state;
                $UserFilterState = explode(",", $UserFilterState);
            }
            if ($UserFilterStartDate != "") {
                $UserFilterStartDate = Carbon::parse($UserFilterStartDate)->format("Y-m-d");
            }
            if ($UserFilterEndDate != "") {
                $UserFilterEndDate = Carbon::parse($UserFilterEndDate)->format("Y-m-d");
            } else {
                $UserFilterEndDate = Carbon::now()->format("Y-m-d");
            }
        }
        /* Get User State Filter Data - End */

        if ($Role == 1 || $Role == 2) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $LeadTeamId = $this->GetLeadTeamId($item->id);
                $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                $Action = '';
                $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                $Action .= '<button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';
                $Url = "";
                if ($Role == 1) {
                    $Url = url('admin/lead/edit/' . $item->id);
                }
                elseif ($Role == 2){
                    $Url = url('global_manager/lead/edit/' . $item->id);
                }
                $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="delete_' . $item->id . '" onclick="DeleteLead(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash-alt"></i></button>';

                // Edit Status Button with Badge
                $ConfirmedBy = "";
                $Phone_Email = "";
                if ($item->confirmed_by != "") {
                    $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                }
                if ($item->phone != "") {
                    $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                }
                if ($item->email != "") {
                    $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                }
                $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                $sub_array = array();
                $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                $sub_array['id'] = $SrNo;
                if($item->lead_source != ''){
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                }
                else{
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                }
                $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                if ($item->appointment_time != "") {
                    $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                } else {
                    $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                }
                $sub_array['lead_type'] = $lead_status;
                $sub_array['action'] = $Action;
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
        elseif ($Role == 3) {
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = "";
                    if ($Role == 3) {
                        $Url = url('acquisition_manager/lead/edit/' . $item->id);
                    } elseif ($Role == 4) {
                        $Url = url('disposition_manager/lead/edit/' . $item->id);
                    }
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';

                    // Edit Status Button with Badge
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = "";
                    $Url = url('acquisition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';

                    // Edit Status Button with Badge
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        elseif ($Role == 4) {
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if ($item->LeadAssignmentUserId != "" && $item->LeadAssignmentUserId == Auth::id()) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';

                    // Edit Status Button with Badge
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ($item->lead_status == 12 && $item->state == $UserState) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';

                    // Edit Status Button with Badge
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button><button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';

                    // Edit Status Button with Badge
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Add new followup"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
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
        elseif ($Role == 5) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_representative/lead/edit/' . $item->id);;
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';

                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_representative/lead/edit/' . $item->id);;
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';

                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                else {
                  unset($fetch_data[$row]);
                }
            }

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
        elseif ($Role == 6) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            // $SrNo = $start + 1;
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                else {
                  unset($fetch_data[$row]);
                }
            }

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
        elseif ($Role == 7) {
            $lead_type = 1;
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            /*Get Assigned Leads*/
            $AssignedLeads = DB::select("SELECT A.lead_id FROM lead_assignments A INNER JOIN leads B ON A.lead_id = B.id WHERE B.user_id = ?", array(Auth::id()));
            $AssignedLeadIds = array();
            foreach ($AssignedLeads as $assignedLead){
                $AssignedLeadIds[] = $assignedLead->lead_id;
            }

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.lead_status', '<>', 1)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.lead_status', '<>', 1)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.lead_status', '<>', 1]
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.lead_status', '<>', 1]
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->role_id == 8)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('cold_caller/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('cold_caller/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                else {
                  unset($fetch_data[$row]);
                }
            }

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
        elseif ($Role == 8) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    // ->where('leads.user_id', '=', Auth::id())
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if ($item->user_id == Auth::id()) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('affiliate/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('affiliate/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                else {
                  unset($fetch_data[$row]);
                }
            }

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
        elseif ($Role == 9) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->where(function ($query) use ($CityFilter, $StateFilter, $ZipcodeFilter, $FollowUpTime, $LeadCreationDate, $TitleCompany, $LeadSource, $SearchType, $SearchSubType, $LeadSearchStartDate, $LeadSearchEndDate) {
                        if ($CityFilter != "0" && $CityFilter != "") {
                            $query->where('leads.city', '=', $CityFilter);
                        }
                        if ($StateFilter != "0") {
                            $query->where('leads.state', '=', $StateFilter);
                        }
                        if ($ZipcodeFilter != "") {
                            $query->where('leads.zipcode', '=', $ZipcodeFilter);
                        }
                        if ($FollowUpTime != "") {
                          $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($FollowUpTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($FollowUpTime)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($LeadCreationDate != "") {
                            $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($LeadCreationDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($LeadCreationDate)->addDays(1)->format('Y-m-d H:i:s')]);
                        }
                        if ($TitleCompany != "0") {
                            $query->where('leads.company', '=', $TitleCompany);
                        }
                        if (sizeof($LeadSource) > 0) {
                            $query->whereIn('leads.lead_source', $LeadSource);
                        }
                        /*Custom Lead Search START*/
                        if($SearchType == 'followUp'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.appointment_time', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        elseif($SearchType == 'creationDate'){
                            if($SearchSubType == 'customRange' || $SearchSubType == 'lastWeek' || $SearchSubType == 'currentWeek' || $SearchSubType == 'nextWeek' || $SearchSubType == 'lastMonth' || $SearchSubType == 'currentMonth' || $SearchSubType == 'nextMonth' || $SearchSubType == 'lastYear' || $SearchSubType == 'CurrentYear'){
                                if ($LeadSearchStartDate != "" && $LeadSearchEndDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchEndDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                            if($SearchSubType == 'yesterday' || $SearchSubType == 'today' || $SearchSubType == 'tomorrow'){
                                if ($LeadSearchStartDate != "") {
                                    $query->whereBetween('leads.created_at', [Carbon::parse($LeadSearchStartDate)->format("Y-m-d"), Carbon::parse($LeadSearchStartDate)->addDays(1)->format("Y-m-d")]);
                                }
                            }
                        }
                        /*Custom Lead Search END*/
                    })
                    ->where(function ($query) use ($FullName) {
                        if ($FullName != "") {
                            $FullName = (explode(" ",$FullName));
                            for ($i=0; $i < count($FullName); $i++) {
                              $query->orWhere('leads.firstname', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.middlename', 'LIKE', '%'.$FullName[$i].'%');
                              $query->orWhere('leads.lastname', 'LIKE', '%'.$FullName[$i].'%');
                            }
                        }
                    })
                    ->where(function ($query) use ($Phone) {
                        if ($Phone != "") {
                            $query->orWhere('leads.phone', '=', $Phone);
                            $query->orWhere('leads.phone2', '=', $Phone);
                        }
                    })
                    ->where(function ($query) use ($Investor) {
                        if (sizeof($Investor) > 0 && $Investor != "") {
                            for($i=0; $i < sizeof($Investor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Investor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($Realtor) {
                        if (sizeof($Realtor) > 0 && $Realtor != "") {
                            for($i=0; $i < sizeof($Realtor); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.investors) > 0", [$Realtor[$i]]);
                            }
                        }
                    })
                    ->where(function ($query) use ($DataSource) {
                        if (sizeof($DataSource) > 0 && $DataSource != "") {
                            for($i=0; $i < sizeof($DataSource); $i++){
                                $query->orWhereRaw("FIND_IN_SET(?, leads.data_source) > 0", [$DataSource[$i]]);
                            }
                        }
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.created_at', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('realtor/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status= '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">'.$this->GetLeadStatusColor($item->lead_status).'</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('realtor/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if($item->lead_source != ''){
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    else{
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                }
                else {
                  unset($fetch_data[$row]);
                }
            }

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
    }

    public function GetConfirmedByName($UserId)
    {
        $user_details = DB::table('profiles')
            ->where('user_id', '=', $UserId)
            ->get();

        return $user_details[0]->firstname . " " . $user_details[0]->lastname;
    }

    public function LoadAllLeadsByLeadNumber(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadNumber = $request['lead_number'];
        if ($Role == 1 || $Role == 2) {
            $lead_type = 1;
            $lead_status = array(21);
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
                $fetch_data = DB::table('leads')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where('leads.lead_number', '=', $LeadNumber)
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where('leads.lead_number', '=', $LeadNumber)
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadNumber) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.lead_number', '=', $LeadNumber],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadNumber) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.lead_number', '=', $LeadNumber],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $lead_status = $this->GetLeadStatusColor($item->lead_status);
                $sub_array = array();
                $sub_array['id'] = $SrNo;
                $sub_array['lead_number'] = $item->lead_number;
                $sub_array['firstname'] = $item->firstname;
                $sub_array['lastname'] = $item->lastname;
                $sub_array['phone'] = $item->phone;
                if ($item->is_duplicated == 1) {
                    $sub_array['is_duplicated'] = '<span class="badge badge-warning">Yes</span>';
                } else {
                    $sub_array['is_duplicated'] = '<span class="badge badge-success">No</span>';
                }
                $sub_array['lead_status'] = $lead_status;
                if ($Role == 1) {
                    $sub_array['add_sale'] = '<button class="btn btn-info mr-2" id="sale_' . $item->id . '_' . $item->lead_number . '_' . $item->contract_amount . '" onclick="AddSaleForm(this.id);"><i class="fas fa-edit"></i></button>';
                } elseif ($Role == 2) {
                    $sub_array['add_sale'] = '<button class="btn btn-info mr-2" id="sale_' . $item->id . '_' . $item->lead_number . '_' . $item->contract_amount . '" onclick="AddSaleForm(this.id);"><i class="fas fa-edit"></i></button>';
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

    public function LoadAllLeadsByLeadPhoneNumber(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadPhoneNumber = $request['phone_number'];
        if ($Role == 1 || $Role == 2) {
            $lead_type = 1;
            $lead_status = array(21);
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
                $fetch_data = DB::table('leads')
                    ->where('leads.deleted_at', '=', null)
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->orWhere('leads.phone', '=', $LeadPhoneNumber);
                        $query->orWhere('leads.phone2', '=', $LeadPhoneNumber);
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.lead_type', '=', $lead_type)
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->orWhere('leads.phone', '=', $LeadPhoneNumber);
                        $query->orWhere('leads.phone2', '=', $LeadPhoneNumber);
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->orWhere('leads.phone', '=', $LeadPhoneNumber);
                        $query->orWhere('leads.phone2', '=', $LeadPhoneNumber);
                    })
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->orWhere('leads.phone', '=', $LeadPhoneNumber);
                        $query->orWhere('leads.phone2', '=', $LeadPhoneNumber);
                    })
                    ->where(function ($query) use ($LeadPhoneNumber) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $lead_status = $this->GetLeadStatusColor($item->lead_status);
                $sub_array = array();
                $sub_array['id'] = $SrNo;
                $sub_array['lead_number'] = $item->lead_number;
                $sub_array['firstname'] = $item->firstname;
                $sub_array['lastname'] = $item->lastname;
                $sub_array['phone'] = $item->phone;
                if ($item->is_duplicated == 1) {
                    $sub_array['is_duplicated'] = '<span class="badge badge-warning">Yes</span>';
                } else {
                    $sub_array['is_duplicated'] = '<span class="badge badge-success">No</span>';
                }
                $sub_array['lead_status'] = $lead_status;
                if ($Role == 1) {
                    $sub_array['add_sale'] = '<button class="btn btn-info mr-2" id="sale_' . $item->id . '_' . $item->lead_number . '_' . $item->contract_amount . '" onclick="AddSaleForm(this.id);"><i class="fas fa-edit"></i></button>';
                } elseif ($Role == 2) {
                    $sub_array['add_sale'] = '<button class="btn btn-info mr-2" id="sale_' . $item->id . '_' . $item->lead_number . '_' . $item->contract_amount . '" onclick="AddSaleForm(this.id);"><i class="fas fa-edit"></i></button>';
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

    public function LoadDashboardAllLeads(Request $request)
    {
        $Role = Session::get('user_role');

        /* Get User State Filter Data - Start */
        $UserFilterLeadStatus = array();
        $UserFilterState = array();
        $UserFilterStartDate = "";
        $UserFilterEndDate = "";

        $UserDepartmentFilterRecord = DB::table('user_department_filters')
            ->where('user_id', '=', Auth::id())
            ->where('deleted_at', '=', null)
            ->get();

        if ($UserDepartmentFilterRecord != "" && count($UserDepartmentFilterRecord) > 0) {

            $UserFilterStartDate = $UserDepartmentFilterRecord[0]->start_date;
            $UserFilterEndDate = $UserDepartmentFilterRecord[0]->end_date;

            if ($UserDepartmentFilterRecord[0]->lead_status != "") {
                $UserFilterLeadStatus = $UserDepartmentFilterRecord[0]->lead_status;
                $UserFilterLeadStatus = explode(",", $UserFilterLeadStatus);
            }
            if ($UserDepartmentFilterRecord[0]->state != "") {
                $UserFilterState = $UserDepartmentFilterRecord[0]->state;
                $UserFilterState = explode(",", $UserFilterState);
            }
            if ($UserFilterStartDate != "") {
                $UserFilterStartDate = Carbon::parse($UserFilterStartDate)->format("Y-m-d");
            }
            if ($UserFilterEndDate != "") {
                $UserFilterEndDate = Carbon::parse($UserFilterEndDate)->format("Y-m-d");
            } else {
                $UserFilterEndDate = Carbon::now()->format("Y-m-d");
            }
        }
        /* Get User State Filter Data - End */

        /* Follow Up Date Range (From today to next 15 days) - Start */
        $CurrentDate = Carbon::now();
        $DateAfter15Days = Carbon::parse($CurrentDate)->addDays(15);
        /* Follow Up Date Range (From today to next 15 days) - End */

        $lead_status = array(3);
        if ($Role == 1 || $Role == 2) {
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.appointment_time', '>=', Carbon::now()],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.appointment_time', '>=', Carbon::now()],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = $start + 1;
            foreach ($fetch_data as $row => $item) {
                $LeadTeamId = $this->GetLeadTeamId($item->id);
                $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                $Action = '';
                $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                $Action .= '<button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';
                $Url = "";
                if ($Role == 1) {
                    $Url = url('admin/lead/edit/' . $item->id);
                } elseif ($Role == 2) {
                    $Url = url('global_manager/lead/edit/' . $item->id);
                }
                $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                $Action .= '<button class="btn greenActionButtonTheme ml-2" id="delete_' . $item->id . '" onclick="DeleteLead(this.id);" data-toggle="tooltip" title="Delete"><i class="fas fa-trash-alt"></i></button>';
                $ConfirmedBy = "";
                $Phone_Email = "";
                if ($item->confirmed_by != "") {
                    $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                }
                if ($item->phone != "") {
                    $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                }
                if ($item->email != "") {
                    $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                }
                $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                $sub_array = array();
                $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                $sub_array['id'] = $SrNo;
                if ($item->lead_source != '') {
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                } else {
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                }
                $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                if ($item->appointment_time != "") {
                    $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                } else {
                    $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                }
                $sub_array['lead_type'] = $lead_status;
                $sub_array['action'] = $Action;
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
        } elseif ($Role == 3) {
            $lead_status = array(1);
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.appointment_time', '>=', Carbon::now()],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                            ['leads.appointment_time', '>=', Carbon::now()],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        } elseif ($Role == 4) {
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS LeadAssignmentUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if ($item->LeadAssignmentUserId != "" && $item->LeadAssignmentUserId == Auth::id()) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_manager/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fa fa-calendar cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
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
        } elseif ($Role == 5) {
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type, $UserState) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type, $UserState) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('acquisition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        } elseif ($Role == 6) {
            $UserState = SiteHelper::GetCurrentUserState();
            $lead_type = 1;
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
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type, $UserState) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type, $UserState) {
                        $query->where([
                            ['leads.deleted_at', '=', null],
                        ]);
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('disposition_representative/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        } elseif ($Role == 7) {
            $lead_type = 1;
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            /*Get Assigned Leads*/
            $AssignedLeads = DB::select("SELECT A.lead_id FROM lead_assignments A INNER JOIN leads B ON A.lead_id = B.id WHERE B.user_id = ?", array(Auth::id()));
            $AssignedLeadIds = array();
            foreach ($AssignedLeads as $assignedLead) {
                $AssignedLeadIds[] = $assignedLead->lead_id;
            }

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->role_id == 8)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('cold_caller/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('cold_caller/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        } elseif ($Role == 8) {
            $lead_type = 1;
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            /*Get Assigned Leads*/
            $AssignedLeads = DB::select("SELECT A.lead_id FROM lead_assignments A INNER JOIN leads B ON A.lead_id = B.id WHERE B.user_id = ?", array(Auth::id()));
            $AssignedLeadIds = array();
            foreach ($AssignedLeads as $assignedLead) {
                $AssignedLeadIds[] = $assignedLead->lead_id;
            }

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->whereNotIn('leads.id', $AssignedLeadIds)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'users.role_id')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->role_id == 8)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('affiliate/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_1" class="cursor-pointer" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    if ($Role == 1 || $Role == 2) {
                        $Action .= '<button class="btn greenActionButtonTheme ml-2" id="leadEvaluation_' . $item->id . '" onclick="LeadEvaluationModal(this.id);" data-toggle="tooltip" title="Evaluation"><i class="fas fa-eye"></i></button>';
                    }
                    $Url = url('affiliate/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
        } elseif ($Role == 9) {
            $lead_type = 1;
            $limit = $request->post('length');
            $start = $request->post('start');
            $searchTerm = $request->post('search')['value'];
            // Filter Page
            $FirstName = $request->post('FirstName');
            $LastName = $request->post('LastName');
            $Phone1 = $request->post('Phone1');
            $Phone2 = $request->post('Phone2');
            $StateFilter = $request->post('StateFilter');
            $Company = $request->post('Company');
            $User = $request->post('User');
            $LeadStatus = $request->post('LeadStatus');
            $StartDate = $request->post('StartDate');
            $EndDate = $request->post('EndDate');
            $AppointmentTime = $request->post('AppointmentTime');
            $LeadType = $request->post('LeadType');

            $columnIndex = $request->post('order')[0]['column']; // Column index
            $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

            $fetch_data = null;
            $recordsTotal = null;
            $recordsFiltered = null;
            if ($searchTerm == '') {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($FirstName, $LastName, $StateFilter, $Company, $User, $LeadStatus, $StartDate, $EndDate, $AppointmentTime, $LeadType) {

                    })
                    ->where(function ($query) use ($Phone1, $Phone2) {

                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where('leads.deleted_at', '=', null)
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($FirstName, $LastName, $StateFilter, $Company, $User, $LeadStatus, $StartDate, $EndDate, $AppointmentTime, $LeadType) {

                    })
                    ->where(function ($query) use ($Phone1, $Phone2) {

                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            } else {
                $fetch_data = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($FirstName, $LastName, $StateFilter, $Company, $User, $LeadStatus, $StartDate, $EndDate, $AppointmentTime, $LeadType) {

                    })
                    ->where(function ($query) use ($Phone1, $Phone2) {

                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->get();
                $recordsTotal = sizeof($fetch_data);
                $recordsFiltered = DB::table('leads')
                    ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                    ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                    ->where('leads.appointment_time', '>=', Carbon::now())
                    ->where(function ($query) use ($lead_type) {
                        $query->where([
                            ['leads.deleted_at', '=', null]
                        ]);
                    })
                    ->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($CurrentDate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($DateAfter15Days)->addDays(1)->format('Y-m-d H:i:s')])
                    ->where(function ($query) use ($searchTerm) {
                        $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                        $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->whereIn('leads.lead_status', $lead_status)
                    ->where(function ($query) use ($FirstName, $LastName, $StateFilter, $Company, $User, $LeadStatus, $StartDate, $EndDate, $AppointmentTime, $LeadType) {

                    })
                    ->where(function ($query) use ($Phone1, $Phone2) {

                    })
                    ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                    ->distinct('leads.id')
                    ->orderBy('leads.appointment_time', 'DESC')
                    ->orderBy($columnName, $columnSortOrder)
                    ->count();
            }

            $data = array();
            $SrNo = 1;
            foreach ($fetch_data as $row => $item) {
                $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
                if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('realtor/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                    $LeadTeamId = $this->GetLeadTeamId($item->id);
                    $lead_status = '<span class="cursor-pointer" id="leadupdatestatus_' . $item->id . '_' . $LeadTeamId . '_2" onclick="showLeadUpdateStatus(this.id);">' . $this->GetLeadStatusColor($item->lead_status) . '</span>';
                    $Action = '';
                    $Action .= '<button class="btn greenActionButtonTheme" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);" data-toggle="tooltip" title="Comment"><i class="fas fa-sticky-note"></i></button>';
                    $Url = url('realtor/lead/edit/' . $item->id);
                    $Action .= '<button class="btn greenActionButtonTheme ml-2" onclick="window.location.href=\'' . $Url . '\'" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>';
                    $ConfirmedBy = "";
                    $Phone_Email = "";
                    if ($item->confirmed_by != "") {
                        $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
                    }
                    if ($item->phone != "") {
                        $Phone_Email .= "<b><a href='tel: " . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "' style='color: black;'>" . SiteHelper::ConvertPhoneNumberFormat($item->phone) . "</a></b><br><br>";
                    }
                    if ($item->email != "") {
                        $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
                    }
                    $LeadInfo = 'M.A.O: <b>' . $item->maximum_allow_offer . '</b><br><br>' . 'ARV: <b>' . $item->arv . '</b>';
                    $sub_array = array();
                    $sub_array['checkbox'] = '<input type="checkbox" class="checkAllBox assignLeadCheckBox" name="checkAllBox[]" value="' . $item->id . '" onchange="CheckIndividualCheckbox();" />';
                    $sub_array['id'] = $SrNo;
                    if ($item->lead_source != '') {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br><b>" . $item->lead_source . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    } else {
                        $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br></b>" . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
                    }
                    $sub_array['seller_information'] = '<span>' . '<span class="cursor-pointer" onclick="window.location.href=\'' . $Url . '\'"><b>' . $item->firstname . " " . $item->lastname . '</b></span>' . wordwrap("<br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
                    $sub_array['last_comment'] = '<span>' . wordwrap($this->GetLeadLastNote($item->id), 30, '<br>') . '</span>';
                    if ($item->appointment_time != "") {
                        $sub_array['appointment_time'] = '<span>' . Carbon::parse($item->appointment_time)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->appointment_time)->format('g:i a') . '</span>' . '<br><br>' . '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    } else {
                        $sub_array['appointment_time'] = '<i class="fas fa-edit cursor-pointer ml-1" id="leadUpdateAppointmentTime_' . $item->id . '" onclick="LeadEditAppointmentTime(this.id);" data-toggle="tooltip" title="Follow Up"></i>';
                    }
                    $sub_array['lead_type'] = $lead_status;
                    $sub_array['action'] = $Action;
                    $SrNo++;
                    $data[] = $sub_array;
                } else {
                    unset($fetch_data[$row]);
                }
            }

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
    }

    function GetLeadLastNote($LeadId)
    {
        $Note = DB::table('history_notes')
            ->where('lead_id', '=', $LeadId)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if (sizeof($Note) > 0) {
            return wordwrap($Note[0]->history_note, 15, '<br>');
        } else {
            // Get Lead Note
            $LeadNote = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->get();

            return $LeadNote[0]->note;
        }
    }

    public function GetLeadTeamId($Id)
    {
        $Lead = DB::table('leads')
            ->where('id', '=', $Id)
            ->get();

        return $Lead[0]->team_id;
    }

    public function GetProductName($ProductId)
    {
        $ProductName = "";
        $product_details = DB::table('products')->where('id', '=', $ProductId)->get();
        foreach ($product_details as $details) {
            $ProductName = $details->name;
        }
        return $ProductName;
    }

    public function GetLeadStatusColor($lead_status)
    {
        if ($lead_status == 1) {
            return '<span class="badge badge-success">Interested</span>';
        } elseif ($lead_status == 2) {
            return '<span class="badge badge-danger">Not Interested</span>';
        } elseif ($lead_status == 3) {
            return '<span class="badge badge-warning">Lead In</span>';
        } elseif ($lead_status == 4) {
            return '<span class="badge badge-primary">Do Not Call</span>';
        } elseif ($lead_status == 5) {
            return '<span class="badge badge-warning" style="background-color:pink;color:white;">No Answer</span>';
        } elseif ($lead_status == 7) {
            return '<span class="badge badge-warning" style="background-color:orange;">Offer Not Given</span>';
        } elseif ($lead_status == 8) {
            return '<span class="badge badge-secondary">Offer Not Accepted</span>';
        } elseif ($lead_status == 9) {
            return '<span class="badge badge-success">Accepted</span>';
        } elseif ($lead_status == 10) {
            return '<span class="badge" style="background-color: #ff9999;">Negotiating with Seller</span>';
        } elseif ($lead_status == 11) {
            return '<span class="badge" style="background-color: #ccff33;color: black;">Agreement Sent</span>';
        } elseif ($lead_status == 12) {
            return '<span class="badge" style="background-color: #ffff99;color: black;">Agreement Received</span>';
        } elseif ($lead_status == 13) {
            return '<span class="badge" style="background-color: #ff9933;">Send To Investor</span>';
        } elseif ($lead_status == 14) {
            return '<span class="badge" style="background-color: #666633;color: white;">Negotiation with Investors</span>';
        } elseif ($lead_status == 15) {
            return '<span class="badge" style="background-color: #009999; color: white;">Sent to Title</span>';
        } elseif ($lead_status == 16) {
            return '<span class="badge" style="background-color: #990099;color: white;">Send Contract to Investor</span>';
        } elseif ($lead_status == 17) {
            return '<span class="badge" style="background-color: #ff9900; color: black;">EMD Received</span>';
        } elseif ($lead_status == 18) {
            return '<span class="badge" style="background-color: #ff66ff; color: black;">EMD Not Received</span>';
        } elseif ($lead_status == 21) {
            return '<span class="badge" style="background-color: #66ffcc; color: black;">Closed WON</span>';
        } elseif ($lead_status == 22) {
            return '<span class="badge" style="background-color: #cc0066; color: white;">Deal Lost</span>';
        } elseif ($lead_status == 23) {
            return '<span class="badge" style="background-color: #cc0066; color: white;">Wrong Number</span>';
        } elseif ($lead_status == 24) {
            return '<span class="badge" style="background-color: #3366cc; color: white;">Inspection</span>';
        } elseif ($lead_status == 25) {
            return '<span class="badge" style="background-color: #1ac6ff; color: white;">Close On</span>';
        }
    }

    // Change Lead Status
    public function AdminChangeLeadStatus($Id)
    {
        $page = "leads";
        $Role = Session::get('user_role');

        $Lead = DB::table('leads')
            ->where('id', '=', $Id)
            ->get();

        $Company = DB::table('buissness_accounts')
            ->where('deleted_at', '=', null)
            ->get();

        return view('admin.lead.change-lead', compact('page', 'Lead', 'Role', 'Company'));
    }

    public function GetLeadStatusName($LeadStatusId)
    {
        if ($LeadStatusId == 1) {
            return "Interested";
        } elseif ($LeadStatusId == 2) {
            return "Not Interested";
        } elseif ($LeadStatusId == 3) {
            return "Lead In";
        } elseif ($LeadStatusId == 4) {
            return "Do Not Call";
        } elseif ($LeadStatusId == 5) {
            return "No Answer";
        } elseif ($LeadStatusId == 6) {
            return "Follow Up";
        } elseif ($LeadStatusId == 7) {
            return "Offer Not Given";
        } elseif ($LeadStatusId == 8) {
            return "Offer Not Accepted";
        } elseif ($LeadStatusId == 9) {
            return "Accepted";
        } elseif ($LeadStatusId == 10) {
            return "Negotiating with Seller";
        } elseif ($LeadStatusId == 11) {
            return "Agreement Sent";
        } elseif ($LeadStatusId == 12) {
            return "Agreement Received";
        } elseif ($LeadStatusId == 13) {
            return "Send To Investor";
        } elseif ($LeadStatusId == 14) {
            return "Negotiation with Investors";
        } elseif ($LeadStatusId == 15) {
            return "Sent to Title";
        } elseif ($LeadStatusId == 16) {
            return "Send Contract to Investor";
        } elseif ($LeadStatusId == 17) {
            return "EMD Received";
        } elseif ($LeadStatusId == 18) {
            return "EMD Not Received";
        } elseif ($LeadStatusId == 21) {
            return "Closed WON";
        } elseif ($LeadStatusId == 22) {
            return "Deal Lost";
        } elseif ($LeadStatusId == 23) {
            return "Wrong Number";
        } elseif ($LeadStatusId == 24) {
            return "Inspection";
        } elseif ($LeadStatusId == 25) {
            return "Close On";
        }
    }

    public function GetLeadClosedDate(Request $request)
    {
      $ClosedDate = "";
      $LeadId = $request['LeadId'];
      $lead_details = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->where('deleted_at', '=', null)
          ->get();

      if ($lead_details != "" && count($lead_details) > 0) {
        $ClosedDate = $lead_details[0]->close_date;
        $ClosedDate = Carbon::parse($ClosedDate)->format('m/d/Y');
      }

      return $ClosedDate;
    }

    public function AdminUpdateLeadStatus(Request $request)
    {
        $user_id = Auth::id();
        $Role = Session::get('user_role');
        $LeadId = $request['id'];
        $LeadStatusType = $request->post('LeadStatusType');
        $LeadStatus = $request->post('LeadStatus');
        $Investors = null;
        if(sizeof(json_decode($request->post('Investors'))) > 0){
            $Investors = implode(',', json_decode($request->post('Investors')));
        }
        $Company = $request->post('Company');
        $Amount = $request->post('Amount');
        $Days = $request->post('Days');
        $ContractAmount = $request->post('ContractAmount');
        $InterestedComments = $request->post('InterestedComments');
        $NotInterestedComments = $request->post('NotInterestedComments');
        $FollowUpTime = $request['FollowUpTime'];
        $_Comments = $request->post('_Comments');
        $ClosedOnDate = $request->post('ClosedOnDate');
        $ClosedDate = $request->post('ClosedDate');
        $CloseWonCost = $request->post('CloseWonCost');
        $PurchaseAmount = $request->post('PurchaseAmount');
        $InspectionPeriod = $request->post('InspectionPeriod');
        $InspectionNumberofDays = $request->post('InspectionNumberofDays');
        $CurrentDate = date('Y-m-d');

        // Record Lead Status Change in the History Note
        // Get Lead Current Status
        $lead_status_details = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->get();

        /*HIstory Note Work*/
        $OldLeadStatus = $this->GetLeadStatusName($lead_status_details[0]->lead_status);
        $CurrentLeadStatus = $this->GetLeadStatusName($LeadStatus);
        $HistoryNote = "Changed the status of lead from " . $OldLeadStatus . " to " . $CurrentLeadStatus;
        HistoryNote::create([
            'user_id' => $user_id,
            'lead_id' => $LeadId,
            'history_note' => $HistoryNote,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        /*Updating Lead Status Work*/
        if ($LeadStatus == 6) {
            if ($FollowUpTime != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'appointment_time' => $FollowUpTime,
                        'updated_at' => Carbon::now()
                    ]);

                // Add follow up time in the lead history table
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Follow up time is: " . Carbon::parse($FollowUpTime)->format('m/d/Y - g:i a'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 1){
            if ($InterestedComments != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'interested_reason' => $InterestedComments,
                        'updated_at' => Carbon::now()
                    ]);

                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Interested reason is: " . $InterestedComments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if ($Role == 7) {
                    // Assign this lead to state Aquisition Manager
                    $GetLeadDetails = DB::table('leads')->where('id', '=', $LeadId)->get();
                    $LeadState = $GetLeadDetails[0]->state;

                    $GetStateAquisitionManager = DB::table('users')
                                                 ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                 ->where('users.role_id', '=', 3)
                                                 ->where('profiles.state', '=', $LeadState)
                                                 ->select('users.*')
                                                 ->get();

                    if ($GetStateAquisitionManager != "" && count($GetStateAquisitionManager) > 0) {
                      // Check if lead and user is avaliable in the lead assignment table then no need to entry again
                      $CheckLeadUserDetails = DB::table('lead_assignments')
                                             ->where('lead_id', '=', $LeadId)
                                             ->where('user_id', '=', $GetStateAquisitionManager[0]->id)
                                             ->count();

                      if ($CheckLeadUserDetails == 0) {
                         $Affected = LeadAssignment::create([
                             'lead_id' => $LeadId,
                             'user_id' => $GetStateAquisitionManager[0]->id,
                             'status' => 0,
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);
                         HistoryNote::create([
                             'user_id' => Auth::id(),
                             'lead_id' => $LeadId,
                             'history_note' => "This lead is assigned to " . $this->GetUserName($GetStateAquisitionManager[0]->id),
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);

                         $item_details = DB::table('leads')
                             ->where('id', '=', $LeadId)
                             ->get();

                         $Message = "Lead #". $item_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                         Notification::create([
                             'lead_id' => $LeadId,
                             'sender_id' => Auth::id(),
                             'reciever_id' => $GetStateAquisitionManager[0]->id,
                             'message' => $Message,
                             'created_at' => Carbon::now()
                         ]);
                      }
                    }
                }
                elseif ($Role == 8) {
                    // Assign this lead to state Aquisition Manager
                    $GetLeadDetails = DB::table('leads')->where('id', '=', $LeadId)->get();
                    $LeadState = $GetLeadDetails[0]->state;

                    $GetStateAquisitionManager = DB::table('users')
                                                 ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                 ->where('users.role_id', '=', 3)
                                                 ->where('profiles.state', '=', $LeadState)
                                                 ->select('users.*')
                                                 ->get();

                    if ($GetStateAquisitionManager != "" && count($GetStateAquisitionManager) > 0) {
                      // Check if lead and user is avaliable in the lead assignment table then no need to entry again
                      $CheckLeadUserDetails = DB::table('lead_assignments')
                                             ->where('lead_id', '=', $LeadId)
                                             ->where('user_id', '=', $GetStateAquisitionManager[0]->id)
                                             ->count();

                      if ($CheckLeadUserDetails == 0) {
                         $Affected = LeadAssignment::create([
                             'lead_id' => $LeadId,
                             'user_id' => $GetStateAquisitionManager[0]->id,
                             'status' => 0,
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);
                         HistoryNote::create([
                             'user_id' => Auth::id(),
                             'lead_id' => $LeadId,
                             'history_note' => "This lead is assigned to " . $this->GetUserName($GetStateAquisitionManager[0]->id),
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);

                         $item_details = DB::table('leads')
                             ->where('id', '=', $LeadId)
                             ->get();

                         $Message = "Lead #". $item_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                         Notification::create([
                             'lead_id' => $LeadId,
                             'sender_id' => Auth::id(),
                             'reciever_id' => $GetStateAquisitionManager[0]->id,
                             'message' => $Message,
                             'created_at' => Carbon::now()
                         ]);
                      }
                    }
                }
                elseif ($Role == 9) {
                    // Assign this lead to state Aquisition Manager
                    $GetLeadDetails = DB::table('leads')->where('id', '=', $LeadId)->get();
                    $LeadState = $GetLeadDetails[0]->state;

                    $GetStateAquisitionManager = DB::table('users')
                                                 ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                                 ->where('users.role_id', '=', 3)
                                                 ->where('profiles.state', '=', $LeadState)
                                                 ->select('users.*')
                                                 ->get();

                    if ($GetStateAquisitionManager != "" && count($GetStateAquisitionManager) > 0) {
                      // Check if lead and user is avaliable in the lead assignment table then no need to entry again
                      $CheckLeadUserDetails = DB::table('lead_assignments')
                                             ->where('lead_id', '=', $LeadId)
                                             ->where('user_id', '=', $GetStateAquisitionManager[0]->id)
                                             ->count();

                      if ($CheckLeadUserDetails == 0) {
                         $Affected = LeadAssignment::create([
                             'lead_id' => $LeadId,
                             'user_id' => $GetStateAquisitionManager[0]->id,
                             'status' => 0,
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);
                         HistoryNote::create([
                             'user_id' => Auth::id(),
                             'lead_id' => $LeadId,
                             'history_note' => "This lead is assigned to " . $this->GetUserName($GetStateAquisitionManager[0]->id),
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now()
                         ]);

                         $item_details = DB::table('leads')
                             ->where('id', '=', $LeadId)
                             ->get();

                         $Message = "Lead #". $item_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                         Notification::create([
                             'lead_id' => $LeadId,
                             'sender_id' => Auth::id(),
                             'reciever_id' => $GetStateAquisitionManager[0]->id,
                             'message' => $Message,
                             'created_at' => Carbon::now()
                         ]);
                      }
                    }
                }

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 2){
            if ($NotInterestedComments != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'not_interested_reason' => $NotInterestedComments,
                        'updated_at' => Carbon::now()
                    ]);

                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Not Interested reason is: " . $NotInterestedComments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 5){
            /*Update status plus follow up as well*/
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'appointment_time' => $FollowUpTime,
                    'updated_at' => Carbon::now()
                ]);

            // Follow Up note
            $Affected1 = null;
            if ($_Comments != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Follow Up note: " . $_Comments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Add follow up time in the lead history table
            $Affected1 = HistoryNote::create([
                'user_id' => $user_id,
                'lead_id' => $LeadId,
                'history_note' => "Follow up time is: " . Carbon::parse($FollowUpTime)->format('m/d/Y - g:i a'),
                'created_at' => Carbon::now(),
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
        elseif($LeadStatus == 7 || $LeadStatus == 8 || $LeadStatus == 10){
            /*Update status plus follow up as well*/
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'updated_at' => Carbon::now()
                ]);

            // Follow Up note
            $Affected1 = null;
            if ($_Comments != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Note: " . $_Comments,
                    'created_at' => Carbon::now(),
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
        elseif($LeadStatus == 12){
            // Get lead disposition manager details
            $lead_details = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->get();

            $disposition_manager_details = DB::table('users')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where('users.role_id', '=', 4)
                ->where('users.deleted_at', '=', null)
                ->where('profiles.state', '=', $lead_details[0]->state)
                ->select('users.id')
                ->get();

            if ($ContractAmount != "") {
                $Affected = null;
                $Affected1 = null;
                $Affected2 = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'contract_amount' => $ContractAmount,
                        'updated_at' => Carbon::now()
                    ]);

                if ($disposition_manager_details != "" && count($disposition_manager_details) > 0) {
                  $Affected1 = Notification::create([
                      'lead_id' => $LeadId,
                      'sender_id' => Auth::id(),
                      'reciever_id' => $disposition_manager_details[0]->id,
                      'message' => "We receive the agreement for lead #" . $lead_details[0]->lead_number . ". Please work on it as
                      soon as you can or assigned it to one of your team members 😇.",
                      'created_at' => Carbon::now(),
                      'updated_at' => Carbon::now()
                  ]);
                }

                $Affected2 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Note: Lead has been assigned to disposition. ",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                if ($Affected && $Affected2) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 13){
            if ($Investors != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'investors' => $Investors,
                        'updated_at' => Carbon::now()
                    ]);

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 14){
            /*Update status plus follow up as well*/
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'appointment_time' => $FollowUpTime,
                    'updated_at' => Carbon::now()
                ]);

            // Follow Up note
            $Affected1 = null;
            if ($_Comments != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Follow Up note: " . $_Comments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Add follow up time in the lead history table
            $Affected1 = HistoryNote::create([
                'user_id' => $user_id,
                'lead_id' => $LeadId,
                'history_note' => "Follow up time is: " . Carbon::parse($FollowUpTime)->format('m/d/Y - g:i a'),
                'created_at' => Carbon::now(),
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
        elseif($LeadStatus == 15) {
            if ($Company != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'company' => $Company,
                        'updated_at' => Carbon::now()
                    ]);

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 16){
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'investors' => $Investors,
                    'updated_at' => Carbon::now()
                ]);

            // Add Purchase Amount into History Notes
            if ($PurchaseAmount != "") {
              $Affected1 = HistoryNote::create([
                  'user_id' => $user_id,
                  'lead_id' => $LeadId,
                  'history_note' => "Purchase Amount is: $" . $PurchaseAmount,
                  'created_at' => Carbon::now(),
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
        elseif($LeadStatus == 17){
            if ($Amount != "") {
                $Affected = null;
                $Affected = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->update([
                        'lead_status' => $LeadStatus,
                        'emd_amount' => $Amount,
                        'updated_at' => Carbon::now()
                    ]);

                if ($Affected) {
                    DB::commit();
                    echo "Success";
                } else {
                    DB::rollback();
                    echo "Error";
                }
            } else {
                echo "Error";
            }
        }
        elseif($LeadStatus == 18){
            /*Update status plus follow up as well*/
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'closing_days' => $Days,
                    'appointment_time' => $FollowUpTime,
                    'updated_at' => Carbon::now()
                ]);

            // Follow Up note
            $Affected1 = null;
            if ($_Comments != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Follow Up note: " . $_Comments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Add follow up time in the lead history table
            $Affected1 = HistoryNote::create([
                'user_id' => $user_id,
                'lead_id' => $LeadId,
                'history_note' => "Follow up time is: " . Carbon::parse($FollowUpTime)->format('m/d/Y - g:i a'),
                'created_at' => Carbon::now(),
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
        elseif($LeadStatus == 21){
            /*Update status plus follow up as well*/
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
                    'close_date' => Carbon::parse($ClosedDate)->format('Y-m-d'),
                    'updated_at' => Carbon::now()
                ]);

            // Close Won date
            $Affected1 = null;
            if ($ClosedDate != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Lead closed at: " . Carbon::parse($ClosedDate)->format('m/d/Y'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Add Close Won Cost into History Notes
            if ($CloseWonCost != "") {
              $Affected1 = HistoryNote::create([
                  'user_id' => $user_id,
                  'lead_id' => $LeadId,
                  'history_note' => "Close Won cost is: $" . $CloseWonCost,
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now()
              ]);
            }

            // Notifications Work - Managers and Up - Start
            $lead_details = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->get();
            $Message = "We won deal #". $lead_details[0]->lead_number ." 😀.";
            $this->SendLeadStatusChangeNotification($LeadId, $Message);
            // Notifications Work - Managers and Up - End

            if ($Affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Error";
            }
        }
        elseif($LeadStatus == 22){
          $Affected = null;
          $Affected = DB::table('leads')
              ->where('id', '=', $LeadId)
              ->update([
                  'lead_status' => $LeadStatus,
                  'updated_at' => Carbon::now()
              ]);

            // Deal Lost Note note
            $Affected1 = null;
            if ($_Comments != "") {
                $Affected1 = HistoryNote::create([
                    'user_id' => $user_id,
                    'lead_id' => $LeadId,
                    'history_note' => "Deal lost reason: " . $_Comments,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Notifications Work - Managers and Up - Start
            $lead_details = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->get();
            $Message = "Lead #". $lead_details[0]->lead_number ." is lost 😢!";
            $this->SendLeadStatusChangeNotification($LeadId, $Message);
            // Notifications Work - Managers and Up - End

            if ($Affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Error";
            }
        }
        elseif($LeadStatus == 24){
          $Affected = null;
          $Affected = DB::table('leads')
              ->where('id', '=', $LeadId)
              ->update([
                  'lead_status' => $LeadStatus,
                  'updated_at' => Carbon::now()
              ]);

            // Deal Lost Note note
            $Affected1 = null;
            if ($InspectionPeriod != "") {
                if ($InspectionPeriod == "Yes") {
                  $Affected1 = HistoryNote::create([
                      'user_id' => $user_id,
                      'lead_id' => $LeadId,
                      'history_note' => "Inspection Period: " . $InspectionNumberofDays . " days.",
                      'created_at' => Carbon::now(),
                      'updated_at' => Carbon::now()
                  ]);

                  // Notifications Work - General Manager and Up - Start
                  $lead_details = DB::table('leads')
                      ->where('id', '=', $LeadId)
                      ->get();
                  $Message = "We have ". $InspectionNumberofDays ." days to inspect property #". $lead_details[0]->lead_number . " ";
                  $this->SendLeadStatusInspectionNotification($LeadId, $Message);
                  // Notifications Work - General Manager and Up - End

                } elseif ($InspectionPeriod == "No") {
                  $Affected1 = HistoryNote::create([
                      'user_id' => $user_id,
                      'lead_id' => $LeadId,
                      'history_note' => "Inspection period is not provided.",
                      'created_at' => Carbon::now(),
                      'updated_at' => Carbon::now()
                  ]);
                }
            }

            if ($Affected) {
                DB::commit();
                echo "Success";
            } else {
                DB::rollback();
                echo "Error";
            }
        }
        elseif($LeadStatus == 25){
          $Affected = null;
          $Affected = DB::table('leads')
              ->where('id', '=', $LeadId)
              ->update([
                  'lead_status' => $LeadStatus,
                  'close_date' => $ClosedOnDate,
                  'updated_at' => Carbon::now()
              ]);

            // Deal Lost Note note
            $Affected1 = null;
            if ($ClosedOnDate != "") {
              $Affected1 = HistoryNote::create([
                  'user_id' => $user_id,
                  'lead_id' => $LeadId,
                  'history_note' => "Lead close on date is: " . Carbon::parse($ClosedOnDate)->format('m/d/Y'),
                  'created_at' => Carbon::now(),
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
        else{
            $Affected = null;
            $Affected = DB::table('leads')
                ->where('id', '=', $LeadId)
                ->update([
                    'lead_status' => $LeadStatus,
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

    function SendLeadStatusChangeNotification($LeadId, $Message){
      // First get lead state
      $lead_details = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->get();

      $State = $lead_details[0]->state;

      // Get State Aquisition Manager
      $StateAqusitionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 3)
          ->where('profiles.state', '=', $State)
          ->select('users.id')
          ->get();

      if ($StateAqusitionManagerDetails != "" && count($StateAqusitionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $StateAqusitionManagerDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get State Disposition Manager
      $StateDispositionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 4)
          ->where('profiles.state', '=', $State)
          ->select('users.id')
          ->get();

      if ($StateDispositionManagerDetails != "" && count($StateDispositionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $StateDispositionManagerDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get Global Manager
      $GlobalManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 2)
          ->select('users.id')
          ->get();

      if ($GlobalManagerDetails != "" && count($GlobalManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $GlobalManagerDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get Admin
      $AdminDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 1)
          ->select('users.id')
          ->get();

      if ($AdminDetails != "" && count($AdminDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $AdminDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }
    }

    function SendLeadStatusInspectionNotification($LeadId, $Message){
      // First get lead state
      $lead_details = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->get();

      $State = $lead_details[0]->state;

      // Get State Disposition Manager
      $StateDispositionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 4)
          ->where('profiles.state', '=', $State)
          ->select('users.id')
          ->get();

      if ($StateDispositionManagerDetails != "" && count($StateDispositionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $StateDispositionManagerDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get Global Manager
      $GlobalManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 2)
          ->select('users.id')
          ->get();

      if ($GlobalManagerDetails != "" && count($GlobalManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => Auth::id(),
              'reciever_id' => $GlobalManagerDetails[0]->id,
              'message' => $Message,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }
    }

    function AdminUpdateLeadAppointmentTime(Request $request){
        $user_id = Auth::id();
        $LeadId = $request['id'];
        $FollowUpTime = $request['FollowUpTime'];
        $FollowUpNotes = $request['FollowUpNotes'];

        $Affected = null;
        $Affected = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->update([
                'appointment_time' => $FollowUpTime,
                'updated_at' => Carbon::now()
            ]);

        /*Add History Note of Follow up notes*/
        $Affected1 = null;
        if ($FollowUpNotes != "") {
          $Affected1 = HistoryNote::create([
              'user_id' => $user_id,
              'lead_id' => $LeadId,
              'history_note' => "Follow Up note: " . $FollowUpNotes,
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now()
          ]);
        }

        // Add follow up time in the lead history table
        $Affected2 = HistoryNote::create([
            'user_id' => $user_id,
            'lead_id' => $LeadId,
            'history_note' => "Follow up time is: " . Carbon::parse($FollowUpTime)->format('m/d/Y - g:i a'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($Affected && $Affected2) {
            DB::commit();
            echo "Success";
        } else {
            DB::rollback();
            echo "Error";
        }
    }

    public function HistoryNoteStore(Request $request)
    {
        $LeadId = $request['LeadId'];
        $HistoryNote = $request['HistoryNote'];
        $user_id = Auth::id();

        DB::beginTransaction();
        $affected = HistoryNote::create([
            'user_id' => $user_id,
            'lead_id' => $LeadId,
            'history_note' => $HistoryNote,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ($affected) {
            DB::commit();
            echo "Success";
        } else {
            DB::rollback();
            echo "Error";
        }
    }

    public function LoadHistoryNote(Request $request)
    {
        $LeadId = $request['LeadId'];
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
            $fetch_data = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('history_notes.history_note', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('history_notes.created_at', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('history_notes')
                ->join('profiles', 'profiles.user_id', '=', 'history_notes.user_id')
                ->where('history_notes.deleted_at', '=', null)
                ->where('history_notes.lead_id', '=', $LeadId)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('history_notes.history_note', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('history_notes.created_at', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('history_notes.*', 'profiles.firstname', 'profiles.lastname')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['user_id'] = '<span>' . $item->firstname . " " . $item->lastname . '<br><br>' . Carbon::parse($item->created_at)->format('m/d/Y') . '<br><br>' . Carbon::parse($item->created_at)->format('g:i A');
            $sub_array['history_note'] = '<span>' . wordwrap($item->history_note, '60', '<br>') . '</span>';
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

    public function ImportLeads()
    {
        // $page = "import";
        $Role = Session::get('user_role');
        return view('admin.lead.import', compact('page', 'Role'));
    }
    public function ImportLeadsView()
    {
        $page = "importleadview";
        $Role = Session::get('user_role');
        return view('admin.lead.import-view', compact('page', 'Role'));
    }


    public function ImportLeadsStore(Request $request)
    {
        // dd("Test");
        Excel::import(new LeadsImport(), $request->file('file'));
        return back()->with('message', 'Leads has been imported successfully.');
    }

    function AssignedUsersToLead(Request $request)
    {
        $LeadId = $request->post('LeadId');
        $Users = DB::table('lead_assignments')
            ->where('lead_id', '=', $LeadId)
            ->get();
        if (sizeof($Users) > 0) {
            echo json_encode($Users);
        } else {
            echo json_encode([]);
        }
        exit();
    }

    function GetUserName($UserId) {
      $FullName = "";
      $user_details = DB::table('users')
          ->where('users.deleted_at', '=', null)
          ->where('users.id', '=', $UserId)
          ->join('profiles', 'profiles.user_id', '=', 'users.id')
          ->select('profiles.firstname AS firstname', 'profiles.middlename AS middlename', 'profiles.lastname AS lastname')
          ->get();

      if ($user_details != "" && count($user_details) > 0) {
        if ($user_details[0]->firstname != "") {
          $FullName .= $user_details[0]->firstname;
        }
        if ($user_details[0]->middlename != "") {
          $FullName .= " " . $user_details[0]->middlename;
        }
        if ($user_details[0]->lastname != "") {
          $FullName .= " " . $user_details[0]->lastname;
        }
      }

      return $FullName;
    }

    function GetUserFirstLastName($UserId) {
      $FullName = "";
      $user_details = DB::table('users')
          ->where('users.deleted_at', '=', null)
          ->where('users.id', '=', $UserId)
          ->join('profiles', 'profiles.user_id', '=', 'users.id')
          ->select('profiles.firstname AS firstname', 'profiles.middlename AS middlename', 'profiles.lastname AS lastname')
          ->get();

      if ($user_details != "" && count($user_details) > 0) {
        if ($user_details[0]->firstname != "") {
          $FullName .= $user_details[0]->firstname;
        }
        if ($user_details[0]->lastname != "") {
          $FullName .= " " . $user_details[0]->lastname;
        }
      }

      return $FullName;
    }

    function AssignLeadToUser(Request $request)
    {
        $Role = Session::get('user_role');
        $LeadId = $request->post('LeadId');
        $lead_details = DB::table('leads')
            ->where('id', '=', $LeadId)
            ->get();

        if ($Role == 7) {
          // Assign this lead to state Aquisition Manager
          $GetLeadDetails = DB::table('leads')->where('id', '=', $LeadId)->get();
          $LeadState = $GetLeadDetails[0]->state;

          $GetStateAquisitionManager = DB::table('users')
                                       ->join('profiles', 'users.id', '=', 'profiles.user_id')
                                       ->where('users.role_id', '=', 3)
                                       ->where('profiles.state', '=', $LeadState)
                                       ->select('users.*')
                                       ->get();

          if ($GetStateAquisitionManager != "") {
             // Check if lead and user is avaliable in the lead assignment table then no need to entry again
             $CheckLeadUserDetails = DB::table('lead_assignments')
                                    ->where('lead_id', '=', $LeadId)
                                    ->where('user_id', '=', $GetStateAquisitionManager[0]->id)
                                    ->count();

             if ($CheckLeadUserDetails == 0) {
                DB::beginTransaction();
                $Affected = null;
                $Affected = LeadAssignment::create([
                    'lead_id' => $LeadId,
                    'user_id' => $GetStateAquisitionManager[0]->id,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                HistoryNote::create([
                    'user_id' => Auth::id(),
                    'lead_id' => $LeadId,
                    'history_note' => "This lead is assigned to " . $this->GetUserName($GetStateAquisitionManager[0]->id),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $Message = "Lead #". $lead_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                Notification::create([
                    'lead_id' => $LeadId,
                    'sender_id' => Auth::id(),
                    'reciever_id' => $GetStateAquisitionManager[0]->id,
                    'message' => $Message,
                    'created_at' => Carbon::now()
                ]);

                if ($Affected) {
                    DB::commit();
                    echo json_encode(['message' => 'success']);
                } else {
                    DB::rollback();
                    echo json_encode(['message' => 'failed']);
                }
                exit();
             }
             else {
               echo json_encode(['message' => 'Error! Lead is already assigned to state aquisition manager.']);
               exit();
             }
          }
          else {
            echo json_encode(['message' => 'Error! No state aquisition manager is avaliable.']);
            exit();
          }
        }
        else {
          $Users = json_decode($request->post('Users'));
          $Affected = null;
          DB::beginTransaction();
          foreach ($Users as $user) {
              // Check if lead and user is avaliable in the lead assignment table then no need to entry again
              $CheckLeadUserDetails = DB::table('lead_assignments')
                                      ->where('lead_id', '=', $LeadId)
                                      ->where('user_id', '=', $user)
                                      ->count();

              if ($CheckLeadUserDetails == 0) {
                $Affected = LeadAssignment::create([
                    'lead_id' => $LeadId,
                    'user_id' => $user,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                HistoryNote::create([
                    'user_id' => Auth::id(),
                    'lead_id' => $LeadId,
                    'history_note' => "This lead is assigned to " . $this->GetUserName($user),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $Message = "Lead #". $lead_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";
                Notification::create([
                    'lead_id' => $LeadId,
                    'sender_id' => Auth::id(),
                    'reciever_id' => $user,
                    'message' => $Message,
                    'created_at' => Carbon::now()
                ]);
              }
          }
          DB::commit();
          if ($Affected) {
              echo json_encode(['message' => 'success']);
          } else {
              echo json_encode(['message' => 'failed']);
          }
          exit();
        }
    }

    function AssignedLeads()
    {
        $page = 'assignedLeads';
        $Role = Session::get('user_role');
        $CartMessage = "";

        $AssignedLeads = DB::table('leads')
            ->join('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
            ->where('lead_assignments.user_id', '=', Auth::id())
            ->where('lead_assignments.status', '=', 0)
            ->where('leads.deleted_at', '=', null)
            ->whereNotIn('lead_assignments.lead_id', function ($query) {
                $query->selectRaw('virtual_lead_assignments.lead_id')
                    ->from('virtual_lead_assignments')
                    ->where('virtual_lead_assignments.user_id', '!=', Auth::id());
            })
            ->select('leads.*')
            ->limit(1)
            ->get();

        // Add Entry in Virtual Lead Assignment Table
        foreach ($AssignedLeads as $lead) {
            // Check if entry is not in the virtual table
            $virtual_lead_table_check = DB::table('virtual_lead_assignments')
                ->where('virtual_lead_assignments.lead_id', '=', $lead->id)
                ->where('virtual_lead_assignments.user_id', '=', Auth::id())
                ->count();

            if ($virtual_lead_table_check == 0) {
                DB::beginTransaction();
                $Affected = VirtualLeadAssignment::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                if ($Affected) {
                    DB::commit();
                } else {
                    DB::rollback();
                }
            }
        }

        // Get All Products
        $products = DB::table('products')
            ->where('deleted_at', '=', null)
            ->get();

        // All States
        $states = DB::table('states')
            ->get();

        // All Companies
        $Company = DB::table('buissness_accounts')
            ->where('deleted_at', '=', null)
            ->get();

        $TotalCompletedProgress = $this->CalculateUserProgress();

        if (count($AssignedLeads) == 0) {
            // Total Assigned Leads
            $TotalAssignedLeads = DB::table('lead_assignments')->where('user_id', Auth::id())->count();
            $TotalCompletedLeads = DB::table('lead_assignments')->where('user_id', Auth::id())->where('status', 1)->count();
            $CartMessage = "You completed {" . $TotalAssignedLeads . "} out of {" . $TotalCompletedLeads . "}. Your Cart is empty.";
        }

        return view('admin.lead.assigned-leads', compact('page', 'Role', 'AssignedLeads', 'products', 'states', 'Company', 'TotalCompletedProgress', 'CartMessage'));
    }

    function MarkLeadAsComplete(Request $request)
    {
        $LeadId = $request->post('LeadId');
        $Affected = DB::table('lead_assignments')
            ->where('lead_id', '=', $LeadId)
            // ->where('user_id', '=', Auth::id())
            ->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        if ($Affected) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
        exit();
    }

    public function CalculateUserProgress()
    {
        $UserProgress = 0;
        //Total User Assigned Leads
        $TotalAssignedLeads = DB::table('lead_assignments')
            ->where('user_id', Auth::id())
            ->count();

        if ($TotalAssignedLeads > 0) {
            // Total User Completed Leads
            $TotalCompletedAssignedLeads = DB::table('lead_assignments')
                ->where('user_id', Auth::id())
                ->where('status', '=', 1)
                ->count();

            $UserProgress = (($TotalCompletedAssignedLeads / $TotalAssignedLeads) * 100);
            return bcdiv($UserProgress, 1, 0) . "%";
        } else {
            return bcdiv($UserProgress, 1, 0) . "%";
        }
    }

    public function CalculateProgress(Request $request)
    {
        $UserProgress = 0;
        //Total User Assigned Leads
        $TotalAssignedLeads = DB::table('lead_assignments')
            ->where('user_id', Auth::id())
            ->count();

        if ($TotalAssignedLeads > 0) {
            // Total User Completed Leads
            $TotalCompletedAssignedLeads = DB::table('lead_assignments')
                ->where('user_id', Auth::id())
                ->where('status', '=', 1)
                ->count();

            $UserProgress = (($TotalCompletedAssignedLeads / $TotalAssignedLeads) * 100);
            echo bcdiv($UserProgress, 1, 0) . "%";
        } else {
            echo bcdiv($UserProgress, 1, 0) . "%";
        }
    }

    function AssignLeads()
    {
        $page = "assignLeads";
        $Role = Session::get('user_role');
        return view('admin.lead.assign-leads', compact('page', 'Role'));
    }

    function LoadAllAssignLeads(Request $request)
    {
        $Role = Session::get('user_role');
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request->post('search')['value'];
        // Filter Page
        $LeadStatus = $request->post('LeadStatus');
        $StartDate = $request->post('StartDate');
        $EndDate = $request->post('EndDate');

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = DB::table('leads')
                ->leftJoin('products', 'leads.product', '=', 'products.id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->where(function ($query) use ($LeadStatus, $StartDate, $EndDate) {
                    if ($LeadStatus != 0) {
                        $query->where('leads.lead_type', '=', $LeadStatus);
                    }
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('leads.*', 'products.name As ProductName', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('leads')
                ->leftJoin('products', 'leads.product', '=', 'products.id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->where(function ($query) use ($LeadStatus, $StartDate, $EndDate) {
                    if ($LeadStatus != 0) {
                        $query->where('leads.lead_type', '=', $LeadStatus);
                    }
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('leads.*', 'products.name As ProductName', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = DB::table('leads')
                ->leftJoin('products', 'leads.product', '=', 'products.id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where(function ($query) {
                    $query->where([
                        ['leads.deleted_at', '=', null],
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.appointment_time', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->where(function ($query) use ($LeadStatus, $StartDate, $EndDate) {
                    if ($LeadStatus != 0) {
                        $query->where('leads.lead_type', '=', $LeadStatus);
                    }
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('leads.*', 'products.name As ProductName', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy($columnName, $columnSortOrder)
                ->offset($start)
                ->limit($limit)
                ->get();
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = DB::table('leads')
                ->leftJoin('products', 'leads.product', '=', 'products.id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where(function ($query) {
                    $query->where([
                        ['leads.deleted_at', '=', null],
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('leads.lead_number', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.lastname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.phone', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.phone2', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.state', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('leads.appointment_time', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.firstname', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('profiles.lastname', 'LIKE', '%' . $searchTerm . '%');
                })
                ->where(function ($query) use ($LeadStatus, $StartDate, $EndDate) {
                    if ($LeadStatus != 0) {
                        $query->where('leads.lead_type', '=', $LeadStatus);
                    }
                    if ($StartDate != "" && $EndDate != "") {
                        $query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                    }
                })
                ->select('leads.*', 'products.name As ProductName', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $LeadTeamId = $this->GetLeadTeamId($item->id);
            $lead_status = $this->GetLeadStatusColor($item->lead_status);
            $Action = '';
            $ConfirmedBy = "";
            $Phone_Email = "";
            if ($item->confirmed_by != "") {
                $ConfirmedBy = $this->GetConfirmedByName($item->confirmed_by);
            }
            if ($item->phone != "") {
                $Phone_Email .= "<b><a href='tel: " . $item->phone . "' style='color: black;'>" . $item->phone . "</a></b><br><br>";
            }
            if ($item->phone2 != "") {
                $Phone_Email .= "<b><a href='tel: " . $item->phone2 . "' style='color: black;'>" . $item->phone2 . "</a></b><br><br>";
            }
            if ($item->email != "") {
                $Phone_Email .= "<a href='mailto:" . $item->email . "' style='color: black;'>" . $item->email . "</a>";
            }
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['lead_header'] = '<span>' . wordwrap("<b>" . $item->lead_number . "</b><br><br>" . $item->user_first . ' ' . $item->user_last . "<br><br>".  $item->lead_source . "<br><br>"  . Carbon::parse($item->created_at)->format('m/d/Y g:i a'), 15, '<br><br>') . '</span>';
            $sub_array['homeowner_address'] = '<span>' . wordwrap("<b>" . $item->firstname . " " . $item->lastname . "</b><br><br>" . $item->street . ", " . $item->city . ", " . $item->state . " " . $item->zipcode . "<br><br>", 20, '<br>') . $Phone_Email . '</span>';
            $sub_array['product_appt'] = '<span>' . wordwrap("<b>" . $item->ProductName . "</b><br><br>" . Carbon::parse($item->appointment_time)->format('m/d/Y - g:i a'), 15, '<br>') . '</span>';
            $sub_array['last_note'] = $this->GetLeadLastNote($item->id);
            $sub_array['lead_type'] = $lead_status;

            if ($item->lead_status == 4 || $item->lead_status == 5) {
                $Action .= '<button class="btn btn-info" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);"><i class="fas fa-sticky-note"></i></button>';
            } else {
                $Action .= '<button class="btn btn-info" id="leadhistory_' . $item->id . '" onclick="showHistoryPreviousNotes(this.id);"><i class="fas fa-sticky-note"></i></button>';
            }
            $Action .= '<button class="btn btn-info ml-2" onclick="window.location.href=\'' . url('admin/lead/edit/' . $item->id) . '\'"><i class="fas fa-edit"></i></button><button class="btn btn-info ml-2" id="assign_' . $item->id . '" onclick="AssignLeadToUser(this.id);"><i class="fas fa-arrow-alt-circle-right"></i></button>';
            $sub_array['action'] = $Action;

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

    function AssignLeadsToUsers(Request $request)
    {
        $Users = json_decode($request->post('Users'));
        // Filter Page
        $FirstName = $request->post('FirstName');
        $LastName = $request->post('LastName');
        $Phone1 = $request->post('Phone1');
        $Phone2 = $request->post('Phone2');
        $StateFilter = $request->post('StateFilter');
        $Company = $request->post('Company');
        $User = $request->post('User');
        $LeadStatus = $request->post('LeadStatus');
        $StartDate = $request->post('StartDate');
        $EndDate = $request->post('EndDate');
        $AppointmentTime = $request->post('AppointmentTime');
        $LeadType = $request->post('LeadType');

        $Leads = DB::table('leads')
            ->leftJoin('products', 'leads.product', '=', 'products.id')
            ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
            ->where('leads.deleted_at', '=', null)
            ->where(function ($query) use ($FirstName, $LastName, $StateFilter, $Company, $User, $LeadStatus, $StartDate, $EndDate, $AppointmentTime, $LeadType) {
                if ($FirstName != "") {
                    $query->where('leads.firstname', '=', $FirstName);
                }
                if ($LastName != "") {
                    $query->where('leads.lastname', '=', $LastName);
                }
                if ($StateFilter != "0") {
                    $query->where('leads.state', '=', $StateFilter);
                }
                if ($Company != 0) {
                    $query->where('leads.company', '=', $Company);
                }
                if ($User != 0) {
                    $query->where('leads.user_id', '=', $User);
                }
                if ($LeadStatus != 0) {
                    $query->where('leads.lead_status', '=', $LeadStatus);
                }
                if ($StartDate != "" && $EndDate != "") {
                    $query->whereBetween('leads.created_at', [Carbon::parse($StartDate)->format("Y-m-d"), Carbon::parse($EndDate)->addDays(1)->format("Y-m-d")]);
                }
                if ($AppointmentTime != "") {
                    $query->whereBetween('leads.appointment_time', [Carbon::parse(Carbon::parse($AppointmentTime)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($AppointmentTime)->addDays(1)->format('Y-m-d H:i:s')]);
                }
                if ($LeadType != 0) {
                    $query->where('leads.lead_type', '=', $LeadType);
                }
            })
            ->where(function ($query) use ($Phone1, $Phone2) {
                if ($Phone1 != "") {
                    $query->orWhere('leads.phone', '=', $Phone1);
                }
                if ($Phone2 != "") {
                    $query->orWhere('leads.phone2', '=', $Phone2);
                }
            })
            ->select('leads.*', 'products.name As ProductName', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
            ->get();

        $Affected = null;
        DB::beginTransaction();
        /* Delete All Assigned entries of the leads */
        foreach ($Leads as $lead) {
            DB::table('lead_assignments')
                ->where('lead_id', '=', $lead->id)
                ->delete();
        }
        /* Add new assigned entries */
        foreach ($Users as $user) {
            foreach ($Leads as $lead) {
                $Affected = LeadAssignment::create([
                    'lead_id' => $lead->id,
                    'user_id' => $user,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $item_details = DB::table('leads')
                    ->where('id', '=', $lead->id)
                    ->get();

                $Message = "Lead #". $item_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                Notification::create([
                    'lead_id' => $LeadId,
                    'sender_id' => Auth::id(),
                    'reciever_id' => $user,
                    'message' => $Message,
                    'created_at' => Carbon::now()
                ]);
            }
        }
        DB::commit();
        if ($Affected) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
        exit();
    }

    function LeadDetails(Request $request)
    {
        $Id = $request->post('Id');
        $Lead = DB::table('leads')
            ->where('id', '=', $Id)
            ->get();
        echo json_encode($Lead);
        exit();
    }

    /* Lead Funnel */
    function leadFunnel($Type = null, $StartDate = null, $EndDate = null) {
        $page = "lead-funnel";
        $Role = Session::get('user_role');
        $UserState = SiteHelper::GetCurrentUserState();
        $firstdate = "";
        $lastdate = "";
        if ($Type == "Recent Week") {
          $firstdate = Carbon::now()->startOfWeek();
          $lastdate = Carbon::now()->endOfWeek();
        }
        elseif ($Type == "Recent Month") {
          $firstdate = Carbon::now()->startOfMonth();
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Quarter") {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(2);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Semester") {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(5);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Year") {
          $firstdate = Carbon::now()->startOfYear();
          $lastdate = Carbon::now()->endOfYear();
        }
        elseif($Type == "All Time") {
          $firstdate = "";
          $lastdate = "";
        }
        elseif($Type == "Range") {
          $firstdate = $StartDate;
          $lastdate = $EndDate;
        }

        /* Get User State Filter Data - Start */
        $UserFilterLeadStatus = array();
        $UserFilterState      = array();
        $UserFilterStartDate  = "";
        $UserFilterEndDate    = "";

        $UserDepartmentFilterRecord = DB::table('user_department_filters')
                  ->where('user_id', '=', Auth::id())
                  ->where('deleted_at', '=', null)
                  ->get();

        if ($UserDepartmentFilterRecord != "" && count($UserDepartmentFilterRecord) > 0) {

          $UserFilterStartDate = $UserDepartmentFilterRecord[0]->start_date;
          $UserFilterEndDate   = $UserDepartmentFilterRecord[0]->end_date;

          if ($UserDepartmentFilterRecord[0]->lead_status != "") {
            $UserFilterLeadStatus = $UserDepartmentFilterRecord[0]->lead_status;
            $UserFilterLeadStatus = explode(",", $UserFilterLeadStatus);
          }
          if ($UserDepartmentFilterRecord[0]->state != "") {
            $UserFilterState = $UserDepartmentFilterRecord[0]->state;
            $UserFilterState = explode(",", $UserFilterState);
          }
          if ($UserFilterStartDate != "") {
            $UserFilterStartDate = Carbon::parse($UserFilterStartDate)->format("Y-m-d");
          }
          if ($UserFilterEndDate != "") {
            $UserFilterEndDate = Carbon::parse($UserFilterEndDate)->format("Y-m-d");
          } else {
            $UserFilterEndDate = Carbon::now()->format("Y-m-d");
          }
        }
        /* Get User State Filter Data - End */

        if($Role == 1 || $Role == 2){
            $lead_status = array(3);
            $LeadIn = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(7, 8, 9, 10, 11, 1);
            $AssignedToAcquisitions = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(12);
            $SellerUnderContract = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(13, 14, 16);
            $AssignedToDispositions = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(17, 18);
            $BuyerContract = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(15);
            $SentToTitle = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(21);
            $ClosedWon = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $lead_status = array(22);
            $DealLost = DB::table('leads')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last')
                ->orderBy('id', 'DESC')
                ->get();
            $MaxTotalRecords = 0;
            if(sizeof($LeadIn) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($LeadIn);
            }
            if(sizeof($AssignedToAcquisitions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToAcquisitions);
            }
            if(sizeof($SellerUnderContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SellerUnderContract);
            }
            if(sizeof($AssignedToDispositions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToDispositions);
            }
            if(sizeof($BuyerContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($BuyerContract);
            }
            if(sizeof($SentToTitle) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SentToTitle);
            }
            if(sizeof($ClosedWon) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($ClosedWon);
            }
            if(sizeof($DealLost) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($DealLost);
            }
            return view('admin.lead.lead-funnel', compact('page', 'Role', 'Type', 'StartDate', 'EndDate', 'LeadIn', 'AssignedToAcquisitions', 'SellerUnderContract', 'AssignedToDispositions', 'BuyerContract', 'SentToTitle', 'ClosedWon', 'DealLost', 'MaxTotalRecords'));
        }
        elseif($Role == 3){
            $lead_status = array(3);
            $Data =  array();
            $LeadIn = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($LeadIn as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) || in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $LeadId = $Data;
            $Data = array();

            $lead_status = array(7, 8, 9, 10, 11, 1);
            $AssignedToAcquisitions = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($AssignedToAcquisitions as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) || in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $AssignedToAcquisitions = $Data;
            $Data = array();

            $lead_status = array(12);
            $SellerUnderContract = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($SellerUnderContract as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $SellerUnderContract = $Data;
            $Data = array();

            $lead_status = array(13, 14, 16);
            $AssignedToDispositions = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($AssignedToDispositions as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $AssignedToDispositions = $Data;
            $Data = array();

            $lead_status = array(17, 18);
            $BuyerContract = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($BuyerContract as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $BuyerContract = $Data;
            $Data = array();

            $lead_status = array(15);
            $SentToTitle = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($SentToTitle as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $SentToTitle = $Data;
            $Data = array();

            $lead_status = array(21);
            $ClosedWon = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($ClosedWon as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $ClosedWon = $Data;
            $Data = array();

            $lead_status = array(22);
            $DealLost = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) use ($UserState) {
                //     $query->orWhere('leads.state', '=', $UserState);
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($DealLost as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->state == $UserState) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $DealLost = $Data;
            $Data = array();

            $MaxTotalRecords = 0;
            if(sizeof($LeadIn) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($LeadIn);
            }
            if(sizeof($AssignedToAcquisitions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToAcquisitions);
            }
            if(sizeof($SellerUnderContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SellerUnderContract);
            }
            if(sizeof($AssignedToDispositions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToDispositions);
            }
            if(sizeof($BuyerContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($BuyerContract);
            }
            if(sizeof($SentToTitle) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SentToTitle);
            }
            if(sizeof($ClosedWon) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($ClosedWon);
            }
            if(sizeof($DealLost) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($DealLost);
            }
            return view('admin.lead.lead-funnel', compact('page', 'Role', 'Type', 'StartDate', 'EndDate', 'LeadIn', 'AssignedToAcquisitions', 'SellerUnderContract', 'AssignedToDispositions', 'BuyerContract', 'SentToTitle', 'ClosedWon', 'DealLost', 'MaxTotalRecords'));
        }
        elseif($Role == 4 || $Role == 5 || $Role == 6){
            $Data =  array();
            $lead_status = array(3);
            $LeadIn = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($LeadIn as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $LeadId = $Data;
            $Data = array();

            $lead_status = array(7, 8, 9, 10, 11, 1);
            $AssignedToAcquisitions = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->whereIn('leads.lead_status', $lead_status)
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($AssignedToAcquisitions as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $AssignedToAcquisitions = $Data;
            $Data = array();

            $lead_status = array(12);
            $SellerUnderContract = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($SellerUnderContract as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $SellerUnderContract = $Data;
            $Data = array();

            $lead_status = array(13, 14, 16);
            $AssignedToDispositions = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->whereIn('leads.lead_status', $lead_status)
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($AssignedToDispositions as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $AssignedToDispositions = $Data;
            $Data = array();

            $lead_status = array(17, 18);
            $BuyerContract = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($BuyerContract as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $BuyerContract = $Data;
            $Data = array();

            $lead_status = array(15);
            $SentToTitle = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($SentToTitle as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $SentToTitle = $Data;
            $Data = array();

            $lead_status = array(21);
            $ClosedWon = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($ClosedWon as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $ClosedWon = $Data;
            $Data = array();

            $lead_status = array(22);
            $DealLost = DB::table('leads')
                ->leftJoin('lead_assignments', 'leads.id', '=', 'lead_assignments.lead_id')
                ->leftJoin('profiles', 'leads.user_id', '=', 'profiles.user_id')
                ->where('leads.deleted_at', '=', null)
                /*->where('leads.state', '=', $UserState)*/
                ->whereIn('leads.lead_status', $lead_status)
                ->where(function ($query) use ($firstdate, $lastdate) {
                  if ($firstdate != "" && $lastdate != "") {
                      $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
                  }
                })
                // ->where(function ($query) {
                //     $query->orWhere('leads.user_id', '=', Auth::id());
                //     $query->orWhere('lead_assignments.user_id', '=', Auth::id());
                // })
                ->select('leads.*', 'profiles.firstname AS user_first', 'profiles.lastname AS user_last', 'lead_assignments.user_id AS AssignLeadUserId')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($DealLost as $item) {
              $LeadCreationDate = Carbon::parse($item->created_at)->format("Y-m-d");
              if (($item->user_id == Auth::id()) || ($item->AssignLeadUserId == Auth::id())) {
                array_push($Data,$item);
              }
              elseif ((in_array($item->lead_status, $UserFilterLeadStatus) && in_array($item->state, $UserFilterState)) && ($LeadCreationDate >= $UserFilterStartDate && $LeadCreationDate <= $UserFilterEndDate)) {
                array_push($Data,$item);
              }
            }
            $DealLost = $Data;
            $Data = array();

            $MaxTotalRecords = 0;
            if(sizeof($LeadIn) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($LeadIn);
            }
            if(sizeof($AssignedToAcquisitions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToAcquisitions);
            }
            if(sizeof($SellerUnderContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SellerUnderContract);
            }
            if(sizeof($AssignedToDispositions) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($AssignedToDispositions);
            }
            if(sizeof($BuyerContract) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($BuyerContract);
            }
            if(sizeof($SentToTitle) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($SentToTitle);
            }
            if(sizeof($ClosedWon) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($ClosedWon);
            }
            if(sizeof($DealLost) > $MaxTotalRecords){
                $MaxTotalRecords = sizeof($DealLost);
            }
            return view('admin.lead.lead-funnel', compact('page', 'Role', 'Type', 'StartDate', 'EndDate', 'LeadIn', 'AssignedToAcquisitions', 'SellerUnderContract', 'AssignedToDispositions', 'BuyerContract', 'SentToTitle', 'ClosedWon', 'DealLost', 'MaxTotalRecords'));
        }
    }

    /*Assign Selected Leads To Users*/
    function AssignSelectedLeadsToUsers(Request $request){
        if($request->has('checkAllBox') && $request->has('__assignUsers')){
            $Users = $request->post('__assignUsers');
            $Leads = $request->post('checkAllBox');
            $Affected = null;
            $Role = Session::get('user_role');

            DB::beginTransaction();
            foreach ($Leads as $lead){
                $LeadId = $lead;
                $lead_details = DB::table('leads')
                    ->where('id', '=', $LeadId)
                    ->get();

                if ($Role == 7) {
                    // Assign this lead to state Aquisition Manager
                    $GetLeadDetails = DB::table('leads')->where('id', '=', $LeadId)->get();
                    $LeadState = $GetLeadDetails[0]->state;

                    $GetStateAquisitionManager = DB::table('users')
                        ->join('profiles', 'users.id', '=', 'profiles.user_id')
                        ->where('users.role_id', '=', 3)
                        ->where('profiles.state', '=', $LeadState)
                        ->select('users.*')
                        ->get();

                    if ($GetStateAquisitionManager != "") {
                        // Check if lead and user is available in the lead assignment table then no need to entry again
                        $CheckLeadUserDetails = DB::table('lead_assignments')
                            ->where('lead_id', '=', $LeadId)
                            ->where('user_id', '=', $GetStateAquisitionManager[0]->id)
                            ->count();

                        if ($CheckLeadUserDetails == 0) {
                            $Affected = null;
                            $Affected = LeadAssignment::create([
                                'lead_id' => $LeadId,
                                'user_id' => $GetStateAquisitionManager[0]->id,
                                'status' => 0,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                            HistoryNote::create([
                                'user_id' => Auth::id(),
                                'lead_id' => $LeadId,
                                'history_note' => "This lead is assigned to " . $this->GetUserName($GetStateAquisitionManager[0]->id),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                            $Message = "Lead #". $lead_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";

                            Notification::create([
                                'lead_id' => $LeadId,
                                'sender_id' => Auth::id(),
                                'reciever_id' => $GetStateAquisitionManager[0]->id,
                                'message' => $Message,
                                'created_at' => Carbon::now()
                            ]);
                        }
                    }
                }
                else {
                    foreach ($Users as $user) {
                        // Check if lead and user is available in the lead assignment table then no need to entry again
                        $CheckLeadUserDetails = DB::table('lead_assignments')
                            ->where('lead_id', '=', $LeadId)
                            ->where('user_id', '=', $user)
                            ->count();

                        if ($CheckLeadUserDetails == 0) {
                            $Affected = LeadAssignment::create([
                                'lead_id' => $LeadId,
                                'user_id' => $user,
                                'status' => 0,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                            HistoryNote::create([
                                'user_id' => Auth::id(),
                                'lead_id' => $LeadId,
                                'history_note' => "This lead is assigned to " . $this->GetUserName($user),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                            $Message = "Lead #". $lead_details[0]->lead_number ." was assigned to you by " . $this->GetUserFirstLastName(Auth::id()) . " 🤗.";
                            Notification::create([
                                'lead_id' => $LeadId,
                                'sender_id' => Auth::id(),
                                'reciever_id' => $user,
                                'message' => $Message,
                                'created_at' => Carbon::now()
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        }
        return back();
    }

    function CoverageArea(){
        $ApiKey = $_ENV['GOOGLE_MAPS_API_KEY'];
        $page = "";
        $Role = Session::get('user_role');
        $Leads = DB::table('leads')
            ->where('deleted_at', '=', null)
            ->orderBy('created_at', 'DESC')
            ->get();
        $LeadLocations = DB::table('location_coordinates')
            ->where('type', '=', 'lead')
            ->get();
        $LeadIds = array();
        foreach ($LeadLocations as $leadLocation){
            $LeadIds[] = $leadLocation->type_id;
        }

        /*Add Location Record if not exists*/
        DB::beginTransaction();
        foreach ($Leads as $lead){
            $Address = $lead->street . ", " . $lead->city . ", " . $lead->state . " " . $lead->zipcode;
            if(!in_array($lead->id, $LeadIds)){
                $endpoint = "https://maps.googleapis.com/maps/api/geocode/json";
                $client = new Client();
                try {
                    $response = $client->request('GET', $endpoint, ['query' => [
                        'address' => $Address,
                        'key' => $ApiKey
                    ]]);
                    $statusCode = $response->getStatusCode();
                    $content = $response->getBody();
                    if($statusCode == 200){
                        $Response = json_decode($content);
                        if(isset($Response->results[0])){
                            $FormattedAddress = $Response->results[0]->formatted_address;
                            $Lat = $Response->results[0]->geometry->location->lat;
                            $Lng = $Response->results[0]->geometry->location->lng;
                            location_coordinates::create([
                                'type_id' => $lead->id,
                                'type' => 'lead',
                                'formatted_address' => $FormattedAddress,
                                'lat' => $Lat,
                                'long' => $Lng,
                                'type_last_updated' => $lead->updated_at,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    }
                } catch (\Exception $e) { }
            }
            else{
                $_Type_Last_Updated = DB::table('location_coordinates')
                    ->where('type_id', '=', $lead->id)
                    ->where('type', '=', 'lead')
                    ->select('type_last_updated')
                    ->get()[0]->type_last_updated;
                if($lead->updated_at != $_Type_Last_Updated){
                    $endpoint = "https://maps.googleapis.com/maps/api/geocode/json";
                    $client = new Client();
                    try {
                        $response = $client->request('GET', $endpoint, ['query' => [
                            'address' => $Address,
                            'key' => $ApiKey
                        ]]);
                        $statusCode = $response->getStatusCode();
                        $content = $response->getBody();
                        if($statusCode == 200){
                            $Response = json_decode($content);
                            if(isset($Response->results[0])){
                                $FormattedAddress = $Response->results[0]->formatted_address;
                                $Lat = $Response->results[0]->geometry->location->lat;
                                $Lng = $Response->results[0]->geometry->location->lng;
                                DB::table('location_coordinates')
                                    ->where('type_id', '=', $lead->id)
                                    ->update([
                                        'formatted_address' => $FormattedAddress,
                                        'lat' => $Lat,
                                        'long' => $Lng,
                                        'type_last_updated' => $lead->updated_at,
                                        'updated_at' => Carbon::now()
                                    ]);
                            }
                        }
                    } catch (\Exception $e) { }
                }
            }
        }
        DB::commit();
        $LeadLocations = DB::table('location_coordinates')
            ->leftJoin('leads', 'location_coordinates.type_id', '=', 'leads.id')
            ->where('type', '=', 'lead')
            ->select('location_coordinates.*', 'leads.contract_amount', 'leads.firstname', 'leads.lastname', 'leads.lead_status', 'leads.created_at AS DateCreated', 'leads.lead_number')
            ->get();

        return view('admin.coverage-area.index', compact('page', 'Role', 'LeadLocations'));
    }

    function ZoningSearchLead(Request $request){
        $SearchTerm = $request->post('search');
        $LeadLocations = DB::table('location_coordinates')
            ->leftJoin('leads', 'location_coordinates.type_id', '=', 'leads.id')
            ->where('type', '=', 'lead')
            ->where(function ($query) use ($SearchTerm) {
                $query->orWhere('leads.lead_number', 'LIKE', '%' . $SearchTerm . '%');
                $query->orWhere('leads.firstname', 'LIKE', '%' . $SearchTerm . '%');
                $query->orWhere('leads.lastname', 'LIKE', '%' . $SearchTerm . '%');
            })
            ->select('location_coordinates.*', 'leads.contract_amount', 'leads.firstname', 'leads.lastname', 'leads.lead_status', 'leads.created_at AS DateCreated', 'leads.lead_number')
            ->get();

        $Count = 0;
        $LeadsController = new \App\Http\Controllers\LeadController();
        $Rows = '';
        foreach($LeadLocations as $location){
            $Value = $location->contract_amount != ''? '$' . number_format($location->contract_amount) : '$0' ;
            $Parameters = "'" . $location->lat . "', '" . $location->long . "'";
            $Name = $location->firstname . " " . $location->lastname;
            $Initials = strtoupper(substr($location->firstname, 0, 1) . substr($location->lastname, 0, 1));
            $Rows .= '<div class="col-12 col-md-6 p-1">
                        <div class="card" onclick="SetMapCenter(' . $Parameters . ');" style="cursor: pointer;">
                            <div class="card-body">
                                <h6 class="card-title mb-1">'
                                    . $Name .
                                    '<p class="mt-1 mb-0" style="font-size: small;">' . $location->lead_number . '</p>
                                </h6>
                                <p class="mb-1" style="font-size: x-small;">Amount: <b>' . $Value . '</b></p>
                                <p class="mb-1" style="font-size: x-small;">Close Date: <b>' . Carbon::parse($location->DateCreated)->format('m/d/Y') . '</b></p>
                                <p class="mb-1" style="font-size: x-small;">Address: <b>' . $location->formatted_address . '</b></p>
                                <p class="mb-3" style="font-size: x-small;">' . $LeadsController->GetLeadStatusColor($location->lead_status) . '</p>
                                <span style="border-radius: 50px; background: #15D16C; padding: 5px; color: #000;">' . $Initials . '</span>
                            </div>
                        </div>
                      </div>';
        }

        echo base64_encode($Rows);
        exit();
    }
}
