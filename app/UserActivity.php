<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'message',
        'created_at',
        'updated_at'
    ];
}
