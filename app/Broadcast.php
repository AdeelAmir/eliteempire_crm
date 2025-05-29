<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = [
        'sender_id',
        // 'reciever_id',
        'message',
        // 'read_status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
