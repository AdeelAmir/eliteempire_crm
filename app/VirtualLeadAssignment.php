<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VirtualLeadAssignment extends Model
{
  protected $fillable = [
      'lead_id',
      'user_id',
      'created_at',
      'updated_at'
  ];
}
