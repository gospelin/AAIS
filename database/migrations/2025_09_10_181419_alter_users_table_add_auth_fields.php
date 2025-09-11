<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('identifier')->unique()->nullable()->after('username');
            $table->string('mfa_secret')->nullable()->after('password');
            $table->boolean('active')->default(true)->after('mfa_secret');
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'identifier', 'mfa_secret', 'active']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
