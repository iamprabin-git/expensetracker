<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->where(function ($query) {
                $query->where('is_approved', true)
                    ->orWhereNotNull('google_id');
            })
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Non-reversible safely.
    }
};
