<?php

namespace App\Support;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryIcons
{
    public const DEFAULT = 'tag';

    /** @var array<string, string> */
    private const LABELS = [
        'tag' => 'Tag',
        'banknotes' => 'Money',
        'briefcase' => 'Work',
        'computer-desktop' => 'Freelance',
        'utensils' => 'Food & dining',
        'truck' => 'Transport',
        'shopping-bag' => 'Shopping',
        'document-text' => 'Bills & documents',
        'home' => 'Home',
        'heart' => 'Health',
        'academic-cap' => 'Education',
        'gift' => 'Gifts',
        'bolt' => 'Utilities',
        'chart-bar' => 'Investments',
        'building-library' => 'Property',
        'credit-card' => 'Credit & loans',
        'device-phone-mobile' => 'Phone & tech',
        'film' => 'Entertainment',
        'plane' => 'Travel',
        'wrench-screwdriver' => 'Repairs',
    ];

    /** @var array<string, string> */
    private const NAME_HINTS = [
        'salary' => 'banknotes',
        'freelance' => 'computer-desktop',
        'food' => 'utensils',
        'dining' => 'utensils',
        'transport' => 'truck',
        'shopping' => 'shopping-bag',
        'bill' => 'document-text',
        'rent' => 'home',
        'mortgage' => 'home',
        'health' => 'heart',
        'medical' => 'heart',
        'education' => 'academic-cap',
        'gift' => 'gift',
        'utility' => 'bolt',
        'invest' => 'chart-bar',
        'asset' => 'building-library',
        'loan' => 'credit-card',
        'credit' => 'credit-card',
        'phone' => 'device-phone-mobile',
        'entertain' => 'film',
        'travel' => 'plane',
        'repair' => 'wrench-screwdriver',
    ];

    /** @var list<string> */
    private const CHART_PALETTE = [
        '#6366f1',
        '#22c55e',
        '#f97316',
        '#3b82f6',
        '#a855f7',
        '#ef4444',
        '#14b8a6',
        '#eab308',
    ];

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::LABELS);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::LABELS;
    }

    public static function label(?string $icon): string
    {
        $key = self::normalize($icon);

        return self::LABELS[$key] ?? self::LABELS[self::DEFAULT];
    }

    public static function normalize(?string $icon): string
    {
        $key = Str::lower(trim((string) $icon));

        return array_key_exists($key, self::LABELS) ? $key : self::DEFAULT;
    }

    public static function defaultForType(CategoryType $type): string
    {
        return match ($type) {
            CategoryType::Income => 'banknotes',
            CategoryType::Expense => 'shopping-bag',
            CategoryType::Asset => 'building-library',
            CategoryType::Liability => 'credit-card',
        };
    }

    public static function suggestForName(string $name): string
    {
        $lower = Str::lower($name);

        foreach (self::NAME_HINTS as $hint => $icon) {
            if (Str::contains($lower, $hint)) {
                return $icon;
            }
        }

        return self::DEFAULT;
    }

    public static function resolve(Category $category): string
    {
        if ($category->icon) {
            return self::normalize($category->icon);
        }

        return self::suggestForName($category->name)
            ?: self::defaultForType($category->type);
    }

    /**
     * @return list<string>
     */
    public static function chartPalette(): array
    {
        return self::CHART_PALETTE;
    }

    public static function chartColor(int $index): string
    {
        $palette = self::CHART_PALETTE;

        return $palette[$index % count($palette)];
    }
}
