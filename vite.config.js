import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath } from 'node:url';

export default defineConfig({
    resolve: {
        alias: {
            // utilisé par le SCSS legacy (@import "~bootstrap/...")
            '~bootstrap': fileURLToPath(new URL('node_modules/bootstrap', import.meta.url)),
        },
    },
    plugins: [
        laravel({
            input: [
                // --- Front legacy (Bootstrap / Turbo / Stimulus) : pages pas encore migrées ---
                'resources/js/main.js',
                'resources/js/detann.js',
                'resources/js/admin/admin.js',
                'resources/js/home/home.js',
                'resources/js/list/search.js',
                'node_modules/tarteaucitronjs/tarteaucitron.js',

                // --- Nouveau front TALL (Tailwind + Livewire + Alpine) ---
                'resources/css/tall.css',
                'resources/js/tall.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
