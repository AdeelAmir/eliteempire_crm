<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $fillable = [
        'title',
        'team_type',
        'team_manager',
        'team_supervisor',
        'members',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
