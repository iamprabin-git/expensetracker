<?php

namespace App\Services;

use App\Models\SitePage;
use Database\Seeders\SitePageSeeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SiteContentService
{
    private const CACHE_KEY = 'site_pages.all';

    /** @var Collection<string, SitePage>|null */
    private ?Collection $pages = null;

    public function get(string $slug): SitePage
    {
        $this->ensureSystemPagesExist();

        $pages = $this->all();

        $page = $pages->get($slug);

        if (! $page) {
            abort(404, "Site page [{$slug}] is not configured.");
        }

        if (! $page->is_published && ! $this->allowUnpublished()) {
            abort(404);
        }

        return $page;
    }

    /** @return Collection<string, SitePage> */
    public function all(): Collection
    {
        if ($this->pages !== null) {
            return $this->pages;
        }

        $cached = Cache::get(self::CACHE_KEY);

        if (! is_array($cached)) {
            Cache::forget(self::CACHE_KEY);
            $cached = null;
        }

        if ($cached === null) {
            $cached = SitePage::query()
                ->get()
                ->keyBy('slug')
                ->map(fn (SitePage $page): array => $page->toArray())
                ->all();

            Cache::put(self::CACHE_KEY, $cached, 3600);
        }

        $this->pages = $this->hydratePages($cached);

        return $this->pages;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->pages = null;
    }

    /** Seed marketing pages when the database is empty (avoids 404 on /). */
    public function ensureSystemPagesExist(): void
    {
        if (SitePage::query()->exists()) {
            return;
        }

        (new SitePageSeeder)->run();
        $this->flush();
    }

    /**
     * @param  array<string, array<string, mixed>>  $cached
     * @return Collection<string, SitePage>
     */
    private function hydratePages(array $cached): Collection
    {
        return collect($cached)->map(function (array $attributes): SitePage {
            $page = new SitePage;
            $page->forceFill($attributes);
            $page->exists = true;

            return $page;
        });
    }

    private function allowUnpublished(): bool
    {
        return app()->environment('local');
    }
}
