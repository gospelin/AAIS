<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the existing unique constraint on 'name'
            $table->dropUnique(['name']);
            // Add a composite unique constraint on 'name' and 'section'
            $table->unique(['name', 'section'], 'subjects_name_section_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('subjects_name_section_unique');
            // Restore the unique constraint on 'name'
            $table->unique('name');
        });
    }
};