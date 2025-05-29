<?php

namespace App\Imports;

use App\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LocationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $City      = $row['city'];
        $StateId   = $row['state_id'];
        $StateName = $row['state_name'];
        $County    = $row['county_name'];
        $ZipCode   = $row['zips'];
        if ($ZipCode != "") {
          $ZipCode = str_replace(" ",",",$ZipCode);
        }

        return new Location([
          'city' => $City,
          'state_id' => $StateId,
          'state_name' => $StateName,
          'county_name' => $County,
          'zipcode' => $ZipCode,
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now()
        ]);
    }
}
