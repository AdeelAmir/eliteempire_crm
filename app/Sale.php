<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  protected $fillable = [
      'lead_id',
      'lead_number',
      'sale_type',
      'contract_amount',
      'contract_date',
      'product',
      'net_profit',
      'net_profit_amount',
      'sale_date',
      'created_at',
      'updated_at',
      'deleted_at',
  ];
}
