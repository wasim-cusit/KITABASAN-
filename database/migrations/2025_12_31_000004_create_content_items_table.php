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
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->enum('content_type', ['video', 'quiz', 'document', 'assignment'])->default('video');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Video fields
            $table->string('video_id')->nullable(); // YouTube ID or uploaded video path
            $table->enum('video_provider', ['youtube', 'vimeo', 'upload', 'bunny'])->nullable();
            $table->enum('youtube_privacy', ['public', 'unlisted', 'private'])->nullable();
            $table->string('video_file')->nullable(); // Path to uploaded video
            $table->string('video_cloud_url')->nullable(); // Cloud storage URL (S3, Cloudinary)
            $table->integer('duration')->default(0); // in seconds
            
            // Quiz fields
            $table->foreignId('quiz_id')->nullable()->constrained()->onDelete('cascade');
            
            // Document fields
            $table->string('document_file')->nullable(); // PDF, DOCX, etc.
            $table->string('document_cloud_url')->nullable();
            $table->string('document_type')->nullable(); // pdf, docx, txt, etc.
            
            // General fields
            $table->integer('order_index')->default(0);
            $table->boolean('is_preview')->default(false); // Flexible preview flag
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['lesson_id', 'order_index']);
            $table->index(['content_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
