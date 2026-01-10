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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., English, Urdu, Arabic
            $table->string('code', 10)->unique(); // e.g., en, ur, ar
            $table->string('native_name')->nullable(); // Native name of the language
            $table->string('flag')->nullable(); // Flag emoji or image
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default language
            $table->integer('order')->default(0);
            $table->string('direction', 3)->default('ltr'); // ltr or rtl
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'order']);
        });

        // Insert default languages
        DB::table('languages')->insert([
            [
                'name' => 'English',
                'code' => 'en',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'is_active' => true,
                'is_default' => true,
                'order' => 1,
                'direction' => 'ltr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Urdu',
                'code' => 'ur',
                'native_name' => 'اردو',
                'flag' => '🇵🇰',
                'is_active' => true,
                'is_default' => false,
                'order' => 2,
                'direction' => 'rtl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Arabic',
                'code' => 'ar',
                'native_name' => 'العربية',
                'flag' => '🇸🇦',
                'is_active' => true,
                'is_default' => false,
                'order' => 3,
                'direction' => 'rtl',
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
        Schema::dropIfExists('languages');
    }
};
