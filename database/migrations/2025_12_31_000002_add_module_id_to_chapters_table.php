<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->after('book_id')->constrained('modules')->onDelete('cascade');
        });

        // Add is_preview column if it doesn't exist
        if (!Schema::hasColumn('chapters', 'is_preview')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->boolean('is_preview')->default(false)->after('is_free');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });

        if (Schema::hasColumn('chapters', 'is_preview')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropColumn('is_preview');
            });
        }
    }
};
