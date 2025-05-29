<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDepartmentFiltersTable extends Migration
{
    public function up()
    {
        Schema::create('user_department_filters', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('lead_status')->nullable();
            $table->string('state')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_department_filters');
    }
}
