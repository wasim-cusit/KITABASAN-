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
        Schema::table('quizzes', function (Blueprint $table) {
            // Update passing_score default to 70
            $table->integer('passing_score')->default(70)->change();
            
            // Add new columns
            if (!Schema::hasColumn('quizzes', 'questions_json')) {
                $table->json('questions_json')->nullable()->after('description');
            }
            if (!Schema::hasColumn('quizzes', 'is_preview')) {
                $table->boolean('is_preview')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('passing_score')->default(60)->change();
            $table->dropColumn(['questions_json', 'is_preview']);
        });
    }
};
