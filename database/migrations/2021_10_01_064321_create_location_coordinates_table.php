<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationCoordinatesTable extends Migration
{
    public function up()
    {
        Schema::create('location_coordinates', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->string('type')->nullable();
            $table->string('formatted_address')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('type_last_updated')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_coordinates');
    }
}
