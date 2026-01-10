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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., JazzCash, EasyPaisa, Stripe, PayPal
            $table->string('slug')->unique();
            $table->string('code')->unique(); // e.g., jazzcash, easypaisa, stripe
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon/image path
            $table->json('credentials')->nullable(); // Store API keys, merchant IDs, etc. as JSON
            $table->json('config')->nullable(); // Additional configuration (sandbox mode, etc.)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(true); // Test mode
            $table->integer('order')->default(0);
            $table->decimal('transaction_fee_percentage', 5, 2)->default(0); // Transaction fee %
            $table->decimal('transaction_fee_fixed', 10, 2)->default(0); // Fixed transaction fee
            $table->text('instructions')->nullable(); // Setup instructions
            $table->timestamps();
            
            $table->index(['is_active', 'order']);
        });

        // Insert default payment methods
        DB::table('payment_methods')->insert([
            [
                'name' => 'JazzCash',
                'slug' => 'jazzcash',
                'code' => 'jazzcash',
                'description' => 'JazzCash payment gateway integration',
                'is_active' => true,
                'is_sandbox' => true,
                'order' => 1,
                'credentials' => json_encode([
                    'merchant_id' => '',
                    'password' => '',
                    'integrity_salt' => '',
                ]),
                'config' => json_encode([
                    'sandbox_url' => 'https://sandbox.jazzcash.com.pk',
                    'production_url' => 'https://payments.jazzcash.com.pk',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'EasyPaisa',
                'slug' => 'easypaisa',
                'code' => 'easypaisa',
                'description' => 'EasyPaisa payment gateway integration',
                'is_active' => true,
                'is_sandbox' => true,
                'order' => 2,
                'credentials' => json_encode([
                    'merchant_id' => '',
                    'password' => '',
                    'store_id' => '',
                ]),
                'config' => json_encode([
                    'sandbox_url' => 'https://easypay.easypaisa.com.pk',
                    'production_url' => 'https://easypay.easypaisa.com.pk',
                ]),
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
        Schema::dropIfExists('payment_methods');
    }
};
