<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();

            // Customer Info (snapshot)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();

            // Pricing
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('platform_fee')->default(0);
            $table->unsignedInteger('payment_fee')->default(0);
            $table->unsignedInteger('total')->default(0);

            // Status
            $table->string('status', 20)->default('pending'); // pending, awaiting_payment, paid, completed, expired, cancelled, refunded
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('order_number');
            $table->index('user_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('expires_at');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
