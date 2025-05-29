<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'dob',
        'phone',
        'phone2',
        'county',
        'city',
        'street',
        'state',
        'zipcode',
        'identity1',
        'identity2',
        'profile_picture',
        'document_name',
        'document_numbers',
        'buisnesss_name',
        'buisnesss_address',
        'secondary_email',
        'property_classification',
        'property_type',
        'multi_family',
        'construction_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
