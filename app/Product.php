<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = [
      'name',
      'created_at',
      'updated_at',
      'deleted_at',
  ];
}
