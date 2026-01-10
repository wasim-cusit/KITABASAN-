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
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name'); // Human-readable name
            $table->text('value')->nullable(); // Store value as JSON or text
            $table->enum('type', ['color', 'image', 'text', 'number', 'boolean', 'json'])->default('text');
            $table->string('group')->default('general'); // Group settings (general, layout, branding, etc.)
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default theme settings
        DB::table('theme_settings')->insert([
            [
                'key' => 'primary_color',
                'name' => 'Primary Color',
                'value' => '#3B82F6',
                'type' => 'color',
                'group' => 'branding',
                'description' => 'Main brand color for buttons and links',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'secondary_color',
                'name' => 'Secondary Color',
                'value' => '#10B981',
                'type' => 'color',
                'group' => 'branding',
                'description' => 'Secondary brand color',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'logo',
                'name' => 'Logo',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
                'description' => 'Main website logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'favicon',
                'name' => 'Favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
                'description' => 'Website favicon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'course_layout',
                'name' => 'Course Layout',
                'value' => 'grid',
                'type' => 'text',
                'group' => 'layout',
                'description' => 'Course listing layout (grid or list)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'items_per_page',
                'name' => 'Items Per Page',
                'value' => '12',
                'type' => 'number',
                'group' => 'layout',
                'description' => 'Number of items to show per page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
