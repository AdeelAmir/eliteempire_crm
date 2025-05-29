<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id')->nullable()->comment('Id if group message, NULL otherwise');
            $table->integer('sender_id');
            $table->integer('receiver_id')->nullable();
            $table->text('message')->nullable();
            $table->string('read_status')->default('unread')->comment('read,unread');
            $table->string('read_by')->nullable();
            $table->string('time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
