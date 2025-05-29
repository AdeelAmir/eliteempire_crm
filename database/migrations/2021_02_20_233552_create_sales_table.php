<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id');
            $table->integer('lead_number');
            $table->string('sale_type')->comment('Approve Sale/Bank Turn Down');
            $table->double('contract_amount');
            $table->date('contract_date');
            $table->integer('product');
            $table->double('net_profit');
            $table->double('net_profit_amount');
            $table->date('sale_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
