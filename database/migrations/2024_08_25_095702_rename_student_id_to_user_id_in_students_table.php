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
        Schema::table('students', function (Blueprint $table) {
            // Step 1: Add the new column 'user_id'
            $table->unsignedBigInteger('user_id')->nullable(); // Temporarily nullable for data migration
        });

        // Step 2: Migrate the data from 'student_id' to 'user_id'
        DB::statement('UPDATE students SET user_id = student_id');

        Schema::table('students', function (Blueprint $table) {
            
            // Step 3: Drop the old 'student_id' column
            $table->dropColumn('student_id');

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
        Schema::table('students', function (Blueprint $table) {
            // Drop the foreign key on 'user_id'
            $table->dropForeign(['user_id']);

            // Add back the old 'student_id' column
            $table->unsignedBigInteger('student_id')->nullable();

            // Migrate the data back from 'user_id' to 'student_id'
            DB::statement('UPDATE students SET student_id = user_id');

            // Drop the 'user_id' column
            $table->dropColumn('user_id');

            // Re-add the foreign key constraint for 'student_id'
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
        }
};
