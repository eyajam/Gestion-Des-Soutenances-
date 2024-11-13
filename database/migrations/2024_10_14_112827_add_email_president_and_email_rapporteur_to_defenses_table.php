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
        Schema::table('defenses', function (Blueprint $table) {
            $table->string('email_president')->nullable()->after('email_encadrant');  // Add email_president column
            $table->string('email_rapporteur')->nullable()->after('email_president'); // Add email_rapporteur column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropColumn('email_president');
            $table->dropColumn('email_rapporteur');
        });
    }
};
