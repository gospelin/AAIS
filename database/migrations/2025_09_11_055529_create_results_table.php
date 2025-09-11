<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('session_id')->default(1); 
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->enum('term', ['First', 'Second', 'Third'])->default('Third');
            $table->integer('class_assessment')->nullable();
            $table->integer('summative_test')->nullable();
            $table->integer('exam')->nullable();
            $table->integer('total')->nullable();
            $table->string('grade', 5)->nullable();
            $table->string('remark', 100)->nullable();
            $table->unique(['student_id', 'subject_id', 'class_id', 'term', 'session_id'], 'unique_result');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
