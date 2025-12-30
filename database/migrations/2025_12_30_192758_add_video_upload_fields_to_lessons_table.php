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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('video_file')->nullable()->after('video_id'); // Path to uploaded video file
            $table->bigInteger('video_size')->nullable()->after('video_file'); // File size in bytes
            $table->string('video_mime_type')->nullable()->after('video_size'); // MIME type (video/mp4, etc.)
            $table->enum('video_host', ['youtube', 'bunny', 'upload'])->nullable()->change(); // Add 'upload' option
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['video_file', 'video_size', 'video_mime_type']);
            $table->enum('video_host', ['youtube', 'bunny'])->nullable()->change();
        });
    }
};
