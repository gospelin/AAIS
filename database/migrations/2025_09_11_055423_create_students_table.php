<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('reg_no', 50)->nullable()->unique();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('gender', 10);
            $table->date('date_of_birth')->nullable();
            $table->string('parent_name', 70)->nullable();
            $table->text('parent_phone_number')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('parent_occupation', 100)->nullable();
            $table->string('state_of_origin', 50)->nullable();
            $table->string('local_government_area', 50)->nullable();
            $table->string('religion', 50)->nullable();
            $table->timestamp('date_registered')->useCurrent();
            $table->boolean('approved')->default(false);
            $table->string('profile_pic', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
