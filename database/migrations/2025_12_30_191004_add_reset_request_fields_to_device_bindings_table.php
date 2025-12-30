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
        Schema::table('device_bindings', function (Blueprint $table) {
            $table->timestamp('reset_requested_at')->nullable()->after('last_used_at');
            $table->text('reset_request_reason')->nullable()->after('reset_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_bindings', function (Blueprint $table) {
            $table->dropColumn(['reset_requested_at', 'reset_request_reason']);
        });
    }
};

