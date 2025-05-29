<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Earnings_m extends Model
{
    protected $fillable = [
      'u_id',
      'earnings',
      'withdrawn',
      'created_at',
      'updated_at',
      'deleted_at',
    ];
}
