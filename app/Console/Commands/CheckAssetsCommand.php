<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAssetsCommand extends Command
{
    protected $signature = 'assets:check {--fix : Remove public/hot if present}';

    protected $description = 'Verify Vite build files exist (fixes missing CSS/JS on production)';

    public function handle(): int
    {
        $manifestPath = public_path('build/manifest.json');
        $hotPath = public_path('hot');

        if (is_file($hotPath)) {
            if ($this->option('fix')) {
                unlink($hotPath);
                $this->warn('Removed public/hot.');
            } else {
                $this->error('Found public/hot — Laravel will try to load http://127.0.0.1:5173 (dev server), not public/build/.');
                $this->line('Fix: delete public/hot on the server, or run: php artisan assets:check --fix');

                return self::FAILURE;
            }
        }

        if (! is_file($manifestPath)) {
            $this->error('Missing public/build/manifest.json');
            $this->line('Run locally: npm ci && npm run build');
            $this->line('Then upload the public/build folder to your server.');

            return self::FAILURE;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $missing = [];

        foreach ($manifest as $entry) {
            if (! isset($entry['file'])) {
                continue;
            }

            $file = public_path('build/'.$entry['file']);

            if (! is_file($file)) {
                $missing[] = $entry['file'];
            }
        }

        if ($missing !== []) {
            $this->error('Manifest references missing files:');
            foreach ($missing as $path) {
                $this->line('  - build/'.$path);
            }

            return self::FAILURE;
        }

        $this->info('Vite assets OK ('.count($manifest).' entries in manifest).');

        return self::SUCCESS;
    }
}
