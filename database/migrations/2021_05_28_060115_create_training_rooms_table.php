<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('training_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('folder_id');
            $table->integer('order_no')->default(0)->nullable();
            $table->string('type')->comment("video, article, quiz");
            $table->string('title')->nullable();
            $table->text('video_url')->nullable();
            $table->text('article_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_rooms');
    }
}
