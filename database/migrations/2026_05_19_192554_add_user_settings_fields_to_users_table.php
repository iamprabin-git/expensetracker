<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email');
            $table->string('phone', 30)->nullable()->after('avatar_path');
            $table->string('currency', 3)->default('USD')->after('phone');
            $table->string('timezone', 64)->default('UTC')->after('currency');
            $table->string('locale', 10)->default('en')->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'phone', 'currency', 'timezone', 'locale']);
        });
    }
};
