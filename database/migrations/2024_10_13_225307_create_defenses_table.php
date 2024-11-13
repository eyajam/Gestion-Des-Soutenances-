<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('classroom');
            $table->string('cins'); 
            $table->string('students');
            $table->string('title');
            $table->string('emails');
            $table->string('specialty');
            $table->string('email_encadrant'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defenses');
    }
};
