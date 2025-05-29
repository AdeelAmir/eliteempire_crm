<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationDocuments extends Model
{
    protected $fillable = [
        'user_id',
        'document_name',
        'document_number',
        'document',
        'created_at',
        'updated_at'
    ];
}