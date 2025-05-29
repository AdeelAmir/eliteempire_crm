<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create('training_quizzes', function (Blueprint $table) {
            $table->id();
            $table->integer('topic_id');
            $table->text('question')->nullable();
            $table->text('choice1')->nullable();
            $table->text('choice2')->nullable();
            $table->text('choice3')->nullable();
            $table->text('choice4')->nullable();
            $table->integer('answer');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_quizzes');
    }
}
