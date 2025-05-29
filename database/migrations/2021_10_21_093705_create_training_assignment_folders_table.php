<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingAssignmentFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('training_assignment_folders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('folder_id');
            $table->double('completion_rate');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_assignment_folders');
    }
}
