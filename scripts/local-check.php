<?php

use App\Models\SitePage;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$checks = [
    'APP_URL' => config('app.url'),
    'public/hot' => is_file(public_path('hot')) ? file_get_contents(public_path('hot')) : '(none — good for built assets)',
    'build/manifest' => is_file(public_path('build/manifest.json')) ? 'OK' : 'MISSING — run: npm run build',
    'site_pages' => (string) SitePage::count(),
    'VITE_USE_DEV' => env('VITE_USE_DEV', false) ? 'true' : 'false',
];

foreach ($checks as $k => $v) {
    echo str_pad($k.':', 18).$v.PHP_EOL;
}
