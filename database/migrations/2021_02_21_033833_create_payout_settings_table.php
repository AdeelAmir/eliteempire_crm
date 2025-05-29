<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('payout_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->string('payout_type');
            $table->double('amount')->nullable();
            $table->double('percentage')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payout_settings');
    }
}
