<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $page = "kpi";
        $Role = Session::get('user_role');
        $firstdate = Carbon::now()->startOfMonth();
        $lastdate = Carbon::now()->endOfMonth();

        // Lead Lead Source Record
        $D4D_leadsource = 0;
        $PropStream_leadsource = 0;
        $Calling_leadsource = 0;
        $Text_leadsource = 0;
        $Facebook_leadsource = 0;
        $Instagram_leadsource = 0;
        $Website_leadsource = 0;
        $Zillow_leadsource = 0;
        $Wholesaler_leadsource = 0;
        $Realtor_leadsource = 0;
        $Investor_leadsource = 0;
        $Radio_leadsource = 0;
        $JVPartner_leadsource = 0;
        $BandedSign_leadsource = 0;

        // Lead Data Source Record
        $OnMarket_datasource = 0;
        $Vacant_datasource = 0;
        $Liens_datasource = 0;
        $PreForeclosures_datasource = 0;
        $Auctions_datasource = 0;
        $BankOwned_datasource = 0;
        $CashBuyers_datasource = 0;
        $HighEquity_datasource = 0;
        $FreeClear_datasource = 0;
        $Bankruptcy_datasource = 0;
        $Divorce_datasource = 0;
        $TaxDelinquencies_datasource = 0;
        $Flippers_datasource = 0;
        $FailedListings_datasource = 0;
        $SeniorOwners_datasource = 0;
        $VacantLand_datasource = 0;
        $TiredLandlords_datasource = 0;
        $PreProbate_datasource = 0;
        $Others_datasource = 0;

        // Lead Status
        $ClosedWon               = 0;
        $Interested              = 0;
        $NotInterested           = 0;
        $NotInterested           = 0;
        $LeadIn                  = 0;
        $DoNotCall               = 0;
        $NoAnswer                = 0;
        $FollowUp                = 0;
        $OfferNotGiven           = 0;
        $OfferNotAccepted        = 0;
        $Accepted                = 0;
        $NegotiatingWithSeller   = 0;
        $AgreementSent           = 0;
        $AgreementReceived       = 0;
        $SendToInvestor          = 0;
        $NegotiationWithInvestor = 0;
        $SendToTitle             = 0;
        $SendContractToInvestor  = 0;
        $EMDReceived             = 0;
        $EMDNotReceived          = 0;
        $Inspection              = 0;
        $CloseOn                 = 0;
        $DealLost                = 0;
        $WrongNumber             = 0;

        $leads = DB::table('leads')
            ->where('leads.deleted_at', '=', null)
            ->get();

        foreach ($leads as $lead) {

          // Lead Source
          if ($lead->lead_source == "basic") {
              $D4D_leadsource++;
          } elseif ($lead->lead_source == "propStream") {
              $PropStream_leadsource++;
          } elseif ($lead->lead_source == "calling") {
              $Calling_leadsource++;
          } elseif ($lead->lead_source == "text") {
              $Text_leadsource++;
          } elseif ($lead->lead_source == "facebook") {
              $Facebook_leadsource++;
          } elseif ($lead->lead_source == "instagram") {
              $Instagram_leadsource++;
          } elseif ($lead->lead_source == "website") {
              $Website_leadsource++;
          } elseif ($lead->lead_source == "zillow") {
              $Zillow_leadsource++;
          } elseif ($lead->lead_source == "wholesaler") {
              $Wholesaler_leadsource++;
          } elseif ($lead->lead_source == "realtor") {
              $Realtor_leadsource++;
          } elseif ($lead->lead_source == "investor") {
              $Investor_leadsource++;
          } elseif ($lead->lead_source == "radio") {
              $Radio_leadsource++;
          } elseif ($lead->lead_source == "jv_partner") {
              $JVPartner_leadsource++;
          } elseif ($lead->lead_source == "banded_sign") {
              $BandedSign_leadsource++;
          }

          // Data Source
          $LeadDataSource = explode(",", $lead->data_source);
          // Data Source
          if (in_array("On Market", $LeadDataSource)) {
              $OnMarket_datasource++;
          } if (in_array("Vacant", $LeadDataSource)) {
              $Vacant_datasource++;
          } if (in_array("Liens", $LeadDataSource)) {
              $Liens_datasource++;
          } if (in_array("Pre-Foreclosures", $LeadDataSource)) {
              $PreForeclosures_datasource++;
          } if (in_array("Auctions", $LeadDataSource)) {
              $Auctions_datasource++;
          } if (in_array("Bank Owned", $LeadDataSource)) {
              $BankOwned_datasource++;
          } if (in_array("Cash Buyers", $LeadDataSource)) {
              $CashBuyers_datasource++;
          } if (in_array("High Equity", $LeadDataSource)) {
              $HighEquity_datasource++;
          } if (in_array("Free & Clear", $LeadDataSource)) {
              $FreeClear_datasource++;
          } if (in_array("Bankruptcy", $LeadDataSource)) {
              $Bankruptcy_datasource++;
          } if (in_array("Divorce", $LeadDataSource)) {
              $Divorce_datasource++;
          } if (in_array("Tax Delinquencies", $LeadDataSource)) {
              $TaxDelinquencies_datasource++;
          } if (in_array("Flippers", $LeadDataSource)) {
              $Flippers_datasource++;
          } if (in_array("Failed Listings", $LeadDataSource)) {
              $FailedListings_datasource++;
          } if (in_array("Senior Owners", $LeadDataSource)) {
              $SeniorOwners_datasource++;
          } if (in_array("Vacant Land", $LeadDataSource)) {
              $VacantLand_datasource++;
          } if (in_array("Tired Landlords", $LeadDataSource)) {
              $TiredLandlords_datasource++;
          } if (in_array("Pre-Probate (Deceased Owner)", $LeadDataSource)) {
              $PreProbate_datasource++;
          } if (in_array("Others", $LeadDataSource)) {
              $Others_datasource++;
          }

          // Lead Closed Won/Interested
          if ($lead->lead_status == 1) {
              $Interested++;
          } elseif ($lead->lead_status == 21) {
              $ClosedWon++;
          } elseif ($lead->lead_status == 2) {
              $NotInterested++;
          } elseif ($lead->lead_status == 3) {
              $LeadIn++;
          } elseif ($lead->lead_status == 4) {
              $DoNotCall++;
          } elseif ($lead->lead_status == 5) {
              $NoAnswer++;
          } elseif ($lead->lead_status == 6) {
              $FollowUp++;
          } elseif ($lead->lead_status == 7) {
              $OfferNotGiven++;
          } elseif ($lead->lead_status == 8) {
              $OfferNotAccepted++;
          } elseif ($lead->lead_status == 9) {
              $Accepted++;
          } elseif ($lead->lead_status == 10) {
              $NegotiatingWithSeller++;
          } elseif ($lead->lead_status == 11) {
              $AgreementSent++;
          } elseif ($lead->lead_status == 12) {
              $AgreementReceived++;
          } elseif ($lead->lead_status == 13) {
              $SendToInvestor++;
          } elseif ($lead->lead_status == 14) {
              $NegotiationWithInvestor++;
          } elseif ($lead->lead_status == 15) {
              $SendToTitle++;
          } elseif ($lead->lead_status == 16) {
              $SendContractToInvestor++;
          } elseif ($lead->lead_status == 17) {
              $EMDReceived++;
          } elseif ($lead->lead_status == 18) {
              $EMDNotReceived++;
          } elseif ($lead->lead_status == 22) {
              $DealLost++;
          } elseif ($lead->lead_status == 23) {
              $WrongNumber++;
          } elseif ($lead->lead_status == 24) {
              $Inspection++;
          } elseif ($lead->lead_status == 25) {
              $CloseOn++;
          }
        }

        return view('admin.kpi.index', compact('page', 'Role', 'D4D_leadsource', 'PropStream_leadsource', 'Calling_leadsource', 'Text_leadsource', 'Facebook_leadsource', 'Instagram_leadsource', 'Website_leadsource', 'Zillow_leadsource', 'Wholesaler_leadsource', 'Realtor_leadsource', 'Investor_leadsource', 'Radio_leadsource', 'JVPartner_leadsource', 'BandedSign_leadsource', 'OnMarket_datasource', 'Vacant_datasource', 'Liens_datasource', 'PreForeclosures_datasource', 'Auctions_datasource', 'BankOwned_datasource', 'CashBuyers_datasource', 'HighEquity_datasource', 'FreeClear_datasource', 'Bankruptcy_datasource', 'Divorce_datasource', 'TaxDelinquencies_datasource', 'Flippers_datasource', 'FailedListings_datasource', 'SeniorOwners_datasource', 'VacantLand_datasource', 'TiredLandlords_datasource', 'PreProbate_datasource', 'Others_datasource', 'Interested', 'ClosedWon', 'NotInterested', 'LeadIn', 'DoNotCall', 'NoAnswer', 'FollowUp', 'OfferNotGiven', 'OfferNotAccepted', 'Accepted', 'NegotiatingWithSeller', 'AgreementSent', 'AgreementReceived', 'SendToInvestor', 'NegotiationWithInvestor', 'SendToTitle', 'SendContractToInvestor', 'EMDReceived', 'EMDNotReceived', 'DealLost', 'WrongNumber', 'Inspection', 'CloseOn'));
    }

    public function GetLeadSourceAnalytics(Request $request)
    {
        $Type = $request['type'];
        $firstdate = "";
        $lastdate = "";

        if($Type == "Recent Week") {
          $firstdate = Carbon::now()->startOfWeek();
          $lastdate = Carbon::now()->endOfWeek();
        }
        elseif ($Type == "Recent Month")
        {
          $firstdate = Carbon::now()->startOfMonth();
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Quarter")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(2);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Semester")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(5);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Year")
        {
          $firstdate = Carbon::now()->startOfYear();
          $lastdate = Carbon::now()->endOfYear();
        }
        elseif($Type == "All Time")
        {
          $firstdate = "";
          $lastdate = "";
        }
        elseif($Type == "Range")
        {
          $firstdate = $request['StartDate'];
          $lastdate = $request['EndDate'];
        }

        // Lead Data Source Record
        $D4D_leadsource = 0;
        $PropStream_leadsource = 0;
        $Calling_leadsource = 0;
        $Text_leadsource = 0;
        $Facebook_leadsource = 0;
        $Instagram_leadsource = 0;
        $Website_leadsource = 0;
        $Zillow_leadsource = 0;
        $Wholesaler_leadsource = 0;
        $Realtor_leadsource = 0;
        $Investor_leadsource = 0;
        $Radio_leadsource = 0;
        $JVPartner_leadsource = 0;
        $BandedSign_leadsource = 0;

        $leads = DB::table('leads')
            ->where('leads.deleted_at', '=', null)
            ->where(function ($query) use ($firstdate, $lastdate) {
              if ($firstdate != "" && $lastdate != "") {
                  $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
              }
            })
            ->get();

        foreach ($leads as $lead) {
          if ($lead->lead_source == "basic") {
              $D4D_leadsource++;
          } elseif ($lead->lead_source == "propStream") {
              $PropStream_leadsource++;
          } elseif ($lead->lead_source == "calling") {
              $Calling_leadsource++;
          } elseif ($lead->lead_source == "text") {
              $Text_leadsource++;
          } elseif ($lead->lead_source == "facebook") {
              $Facebook_leadsource++;
          } elseif ($lead->lead_source == "instagram") {
              $Instagram_leadsource++;
          } elseif ($lead->lead_source == "website") {
              $Website_leadsource++;
          } elseif ($lead->lead_source == "zillow") {
              $Zillow_leadsource++;
          } elseif ($lead->lead_source == "wholesaler") {
              $Wholesaler_leadsource++;
          } elseif ($lead->lead_source == "realtor") {
              $Realtor_leadsource++;
          } elseif ($lead->lead_source == "investor") {
              $Investor_leadsource++;
          } elseif ($lead->lead_source == "radio") {
              $Radio_leadsource++;
          } elseif ($lead->lead_source == "jv_partner") {
              $JVPartner_leadsource++;
          } elseif ($lead->lead_source == "banded_sign") {
              $BandedSign_leadsource++;
          }
        }

        $Data['firstdate'] = $firstdate;
        $Data['lastdate'] = $lastdate;
        $Data['d4d'] = $D4D_leadsource;
        $Data['PropStream'] = $PropStream_leadsource;
        $Data['Calling'] = $Calling_leadsource;
        $Data['Text'] = $Text_leadsource;
        $Data['Facebook'] = $Facebook_leadsource;
        $Data['Instagram'] = $Instagram_leadsource;
        $Data['Website'] = $Website_leadsource;
        $Data['Zillow'] = $Zillow_leadsource;
        $Data['Wholesaler'] = $Wholesaler_leadsource;
        $Data['Realtor'] = $Realtor_leadsource;
        $Data['Investor'] = $Investor_leadsource;
        $Data['Radio'] = $Radio_leadsource;
        $Data['JV_Partner'] = $JVPartner_leadsource;
        $Data['Banded_Sign'] = $BandedSign_leadsource;
        return json_encode($Data);
    }

    public function GetDataSourceAnalytics(Request $request)
    {
        $Type = $request['type'];
        $firstdate = "";
        $lastdate = "";

        if($Type == "Recent Week") {
          $firstdate = Carbon::now()->startOfWeek();
          $lastdate = Carbon::now()->endOfWeek();
        }
        elseif ($Type == "Recent Month")
        {
          $firstdate = Carbon::now()->startOfMonth();
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Quarter")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(2);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Semester")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(5);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Year")
        {
          $firstdate = Carbon::now()->startOfYear();
          $lastdate = Carbon::now()->endOfYear();
        }
        elseif($Type == "All Time")
        {
          $firstdate = "";
          $lastdate = "";
        }
        elseif($Type == "Range")
        {
          $firstdate = $request['StartDate'];
          $lastdate = $request['EndDate'];
        }

        // Lead Data Source Record
        $OnMarket_datasource = 0;
        $Vacant_datasource = 0;
        $Liens_datasource = 0;
        $PreForeclosures_datasource = 0;
        $Auctions_datasource = 0;
        $BankOwned_datasource = 0;
        $CashBuyers_datasource = 0;
        $HighEquity_datasource = 0;
        $FreeClear_datasource = 0;
        $Bankruptcy_datasource = 0;
        $Divorce_datasource = 0;
        $TaxDelinquencies_datasource = 0;
        $Flippers_datasource = 0;
        $FailedListings_datasource = 0;
        $SeniorOwners_datasource = 0;
        $VacantLand_datasource = 0;
        $TiredLandlords_datasource = 0;
        $PreProbate_datasource = 0;
        $Others_datasource = 0;

        $leads = DB::table('leads')
            ->where('leads.deleted_at', '=', null)
            ->where(function ($query) use ($firstdate, $lastdate) {
              if ($firstdate != "" && $lastdate != "") {
                  $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
              }
            })
            ->get();

        foreach ($leads as $lead) {
          $LeadDataSource = explode(",", $lead->data_source);
          // Data Source
          if (in_array("On Market", $LeadDataSource)) {
              $OnMarket_datasource++;
          } if (in_array("Vacant", $LeadDataSource)) {
              $Vacant_datasource++;
          } if (in_array("Liens", $LeadDataSource)) {
              $Liens_datasource++;
          } if (in_array("Pre-Foreclosures", $LeadDataSource)) {
              $PreForeclosures_datasource++;
          } if (in_array("Auctions", $LeadDataSource)) {
              $Auctions_datasource++;
          } if (in_array("Bank Owned", $LeadDataSource)) {
              $BankOwned_datasource++;
          } if (in_array("Cash Buyers", $LeadDataSource)) {
              $CashBuyers_datasource++;
          } if (in_array("High Equity", $LeadDataSource)) {
              $HighEquity_datasource++;
          } if (in_array("Free & Clear", $LeadDataSource)) {
              $FreeClear_datasource++;
          } if (in_array("Bankruptcy", $LeadDataSource)) {
              $Bankruptcy_datasource++;
          } if (in_array("Divorce", $LeadDataSource)) {
              $Divorce_datasource++;
          } if (in_array("Tax Delinquencies", $LeadDataSource)) {
              $TaxDelinquencies_datasource++;
          } if (in_array("Flippers", $LeadDataSource)) {
              $Flippers_datasource++;
          } if (in_array("Failed Listings", $LeadDataSource)) {
              $FailedListings_datasource++;
          } if (in_array("Senior Owners", $LeadDataSource)) {
              $SeniorOwners_datasource++;
          } if (in_array("Vacant Land", $LeadDataSource)) {
              $VacantLand_datasource++;
          } if (in_array("Tired Landlords", $LeadDataSource)) {
              $TiredLandlords_datasource++;
          } if (in_array("Pre-Probate (Deceased Owner)", $LeadDataSource)) {
              $PreProbate_datasource++;
          } if (in_array("Others", $LeadDataSource)) {
              $Others_datasource++;
          }
        }

        $Data['OnMarket'] = $OnMarket_datasource;
        $Data['Vacant'] = $Vacant_datasource;
        $Data['Liens'] = $Liens_datasource;
        $Data['PreForeclosures'] = $PreForeclosures_datasource;
        $Data['Auctions'] = $Auctions_datasource;
        $Data['BankOwned'] = $BankOwned_datasource;
        $Data['CashBuyers'] = $CashBuyers_datasource;
        $Data['HighEquity'] = $HighEquity_datasource;
        $Data['FreeClear'] = $FreeClear_datasource;
        $Data['Bankruptcy'] = $Bankruptcy_datasource;
        $Data['Divorce'] = $Divorce_datasource;
        $Data['TaxDelinquencies'] = $TaxDelinquencies_datasource;
        $Data['Flippers'] = $Flippers_datasource;
        $Data['FailedListings'] = $FailedListings_datasource;
        $Data['SeniorOwners'] = $SeniorOwners_datasource;
        $Data['VacantLand'] = $VacantLand_datasource;
        $Data['TiredLandlords'] = $TiredLandlords_datasource;
        $Data['PreProbate'] = $PreProbate_datasource;
        $Data['Others'] = $Others_datasource;

        return json_encode($Data);
    }

    public function GetLeadStatusAnalytics(Request $request)
    {
        $Type = $request['type'];
        $firstdate = "";
        $lastdate = "";

        if($Type == "Recent Week") {
          $firstdate = Carbon::now()->startOfWeek();
          $lastdate = Carbon::now()->endOfWeek();
        }
        elseif ($Type == "Recent Month")
        {
          $firstdate = Carbon::now()->startOfMonth();
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Quarter")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(2);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Semester")
        {
          $first_day_of_the_current_month = Carbon::now()->startOfMonth();
          $firstdate = $first_day_of_the_current_month->copy()->subMonths(5);
          $lastdate = Carbon::now()->endOfMonth();
        }
        elseif($Type == "Recent Year")
        {
          $firstdate = Carbon::now()->startOfYear();
          $lastdate = Carbon::now()->endOfYear();
        }
        elseif($Type == "All Time")
        {
          $firstdate = "";
          $lastdate = "";
        }
        elseif($Type == "Range")
        {
          $firstdate = $request['StartDate'];
          $lastdate = $request['EndDate'];
        }

        // Lead Data Source Record
        $ClosedWon               = 0;
        $Interested              = 0;
        $NotInterested           = 0;
        $LeadIn                  = 0;
        $DoNotCall               = 0;
        $NoAnswer                = 0;
        $FollowUp                = 0;
        $OfferNotGiven           = 0;
        $OfferNotAccepted        = 0;
        $Accepted                = 0;
        $NegotiatingWithSeller   = 0;
        $AgreementSent           = 0;
        $AgreementReceived       = 0;
        $SendToInvestor          = 0;
        $NegotiationWithInvestor = 0;
        $SendToTitle             = 0;
        $SendContractToInvestor  = 0;
        $EMDReceived             = 0;
        $EMDNotReceived          = 0;
        $Inspection              = 0;
        $CloseOn                 = 0;
        $DealLost                = 0;
        $WrongNumber             = 0;

        $leads = DB::table('leads')
            ->where('leads.deleted_at', '=', null)
            ->where(function ($query) use ($firstdate, $lastdate) {
              if ($firstdate != "" && $lastdate != "") {
                  $query->whereBetween('leads.created_at', [Carbon::parse(Carbon::parse($firstdate)->format('Y-m-d') . ' 00:00:00')->format('Y-m-d H:i:s'), Carbon::parse($lastdate)->addDays(1)->format('Y-m-d H:i:s')]);
              }
            })
            ->get();

        foreach ($leads as $lead) {
          // Lead Closed Won/Interested
          if ($lead->lead_status == 1) {
              $Interested++;
          } elseif ($lead->lead_status == 21) {
              $ClosedWon++;
          } elseif ($lead->lead_status == 2) {
              $NotInterested++;
          } elseif ($lead->lead_status == 3) {
              $LeadIn++;
          } elseif ($lead->lead_status == 4) {
              $DoNotCall++;
          } elseif ($lead->lead_status == 5) {
              $NoAnswer++;
          } elseif ($lead->lead_status == 6) {
              $FollowUp++;
          } elseif ($lead->lead_status == 7) {
              $OfferNotGiven++;
          } elseif ($lead->lead_status == 8) {
              $OfferNotAccepted++;
          } elseif ($lead->lead_status == 9) {
              $Accepted++;
          } elseif ($lead->lead_status == 10) {
              $NegotiatingWithSeller++;
          } elseif ($lead->lead_status == 11) {
              $AgreementSent++;
          } elseif ($lead->lead_status == 12) {
              $AgreementReceived++;
          } elseif ($lead->lead_status == 13) {
              $SendToInvestor++;
          } elseif ($lead->lead_status == 14) {
              $NegotiationWithInvestor++;
          } elseif ($lead->lead_status == 15) {
              $SendToTitle++;
          } elseif ($lead->lead_status == 16) {
              $SendContractToInvestor++;
          } elseif ($lead->lead_status == 17) {
              $EMDReceived++;
          } elseif ($lead->lead_status == 18) {
              $EMDNotReceived++;
          } elseif ($lead->lead_status == 22) {
              $DealLost++;
          } elseif ($lead->lead_status == 23) {
              $WrongNumber++;
          } elseif ($lead->lead_status == 24) {
              $Inspection++;
          } elseif ($lead->lead_status == 25) {
              $CloseOn++;
          }
        }

        $Data['ClosedWon']       = $ClosedWon;
        $Data['Interested']      = $Interested;
        $Data['NotInterested']   = $NotInterested;
        $Data['LeadIn']          = $LeadIn;
        $Data['DoNotCall']       = $DoNotCall;
        $Data['NoAnswer']        = $NoAnswer;
        $Data['WrongNumber']     = $WrongNumber;
        $Data['FollowUp']        = $FollowUp;
        $Data['OfferNotGiven']   = $OfferNotGiven;
        $Data['OfferNotAccepted'] = $OfferNotAccepted;
        $Data['Accepted']         = $Accepted;
        $Data['NegotiatingWithSeller'] = $NegotiatingWithSeller;
        $Data['AgreementSent']    = $AgreementSent;
        $Data['AgreementReceived'] = $AgreementReceived;
        $Data['SendToInvestor']    = $SendToInvestor;
        $Data['NegotiationWithInvestor'] = $NegotiationWithInvestor;
        $Data['SendToTitle']    = $SendToTitle;
        $Data['SendContractToInvestor'] = $SendContractToInvestor;
        $Data['EMDReceived']    = $EMDReceived;
        $Data['EMDNotReceived'] = $EMDNotReceived;
        $Data['Inspection']     = $Inspection;
        $Data['CloseOn']        = $CloseOn;
        $Data['DealLost']       = $DealLost;

        return json_encode($Data);
    }
}
