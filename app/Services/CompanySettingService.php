<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;

class CompanySettingService
{
    private const CACHE_KEY = 'company_settings.current';

    private ?CompanySetting $settings = null;

    public function get(): CompanySetting
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $cached = Cache::get(self::CACHE_KEY);

        if (! is_array($cached)) {
            Cache::forget(self::CACHE_KEY);
            $cached = null;
        }

        if ($cached === null) {
            $cached = CompanySetting::current()->toArray();
            Cache::put(self::CACHE_KEY, $cached, 3600);
        }

        $this->settings = (new CompanySetting)->forceFill($cached);
        $this->settings->exists = true;

        return $this->settings;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->settings = null;
    }
}
