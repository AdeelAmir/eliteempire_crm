<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id');
            $table->integer('user_id');
            $table->integer('status')->default(0)->comment('0 = Incomplete, 1 = Complete');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_assignments');
    }
}
