<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('order_no');
            $table->string('name');
            $table->string('picture')->nullable();
            $table->integer('required')->comment('0- not required, 1- required');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('folders');
    }
}
