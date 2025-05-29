<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarningsMsTable extends Migration
{
    public function up()
    {
        Schema::create('earnings_ms', function (Blueprint $table) {
            $table->id();
            $table->integer('u_id');
            $table->double('earnings')->default(0);
            $table->double('withdrawn')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('earnings_ms');
    }
}
