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
        Schema::table('teachers', function (Blueprint $table) {
            // Step 1: Add the new column 'user_id'
            $table->unsignedBigInteger('user_id')->nullable(); // Temporarily nullable for data migration
        });

        // Step 2: Migrate the data from 'student_id' to 'user_id'
        DB::statement('UPDATE teachers SET user_id = teacher_id');

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            // Step 3: Drop the old 'student_id' column
            $table->dropColumn('teacher_id');

            // Step 4: Make 'user_id' non-nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Step 5: Add the foreign key constraint for 'user_id'
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop the foreign key on 'user_id'
            $table->dropForeign(['user_id']);

            // Add back the old 'student_id' column
            $table->unsignedBigInteger('teacher_id')->nullable();

            // Migrate the data back from 'user_id' to 'student_id'
            DB::statement('UPDATE teachers SET teacher_id = user_id');

            // Drop the 'user_id' column
            $table->dropColumn('user_id');

            // Re-add the foreign key constraint for 'student_id'
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
