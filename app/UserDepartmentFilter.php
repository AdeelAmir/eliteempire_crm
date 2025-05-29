<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDepartmentFilter extends Model
{
    protected $fillable = [
        'user_id',
        'lead_status',
        'state',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
