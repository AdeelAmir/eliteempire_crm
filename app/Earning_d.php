<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Earning_d extends Model
{
    protected $fillable = [
        'earning_id',
        'lead_id',
        'sale_id',
        'lead_number',
        'payout_type',
        'percentage',
        'earning',
        'bonus',
        'approve_status',
        'submitted_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}