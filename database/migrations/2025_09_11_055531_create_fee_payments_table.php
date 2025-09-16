<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('fee_payments', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        //     $table->unsignedBigInteger('session_id')->default(1);
        //     $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
        //     $table->enum('term', ['First', 'Second', 'Third'])->nullable();
        //     $table->boolean('has_paid_fee')->default(false);
        //     $table->timestamps();
        // });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->enum('term', ['First', 'Second', 'Third'])->nullable();
            $table->boolean('has_paid_fee')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('session_id');
            $table->index('student_id');
        });

        // Set AUTO_INCREMENT starting value
        DB::statement('ALTER TABLE fee_payments AUTO_INCREMENT = 247');
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
