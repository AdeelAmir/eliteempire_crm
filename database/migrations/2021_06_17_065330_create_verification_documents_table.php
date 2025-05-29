<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('document_name')->nullable();
            $table->string('document_number')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('verification_documents');
    }
}
