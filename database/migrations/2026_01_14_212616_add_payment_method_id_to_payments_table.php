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
        Schema::table('payments', function (Blueprint $table) {
            // Change gateway from enum to string to support dynamic payment methods
            if (Schema::hasColumn('payments', 'gateway')) {
                // For MySQL, we need to modify the column type
                DB::statement("ALTER TABLE `payments` MODIFY `gateway` VARCHAR(50) NULL");
            }
            
            // Add payment_method_id column if it doesn't exist
            if (!Schema::hasColumn('payments', 'payment_method_id')) {
                // First check if payment_methods table exists
                if (Schema::hasTable('payment_methods')) {
                    $table->foreignId('payment_method_id')
                        ->nullable()
                        ->after('gateway')
                        ->constrained('payment_methods')
                        ->onDelete('set null');
                } else {
                    // If payment_methods table doesn't exist, add as unsigned big integer
                    $table->unsignedBigInteger('payment_method_id')->nullable()->after('gateway');
                }
            }
            
            // Add currency column if it doesn't exist
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('PKR')->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
            
            // Drop currency column if it exists
            if (Schema::hasColumn('payments', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
