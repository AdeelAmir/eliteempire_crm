<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class location_coordinates extends Model
{
    protected $fillable = [
        'type_id',
        'type',
        'formatted_address',
        'lat',
        'long',
        'type_last_updated',
        'created_at',
        'updated_at'
    ];
}