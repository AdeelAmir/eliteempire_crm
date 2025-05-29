<?php

namespace App\Imports;

use App\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeadsImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $FirstName  = $row[0];
        $LastName   = $row[1];
        $Street     = $row[2];
        $City       = $row[3];
        $State      = $this->GetStateName($row[4]);
        $ZipCode    = $row[5];
        $Phone1     = $row[7];
        $Phone2     = $row[11];
        $Phone3     = $row[15];
        $Phone4     = $row[19];
        $Phone5     = $row[23];
        $LeadNumber = rand(1000000, 9999999);
        $user_id    = Auth::id();
        $LeadType   = 1;
        $LeadStatus = 3;
        $IsDuplicated = 0;

        // Check Lead Is Same Or Not
        $CheckLeadMatchedStatus = $this->CheckLeadIsSameOrNot($FirstName, $LastName, $Phone1, $Street, $ZipCode);
        if ($CheckLeadMatchedStatus == 0) {
          // Check Lead Duplication
          $IsDuplicated = $this->CheckLeadIsDuplicated($Phone1, $Phone2, $Phone3, $Phone4, $Phone5);

          // Get duplicated lead Number
          if ($IsDuplicated == 1) {
            $LeadNumber = $this->GetDuplicatedLeadNumber($Phone1, $Phone2, $Phone3, $Phone4, $Phone5);
          }

          return new Lead([
              'user_id'     => $user_id,
              'lead_number' => $LeadNumber,
              'firstname'   => $FirstName,
              'lastname'    => $LastName,
              'street'      => $Street,
              'city'        => $City,
              'state'       => $State,
              'zipcode'     => $ZipCode,
              'phone'       => $Phone1,
              'phone2'      => $Phone2,
              'phone3'      => $Phone3,
              'phone4'      => $Phone4,
              'phone5'      => $Phone5,
              'lead_type'   => $LeadType,
              'lead_status' => $LeadStatus,
              'created_at'  => Carbon::now()
          ]);
        }
    }

    public function GetStateName($state)
    {
        $State = null;
        $Check = DB::table('locations')
            ->where('state_id', '=', $state)
            ->limit(1)
            ->get();

        if (sizeof($Check) > 0) {
            return $Check[0]->state_name;
        } else {
            return $State;
        }
    }

    public function CheckLeadIsSameOrNot($FirstName, $LastName, $Phone1, $Street, $ZipCode) {
      $Check = DB::table('leads')
          ->where('deleted_at', '=', null)
          ->where(function ($query) use ($FirstName, $LastName, $Phone1, $Street, $ZipCode) {
              if (ucwords(strtolower($FirstName)) != null) {
                  $query->where('leads.firstname', '=', ucwords(strtolower($FirstName)));
              }
              if (ucwords(strtolower($LastName)) != null) {
                  $query->where('leads.lastname', '=', ucwords(strtolower($LastName)));
              }
              if ($Phone1 != null) {
                  $query->where('leads.phone', '=', $Phone1);
              }
              if ($Street != null) {
                  $query->where('leads.street', '=', $Street);
              }
              if ($ZipCode != null) {
                  $query->where('leads.zipcode', '=', $ZipCode);
              }
          })
          ->count();

      return $Check;
    }

    public function CheckLeadIsDuplicated($Phone1, $Phone2, $Phone3, $Phone4, $Phone5)
    {
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
            return 1;
        } else {
            return 0;
        }
    }

    public function GetDuplicatedLeadNumber($Phone1, $Phone2, $Phone3, $Phone4, $Phone5)
    {
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

        return $Check[0]->lead_number;
    }
}
