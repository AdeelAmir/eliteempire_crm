<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'type',
        'message',
        'expiration',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}