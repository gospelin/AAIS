<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_session_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->unsignedBigInteger('session_id')->default(1);
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->enum('current_term', ['First', 'Second', 'Third'])->default('First');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_session_preferences');
    }
};
