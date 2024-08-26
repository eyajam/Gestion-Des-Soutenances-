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
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->unique();
                $table->string('project_title')->nullable();
                $table->string('project_type');
                $table->string('partner')->unique();
                $table->string('company')->nullable();
                $table->string('teacher_email')->nullable();
                $table->string('specs')->nullable();
                $table->timestamps();
    
                $table->foreign('teacher_email')->references('email')->on('teachers')->onDelete('set null');
                $table->foreign('student_id')->references('user_id')->on('students')->onDelete('cascade');
            });
           
        }
    
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('projects');
        }
    };
        

