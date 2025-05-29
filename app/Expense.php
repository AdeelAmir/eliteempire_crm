<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
	protected $fillable = [
		'description',
		'total',
    'expense_date',
    'vendor',
    'location',
    'currency',
		'other_currency_name',
    'exchange_rate',
    'note',
		'created_at',
		'updated_at',
		'deleted_at',
	];
}
