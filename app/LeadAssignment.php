<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadAssignment extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'status',
        'created_at',
        'updated_at'
    ];
}