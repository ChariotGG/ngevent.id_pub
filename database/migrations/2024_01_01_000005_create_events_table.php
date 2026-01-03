<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained();
            $table->foreignId('category_id')->constrained();

            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            // Media
            $table->string('poster')->nullable();
            $table->string('banner')->nullable();
            $table->json('gallery')->nullable();

            // Date & Time
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('timezone', 50)->default('Asia/Jakarta');

            // Location
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('online_url')->nullable();

            // Status
            $table->string('status', 20)->default('draft'); // draft, pending_review, approved, published, cancelled, completed
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_free')->default(false);

            // Pricing cache
            $table->integer('min_price')->default(0);
            $table->integer('max_price')->default(0);

            // Documents
            $table->string('proposal_file')->nullable();

            // Admin
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

            // Stats
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('start_date');
            $table->index('city');
            $table->index('is_featured');
            $table->index(['status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
