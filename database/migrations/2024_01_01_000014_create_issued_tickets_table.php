<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            
            // Ticket Code
            $table->string('code', 20)->unique();
            $table->string('qr_code')->nullable(); // Path to QR code image if stored
            
            // Attendee Info
            $table->string('attendee_name')->nullable();
            $table->string('attendee_email')->nullable();
            $table->string('attendee_phone', 20)->nullable();
            
            // Check-in Status
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('check_in_notes')->nullable();
            
            $table->timestamps();

            $table->index('code');
            $table->index('order_item_id');
            $table->index('user_id');
            $table->index('is_used');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_tickets');
    }
};
