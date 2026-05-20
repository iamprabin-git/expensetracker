<?php

namespace App\Support;

use App\Models\CompanySetting;

class Currencies
{
    /** @return array<string, array{name: string, symbol: string, decimals?: int}> */
    public static function all(): array
    {
        return config('currencies', []);
    }

    /** @return list<string> */
    public static function enabledCodes(): array
    {
        $enabled = CompanySetting::current()->enabled_currencies;

        if (! is_array($enabled) || $enabled === []) {
            return array_keys(self::all());
        }

        return array_values(array_intersect(array_keys(self::all()), $enabled));
    }

    /** @return array<string, array{name: string, symbol: string, decimals?: int}> */
    public static function enabled(): array
    {
        return array_intersect_key(self::all(), array_flip(self::enabledCodes()));
    }

    public static function defaultCode(): string
    {
        $default = strtoupper((string) (CompanySetting::current()->default_currency ?? 'USD'));

        if (in_array($default, self::enabledCodes(), true)) {
            return $default;
        }

        return self::enabledCodes()[0] ?? 'USD';
    }

    /** @return array<string, string> */
    public static function selectOptions(): array
    {
        return collect(self::enabled())
            ->mapWithKeys(fn (array $meta, string $code): array => [
                $code => self::formatLabel($code, $meta),
            ])
            ->all();
    }

    /** @return array<string, string> */
    public static function checkboxOptions(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn (array $meta, string $code): array => [
                $code => self::formatLabel($code, $meta),
            ])
            ->all();
    }

    public static function symbol(?string $code): string
    {
        $code = strtoupper((string) ($code ?? self::defaultCode()));

        return self::all()[$code]['symbol'] ?? '$';
    }

    public static function isEnabled(string $code): bool
    {
        return in_array(strtoupper($code), self::enabledCodes(), true);
    }

    /** @param  array{name: string, symbol: string}  $meta */
    public static function formatLabel(string $code, array $meta): string
    {
        return "{$code} — {$meta['name']} ({$meta['symbol']})";
    }
}
