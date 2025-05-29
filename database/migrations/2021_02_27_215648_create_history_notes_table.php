<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryNotesTable extends Migration
{
    public function up()
    {
        Schema::create('history_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('lead_id');
            $table->text('history_note');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_notes');
    }
}
