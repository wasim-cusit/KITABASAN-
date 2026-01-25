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
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->boolean('email_notifications')->default(true)->after('status')->comment('Notify on new enrollments');
            $table->boolean('course_updates')->default(true)->after('email_notifications')->comment('Notify on course changes');
            $table->boolean('show_profile')->default(true)->after('course_updates')->comment('Show profile to students');
            $table->boolean('show_email')->default(false)->after('show_profile')->comment('Show email to students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropColumn(['email_notifications', 'course_updates', 'show_profile', 'show_email']);
        });
    }
};
