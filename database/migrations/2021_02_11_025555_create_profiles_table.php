<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('parent_id')->nullable();
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->date('dob');
            $table->string('phone');
            $table->string('phone2')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('state');
            $table->string('zipcode')->nullable();
            $table->string('identity1')->nullable();
            $table->string('identity2')->nullable();
            $table->string('document_name')->nullable();
            $table->integer('document_numbers')->nullable();
            $table->string('buisnesss_name')->nullable();
            $table->string('buisness_address')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('property_classification')->nullable();
            $table->string('property_type')->nullable();
            $table->string('multi_family')->nullable();
            $table->string('construction_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
