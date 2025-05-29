<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServingLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('serving_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('property_classification')->nullable();
            $table->string('property_type')->nullable();
            $table->string('multi_family')->nullable();
            $table->string('construction_type')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('zipcode')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('serving_locations');
    }
}
