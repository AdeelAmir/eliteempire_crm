<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServingLocation extends Model
{
  protected $fillable =[
      'user_id',
      'property_classification',
      'property_type',
      'multi_family',
      'construction_type',
      'state',
      'city',
      'county',
      'zipcode',
      'created_at',
      'updated_at',
  ];
}
