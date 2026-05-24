<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->where('type', 'both')
            ->update(['type' => 'expense']);
    }

    public function down(): void
    {
        // Cannot reliably restore which categories were originally "both".
    }
};
