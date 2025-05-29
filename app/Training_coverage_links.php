<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training_coverage_links extends Model
{
    protected $fillable = [
        'user_id',
        'training_link',
        'coverage_file',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}