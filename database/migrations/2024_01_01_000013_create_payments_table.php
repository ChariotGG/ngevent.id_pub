<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Xendit Data
            $table->string('xendit_invoice_id')->nullable()->unique();
            $table->string('xendit_invoice_url', 500)->nullable();
            $table->string('xendit_external_id')->nullable();
            
            // Payment Info
            $table->unsignedInteger('amount');
            $table->string('payment_method')->nullable(); // CREDIT_CARD, BANK_TRANSFER, EWALLET, QR_CODE
            $table->string('payment_channel')->nullable(); // BCA, BNI, OVO, DANA, etc
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, paid, expired, failed, refunded
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Additional Xendit Data
            $table->unsignedInteger('paid_amount')->nullable();
            $table->unsignedInteger('adjusted_received_amount')->nullable();
            $table->unsignedInteger('fees_paid_amount')->nullable();
            
            // Raw Response
            $table->json('raw_response')->nullable();
            
            $table->timestamps();

            $table->index('xendit_invoice_id');
            $table->index('status');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
