<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
      'group_id',
      'sender_id',
      'receiver_id',
      'message',
      'read_status',
      'read_by',
      'time',
      'created_at',
      'updated_at',
      'deleted_at'
    ];
}
