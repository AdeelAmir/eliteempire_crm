<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingQuiz extends Model
{
    protected $fillable = [
        'topic_id',
        'question',
        'choice1',
        'choice2',
        'choice3',
        'choice4',
        'answer',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}