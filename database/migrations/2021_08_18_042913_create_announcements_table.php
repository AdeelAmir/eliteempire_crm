<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('1- Website, 2- CRM');
            $table->string('message')->nullable();
            $table->dateTime('expiration')->nullable();
            $table->integer('status')->default(1)->comment('1- Active, 0- Deactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}