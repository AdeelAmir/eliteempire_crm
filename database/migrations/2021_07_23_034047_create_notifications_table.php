<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id')->nullable();
            $table->integer('sender_id');
            $table->integer('reciever_id');
            $table->string('message')->nullable();
            $table->integer('type')->default(1)->comment('1- lead status notifications, 2- message notification');
            $table->integer('followup_type')->nullable()->comment('	Null- For other notifications 1- Follow up after 2 hours 2- Follow up after 10 minutes 3- Follow up time');
            $table->integer('read_status')->default(0)->comment('0- unread, 1- read');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
