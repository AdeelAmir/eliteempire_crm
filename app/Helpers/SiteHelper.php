<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Sabberworm\CSS\Value\Size;

class SiteHelper
{
    public static function settings()
    {
        $Settings = array();
        $Settings['PrimaryColor'] = '#00007B'; // Blue
        return $Settings;
    }

    public static function ConvertPhoneNumberFormat($Phone)
    {
        $Phone = str_replace("-", "", $Phone);
        $Phone = substr_replace($Phone, "-", 3, 0);
        $Phone = substr_replace($Phone, "-", 7, 0);
        return $Phone;
    }

    public static function GetLeadStatus($Status)
    {
        if ($Status == 1) {
            return "Confirm";
        } elseif ($Status == 2) {
            return "Cancelled";
        } elseif ($Status == 3) {
            return "Pending";
        } elseif ($Status == 4) {
            return "Approve Sales";
        } elseif ($Status == 5) {
            return "Bank Turn Down";
        } elseif ($Status == 6) {
            return "Out of Coverage Area";
        } elseif ($Status == 7) {
            return "Not Interested";
        } elseif ($Status == 8) {
            return "Demo";
        } elseif ($Status == 9) {
            return "1 Legger";
        } elseif ($Status == 10) {
            return "Not Home";
        } elseif ($Status == 11) {
            return "Pending Sales";
        } else {
            return '';
        }
    }

    public static function GetLeadStatusColor($lead_status)
    {
        if ($lead_status == 1) {
            return '<span class="badge badge-success">Confirm</span>';
        } elseif ($lead_status == 2) {
            return '<span class="badge badge-danger">Cancelled</span>';
        } elseif ($lead_status == 3) {
            return '<span class="badge badge-warning">Pending</span>';
        } elseif ($lead_status == 4) {
            return '<span class="badge badge-primary">Approve Sales</span>';
        } elseif ($lead_status == 5) {
            return '<span class="badge badge-warning" style="background-color:pink;color:white;">Bank Turn Down</span>';
        } elseif ($lead_status == 6) {
            return '<span class="badge badge-warning" style="background-color:orange;">Out of coverage area</span>';
        } elseif ($lead_status == 7) {
            return '<span class="badge badge-secondary">Not interested</span>';
        } elseif ($lead_status == 8) {
            return '<span class="badge badge-success">Demo</span>';
        } elseif ($lead_status == 9) {
            return '<span class="badge badge-success">1 Legger</span>';
        } elseif ($lead_status == 10) {
            return '<span class="badge badge-success">Not Home</span>';
        } elseif ($lead_status == 11) {
            return '<span class="badge badge-success">Pending Sales</span>';
        }
    }

    static function GetLeadLastNote($LeadId)
    {
        $Note = DB::table('history_notes')
            ->where('lead_id', '=', $LeadId)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if (sizeof($Note) > 0) {
            return $Note[0]->history_note;
        } else {
            return '';
        }
    }

    static function GetCityFromZipCode($ZipCode)
    {
      $City = null;
      $LocationsSql = "SELECT * FROM locations WHERE ((FIND_IN_SET(:zipcode, zipcode) > 0));";
      $Location = DB::select(DB::raw($LocationsSql), array($ZipCode));
      foreach ($Location as $item) {
          $City = $item->city;
      }
      return $City;
    }

    static function GetNewOrderNumber($RoleId, $FolderId)
    {
        $ordersArray = array();
        $Assignment = DB::table('training_rooms')
            ->where('role_id', '=', $RoleId)
            ->where('folder_id', '=', $FolderId)
            ->where('deleted_at', '=', null)
            ->get();
        if (sizeof($Assignment) > 0) {
            foreach ($Assignment as $assgmnt) {
                array_push($ordersArray, intval($assgmnt->order_no));
            }
            return max($ordersArray) + 1;
        } else {
            return 1;
        }
    }

    static function GetNewFolderOrderNumber($RoleId)
    {
        $ordersArray = array();
        $Assignment = DB::table('folders')
            ->where('role_id', '=', $RoleId)
            ->where('deleted_at', '=', null)
            ->get();
        if (sizeof($Assignment) > 0) {
            foreach ($Assignment as $assgmnt) {
                array_push($ordersArray, intval($assgmnt->order_no));
            }
            return max($ordersArray) + 1;
        } else {
            return 1;
        }
    }

    static function GetCurrentUserState()
    {
        $Profile = DB::table('profiles')->where('user_id', '=', \Illuminate\Support\Facades\Auth::id())->get();
        return $Profile[0]->state;
    }

    static function GetUserState($UserId)
    {
        $State = "";
        $UserDetails = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.deleted_at', '=', null)
            ->where('users.id', '=', $UserId)
            ->select('profiles.state')
            ->get();

        return $UserDetails[0]->state;
    }

    static function GetConstantValue($Name){
        $Constants = DB::table('constants')
            ->get();
        if ($Name == 'ARV_SALES_CLOSING_COST_CONSTANT') {
            return floatval($Constants[0]->value);
        } elseif ($Name == 'WHOLESALES_CLOSING_COST_CONSTANT') {
            return floatval($Constants[1]->value);
        } elseif ($Name == 'INVESTOR_PROFIT_CONSTANT') {
            return floatval($Constants[2]->value);
        } elseif ($Name == 'OFFER_LOWER_RANGE_CONSTANT') {
            return floatval($Constants[3]->value);
        } elseif ($Name == 'OFFER_HIGHER_RANGE_CONSTANT') {
            return floatval($Constants[4]->value);
        } else {
            return 0;
        }
    }
}
