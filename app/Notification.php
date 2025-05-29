<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'lead_id',
        'sender_id',
        'reciever_id',
        'message',
        'type',
        'followup_type',
        'read_status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
