<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarningDsTable extends Migration
{
    public function up()
    {
        Schema::create('earning_ds', function (Blueprint $table) {
            $table->id();
            $table->integer('earning_id');
            $table->integer('lead_id')->nullable();
            $table->integer('sale_id')->nullable();
            $table->string('lead_number');
            $table->string('payout_type');
            $table->double('earning');
            $table->double('bonus')->nullable();
            $table->integer('approve_status')->nullable();
            $table->date('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('earning_ds');
    }
}