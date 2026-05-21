import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { legacyMediaQueriesBundle } from './vite-legacy-media-queries.js';

export default defineConfig({
    css: {
        transformer: 'postcss',
    },
    plugins: [
        legacyMediaQueriesBundle(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/analysis.js',
            ],
            refresh: true,
        }),
    ],
});
