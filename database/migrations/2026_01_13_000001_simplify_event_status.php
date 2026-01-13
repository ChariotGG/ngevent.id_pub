<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing events dengan status pending_review/approved ke published
        DB::table('events')
            ->whereIn('status', ['pending_review', 'approved'])
            ->update(['status' => 'published']);

        // Update rejected/cancelled ke draft
        DB::table('events')
            ->whereIn('status', ['cancelled'])
            ->update(['status' => 'draft']);
    }

    public function down(): void
    {
        // Tidak perlu rollback untuk MVP
    }
};
