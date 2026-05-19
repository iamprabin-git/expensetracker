<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('role');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->decimal('membership_fee', 10, 2)->nullable()->after('approved_at');
            $table->timestamp('membership_expires_at')->nullable()->after('membership_fee');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_approved',
                'approved_at',
                'membership_fee',
                'membership_expires_at',
            ]);
        });
    }
};
