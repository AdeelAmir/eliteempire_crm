<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadAnnouncement extends Model
{
    protected $fillable = [
      'announcement_id',
      'user_id',
      'created_at',
      'updated_at'
    ];
}
