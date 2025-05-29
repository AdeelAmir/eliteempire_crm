<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryNote extends Model
{
    protected $fillable = [
      'user_id',
      'lead_id',
      'history_note',
      'created_at',
      'updated_at',
      'deleted_at'
    ];
}
