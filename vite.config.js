import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { legacyMediaQueriesBundle } from './vite-legacy-media-queries.js';

export default defineConfig({
    css: {
        transformer: 'postcss',
    },
    server: {
        // Bind IPv4 so http://127.0.0.1:5173 and http://localhost:5173 both work on Windows.
        host: process.env.VITE_DEV_HOST ?? '127.0.0.1',
        port: Number(process.env.VITE_DEV_PORT ?? 5173),
        strictPort: true,
        origin: `http://${process.env.VITE_DEV_HOST ?? '127.0.0.1'}:${process.env.VITE_DEV_PORT ?? 5173}`,
        hmr: {
            host: process.env.VITE_DEV_HOST ?? '127.0.0.1',
        },
    },
    plugins: [
        tailwindcss(),
        legacyMediaQueriesBundle(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/analysis.css',
                'resources/css/statement.css',
                'resources/css/transactions-page.css',
                'resources/css/reviews-carousel.css',
                'resources/js/app.js',
                'resources/js/reviews-carousel.js',
                'resources/js/analysis.js',
                'resources/js/ai-scan-entry.js',
            ],
            refresh: true,
        }),
    ],
});
