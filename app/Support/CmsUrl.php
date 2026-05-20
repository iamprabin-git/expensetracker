<?php

namespace App\Support;

class CmsUrl
{
    public static function resolve(?string $url): string
    {
        if (! filled($url)) {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:')) {
            return $url;
        }

        return url($url);
    }
}
