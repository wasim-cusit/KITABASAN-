<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_notification_settings', function (Blueprint $table) {
            $table->boolean('email_device_bindings')->default(true)->after('email_course_updates');
            $table->boolean('email_device_reset_requests')->default(true)->after('email_device_bindings');
        });
    }

    public function down(): void
    {
        Schema::table('admin_notification_settings', function (Blueprint $table) {
            $table->dropColumn(['email_device_bindings', 'email_device_reset_requests']);
        });
    }
};

