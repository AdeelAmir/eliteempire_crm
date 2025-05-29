<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingRoom extends Model
{
    protected $fillable = [
        'role_id',
        'folder_id',
        'order_no',
        'type',
        'title',
        'video_url',
        'article_details',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
