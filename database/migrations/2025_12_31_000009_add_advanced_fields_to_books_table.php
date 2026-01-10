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
        Schema::table('books', function (Blueprint $table) {
            // Course categorization and details
            $table->enum('language', ['en', 'ur', 'ar', 'other'])->default('en')->after('description');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'all'])->default('all')->after('language');
            $table->enum('course_level', ['elementary', 'secondary', 'higher_secondary', 'undergraduate', 'graduate', 'professional'])->nullable()->after('difficulty_level');
            
            // Content and learning
            $table->json('learning_objectives')->nullable()->after('course_level');
            $table->text('prerequisites')->nullable()->after('learning_objectives');
            $table->json('tags')->nullable()->after('prerequisites');
            
            // Enrollment and access
            $table->integer('max_enrollments')->nullable()->after('enrollment_count');
            $table->date('start_date')->nullable()->after('max_enrollments');
            $table->date('end_date')->nullable()->after('start_date');
            
            // Features
            $table->boolean('certificate_enabled')->default(false)->after('end_date');
            $table->boolean('reviews_enabled')->default(true)->after('certificate_enabled');
            $table->boolean('comments_enabled')->default(true)->after('reviews_enabled');
            
            // Media
            $table->string('intro_video_url')->nullable()->after('thumbnail');
            $table->enum('intro_video_provider', ['youtube', 'vimeo', 'upload', 'bunny'])->nullable()->after('intro_video_url');
            
            // Additional metadata
            $table->text('what_you_will_learn')->nullable()->after('prerequisites');
            $table->text('course_requirements')->nullable()->after('what_you_will_learn');
            $table->text('target_audience')->nullable()->after('course_requirements');
            
            // SEO
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->json('meta_keywords')->nullable()->after('meta_description');
            
            // Visibility
            $table->boolean('is_featured')->default(false)->after('status');
            $table->boolean('is_popular')->default(false)->after('is_featured');
            $table->integer('priority_order')->default(0)->after('is_popular');
            
            // Additional fields
            $table->integer('duration_hours')->default(0)->after('total_duration'); // Total course duration in hours
            $table->integer('lectures_count')->default(0)->after('total_lessons');
            $table->integer('resources_count')->default(0)->after('lectures_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'language',
                'difficulty_level',
                'course_level',
                'learning_objectives',
                'prerequisites',
                'tags',
                'max_enrollments',
                'start_date',
                'end_date',
                'certificate_enabled',
                'reviews_enabled',
                'comments_enabled',
                'intro_video_url',
                'intro_video_provider',
                'what_you_will_learn',
                'course_requirements',
                'target_audience',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'is_featured',
                'is_popular',
                'priority_order',
                'duration_hours',
                'lectures_count',
                'resources_count',
            ]);
        });
    }
};
