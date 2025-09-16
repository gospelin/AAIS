<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('student_class_history', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        //     $table->unsignedBigInteger('session_id')->default(1);
        //     $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
        //     $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->enum('start_term', ['First', 'Second', 'Third'])->default('First');
        //     $table->enum('end_term', ['First', 'Second', 'Third'])->nullable();
        //     $table->timestamp('join_date')->useCurrent();
        //     $table->timestamp('leave_date')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->index(['student_id', 'session_id'], 'idx_student_session');
        //     $table->timestamps();
        // });

        Schema::create('student_class_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->enum('start_term', ['First', 'Second', 'Third'])->default('First');
            $table->enum('end_term', ['First', 'Second', 'Third'])->nullable();
            $table->timestamp('join_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('leave_date')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index(['student_id', 'session_id'], 'idx_student_session');
            $table->index('class_id');
            $table->index('session_id');
        });

        // Set AUTO_INCREMENT starting value
        DB::statement('ALTER TABLE student_class_history AUTO_INCREMENT = 245');
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_history');
    }
};
