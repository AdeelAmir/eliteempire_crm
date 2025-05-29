<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadBroadcast extends Model
{
    protected $fillable = [
      'broadcast_id',
      'reciever_id',
      'read_status',
      'created_at',
      'updated_at',
    ];
}
