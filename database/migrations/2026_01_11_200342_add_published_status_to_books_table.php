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
        // Modify enum to add 'published' status
        // Note: This uses DB::statement because Laravel Schema doesn't support modifying enum columns directly
        DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'approved', 'rejected') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original values (without 'published')
        DB::statement("ALTER TABLE books MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft'");
    }
};
