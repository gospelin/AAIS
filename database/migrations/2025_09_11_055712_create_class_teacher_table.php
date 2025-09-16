<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            // $table->unsignedBigInteger('session_id')->default(1);
            // $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            // $table->enum('term', ['First', 'Second', 'Third']);
            $table->string('term', 50);
            $table->boolean('is_form_teacher')->default(0);
            // $table->boolean('is_form_teacher')->default(false);
            $table->primary(['class_id', 'teacher_id', 'session_id', 'term']);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_teacher');
    }
};
