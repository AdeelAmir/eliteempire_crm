<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable =[
        'hours_price',
        'created_at',
        'updated_at'
    ];
}