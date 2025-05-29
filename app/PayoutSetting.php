<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutSetting extends Model
{
  protected $fillable = [
    'role_id',
    'payout_type',
    'amount',
    'percentage',
    'created_at',
    'updated_at',
    'deleted_at',
  ];
}
