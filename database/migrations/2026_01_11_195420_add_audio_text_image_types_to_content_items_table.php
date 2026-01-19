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
        // Modify enum to add new content types: audio, text, image
        // Note: This uses DB::statement because Laravel Schema doesn't support modifying enum columns directly
        DB::statement("ALTER TABLE content_items MODIFY COLUMN content_type ENUM('video', 'audio', 'text', 'image', 'quiz', 'document', 'assignment', 'book') DEFAULT 'video'");
        
        // Add audio-specific fields
        Schema::table('content_items', function (Blueprint $table) {
            $table->string('audio_file')->nullable()->after('video_cloud_url');
            $table->string('audio_cloud_url')->nullable()->after('audio_file');
            
            // Add text/image content fields
            $table->longText('text_content')->nullable()->after('description'); // For text/HTML content
            $table->string('image_file')->nullable()->after('text_content');
            $table->string('image_cloud_url')->nullable()->after('image_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropColumn(['audio_file', 'audio_cloud_url', 'text_content', 'image_file', 'image_cloud_url']);
        });
        
        // Revert enum to original values
        DB::statement("ALTER TABLE content_items MODIFY COLUMN content_type ENUM('video', 'quiz', 'document', 'assignment') DEFAULT 'video'");
    }
};
