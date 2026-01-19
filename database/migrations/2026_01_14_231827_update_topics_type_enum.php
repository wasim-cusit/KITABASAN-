<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the type ENUM to include 'quiz' and 'mcq'
        DB::statement("ALTER TABLE topics MODIFY COLUMN type ENUM('lecture', 'topic', 'quiz', 'mcq') DEFAULT 'lecture'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE topics MODIFY COLUMN type ENUM('lecture', 'topic') DEFAULT 'lecture'");
    }
};
