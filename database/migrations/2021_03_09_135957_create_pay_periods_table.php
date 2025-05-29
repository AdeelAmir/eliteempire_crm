<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('earning_d');
            $table->double('earnings');
            $table->double('bonus');
            $table->double('grossIncome');
            $table->double('tax');
            $table->double('taxAmount');
            $table->double('draw_balance');
            $table->double('net_income');
            $table->date('submitted_at')->nullable();
            $table->integer('approve_status')->nullable();
            $table->date('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_periods');
    }
}