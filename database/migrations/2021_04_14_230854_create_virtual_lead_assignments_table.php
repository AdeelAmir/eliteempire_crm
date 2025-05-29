<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualLeadAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('virtual_lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('virtual_lead_assignments');
    }
}
