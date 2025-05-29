<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadAnnouncementsTable extends Migration
{
    public function up()
    {
        Schema::create('read_announcements', function (Blueprint $table) {
            $table->id();
            $table->integer('announcement_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('read_announcements');
    }
}
