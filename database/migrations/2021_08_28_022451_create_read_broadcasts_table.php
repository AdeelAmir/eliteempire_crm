<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadBroadcastsTable extends Migration
{
    public function up()
    {
        Schema::create('read_broadcasts', function (Blueprint $table) {
          $table->id();
          $table->integer('broadcast_id');
          $table->integer('reciever_id');
          $table->integer('read_status')->default(0)->comment('0- unread, 1- read');
          $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('read_broadcasts');
    }
}
