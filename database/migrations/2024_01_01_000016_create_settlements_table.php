<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('organizer_id')->constrained();
            
            // Financial Summary
            $table->unsignedInteger('gross_amount')->default(0); // Total from ticket sales
            $table->unsignedInteger('platform_fee')->default(0); // Platform fee deducted
            $table->unsignedInteger('payment_fee_total')->default(0); // Total payment gateway fees
            $table->unsignedInteger('refund_amount')->default(0); // Total refunds
            $table->unsignedInteger('net_amount')->default(0); // Amount to transfer
            
            // Bank Details (snapshot from organizer)
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name');
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, processing, transferred, failed
            
            // Transfer Info
            $table->string('transfer_reference')->nullable();
            $table->string('transfer_proof')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();

            $table->index('event_id');
            $table->index('organizer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
