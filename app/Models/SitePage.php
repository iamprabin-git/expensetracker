<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SitePage extends Model
{
    /** Built-in pages with dedicated routes and views. */
    public const SYSTEM_SLUGS = [
        'home',
        'features',
        'pricing',
        'about',
        'faq',
        'contact',
        'privacy',
        'terms',
    ];

    /** Slugs that cannot be used for new pages (app routes & reserved words). */
    public const RESERVED_SLUGS = [
        'admin',
        'login',
        'logout',
        'register',
        'dashboard',
        'settings',
        'profile',
        'api',
        'auth',
        'pages',
        'page',
        'notifications',
        'account',
        'analysis',
        'reports',
        'transactions',
        'categories',
        'budgets',
        'reminders',
        'site-layout',
    ];

    protected $fillable = [
        'slug',
        'label',
        'title',
        'meta_description',
        'is_published',
        'hero_badge',
        'hero_title',
        'hero_lead',
        'hero_image',
        'sections',
        'body_html',
        'extras',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sections' => 'array',
            'extras' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_pages.all'));
        static::deleted(fn () => Cache::forget('site_pages.all'));
    }

    public function imageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function heroImageUrl(): ?string
    {
        return $this->imageUrl($this->hero_image);
    }

    /** @return array<int, array<string, mixed>> */
    public function sectionList(): array
    {
        return $this->sections ?? [];
    }

    public function extra(string $key, mixed $default = null): mixed
    {
        return data_get($this->extras, $key, $default);
    }

    public function isSystem(): bool
    {
        return in_array($this->slug, self::SYSTEM_SLUGS, true);
    }

    public function isCustom(): bool
    {
        return ! $this->isSystem();
    }

    public static function isReservedSlug(string $slug): bool
    {
        $slug = self::normalizeSlug($slug);

        return in_array($slug, array_merge(self::SYSTEM_SLUGS, self::RESERVED_SLUGS), true);
    }

    public static function normalizeSlug(string $value): string
    {
        return Str::slug($value);
    }

    public function publicUrl(): string
    {
        return url($this->publicPath());
    }

    public function publicPath(): string
    {
        return match ($this->slug) {
            'home' => '/',
            'features' => '/features',
            'pricing' => '/pricing',
            'about' => '/about',
            'faq' => '/faq',
            'contact' => '/contact',
            'privacy' => '/privacy',
            'terms' => '/terms',
            default => '/pages/'.$this->slug,
        };
    }

    public function hasReviewsSection(): bool
    {
        return collect($this->sectionList())->contains(fn (array $section): bool => ($section['type'] ?? '') === 'reviews');
    }
}
