<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_term_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('session_id')->default(1);
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->enum('term', ['First', 'Second', 'Third']);
            $table->integer('grand_total')->nullable();
            $table->float('term_average')->nullable();
            $table->float('cumulative_average')->nullable();
            $table->float('last_term_average')->nullable();
            $table->integer('subjects_offered')->nullable();
            $table->string('position', 10)->nullable();
            $table->string('principal_remark', 100)->nullable();
            $table->string('teacher_remark', 100)->nullable();
            $table->string('next_term_begins', 100)->nullable();
            $table->string('date_issued', 100)->nullable();
            $table->unique(['student_id', 'class_id', 'term', 'session_id'], 'unique_term_summary');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_term_summaries');
    }
};
