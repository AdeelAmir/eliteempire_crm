<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralLink extends Controller
{
    /*Referral Link*/
    function ReferralLink(){
        $Url = explode("/", $_SERVER['REQUEST_URI']);
        $UserId = base64_decode(end($Url));
        // All States
        $states = DB::table('states')
            ->get();
        return view('admin.lead.referral-lead', compact('UserId', 'states'));
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

    public function LeadStore(Request $request)
    {
        $LeadType = 1;
        $LeadStatus = 3; // Lead in
        $user_id = $request->post('user_id');
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
            return redirect(url('lead/add/') . '/' . base64_encode($user_id))->with('error', $Message);
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

        if ($affected) {
            DB::commit();
            if (sizeof($Check) > 0) {
                return redirect(url('lead/add/') . '/' . base64_encode($user_id))->with('error', 'This lead have been submitted by a different user. Please contact your manager for more information.');
            } else {
                return redirect(url('lead/add/') . '/' . base64_encode($user_id))->with('message', 'Lead has been sent successfully')->with('leadStore', $LeadNumber);
            }
        } else {
            DB::rollback();
            return redirect(url('lead/add/') . '/' . base64_encode($user_id))->with('error', 'Error! An unhandled exception occurred');
        }
    }
}