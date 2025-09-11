<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('session_id')->default(1);
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('start_term', ['First', 'Second', 'Third'])->default('First');
            $table->enum('end_term', ['First', 'Second', 'Third'])->nullable();
            $table->timestamp('join_date')->useCurrent();
            $table->timestamp('leave_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index(['student_id', 'session_id'], 'idx_student_session');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_history');
    }
};
