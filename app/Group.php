<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
      'name',
      'picture',
      'admins',
      'members',
      'created_at',
      'updated_at',
      'deleted_at'
    ];
}
