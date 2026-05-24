<?php

use App\Models\Category;
use App\Support\CategoryIcons;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon', 40)->default(CategoryIcons::DEFAULT)->after('type');
        });

        Category::query()->each(function (Category $category): void {
            $category->forceFill([
                'icon' => CategoryIcons::suggestForName($category->name) !== CategoryIcons::DEFAULT
                    ? CategoryIcons::suggestForName($category->name)
                    : CategoryIcons::defaultForType($category->type),
            ])->saveQuietly();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('color', 20)->default('#6366f1')->after('type');
        });

        Category::query()->each(function (Category $category): void {
            $category->forceFill(['color' => '#6366f1'])->saveQuietly();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
