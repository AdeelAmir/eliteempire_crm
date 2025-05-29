<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCoverageLinksTable extends Migration
{
    public function up()
    {
        Schema::create('training_coverage_links', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('must be representative');
            $table->text('training_link');
            $table->text('coverage_file');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_coverage_links');
    }
}
