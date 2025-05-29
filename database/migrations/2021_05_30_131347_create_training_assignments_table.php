<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('training_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('assignment_type');
            $table->string('training_assignment_folder_id');
            $table->integer('assignment_id');
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_assignments');
    }
}
