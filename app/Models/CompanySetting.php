<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'brand_name_primary',
        'brand_name_accent',
        'tagline',
        'logo_path',
        'favicon_path',
        'email',
        'phone',
        'support_hours',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'default_currency',
        'enabled_currencies',
        'social_links',
        'footer_lead',
        'newsletter_title',
        'newsletter_text',
        'copyright_text',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'enabled_currencies' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('company_settings.current'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('company_settings.current'));
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        $name = config('app.name', 'Mero Expense Tracker');

        return [
            'company_name' => $name,
            'brand_name_primary' => 'Mero',
            'brand_name_accent' => 'Expense Tracker',
            'tagline' => 'Professional income and expense tracking with a clean dashboard, smart categories, and secure multi-user access.',
            'email' => 'info.meroexpensetracker@gmail.com',
            'phone' => null,
            'support_hours' => 'Mon–Fri, 9am–6pm (UTC)',
            'address_line1' => null,
            'address_line2' => null,
            'city' => null,
            'state' => null,
            'postal_code' => null,
            'country' => null,
            'default_currency' => 'USD',
            'enabled_currencies' => array_keys(config('currencies', ['USD' => []])),
            'social_links' => [
                ['title' => 'Twitter', 'label' => '𝕏', 'link_url' => '#'],
                ['title' => 'LinkedIn', 'label' => 'in', 'link_url' => '#'],
                ['title' => 'GitHub', 'label' => 'GH', 'link_url' => '#'],
            ],
            'footer_lead' => 'Professional income and expense tracking with a clean dashboard, smart categories, and secure multi-user access.',
            'newsletter_title' => 'Stay updated',
            'newsletter_text' => 'Tips and product news. Unsubscribe anytime.',
            'copyright_text' => null,
        ];
    }

    public function logoUrl(): ?string
    {
        return $this->assetUrl($this->logo_path);
    }

    public function faviconUrl(): ?string
    {
        return $this->assetUrl($this->favicon_path);
    }

    public function assetUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function displayName(): string
    {
        if (filled($this->brand_name_primary) && filled($this->brand_name_accent)) {
            return trim($this->brand_name_primary.' '.$this->brand_name_accent);
        }

        return $this->company_name;
    }

    public function formattedAddress(): ?string
    {
        $lines = array_filter([
            $this->address_line1,
            $this->address_line2,
            collect([$this->city, $this->state, $this->postal_code])->filter()->implode(', '),
            $this->country,
        ]);

        return $lines !== [] ? implode("\n", $lines) : null;
    }

    /** @return array<int, array<string, mixed>> */
    public function socialLinkList(): array
    {
        return $this->social_links ?? [];
    }
}
