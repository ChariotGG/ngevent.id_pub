<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 50); // instagram, twitter, facebook, youtube, tiktok, website
            $table->string('url', 500);
            $table->timestamps();

            $table->index('organizer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_social_links');
    }
};
