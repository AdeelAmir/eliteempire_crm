<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
      'id',
      'role_id',
      'order_no',
      'name',
      'picture',
      'required',
      'created_at',
      'updated_at',
      'deleted_at'
    ];
}
