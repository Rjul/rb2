import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import basicSsl from '@vitejs/plugin-basic-ssl'
import viteCommonjs from 'vite-plugin-commonjs' ;
import requireTransform from 'vite-plugin-require-transform';
const path = require('path')

export default defineConfig({
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/js/main.js',
                'resources/js/admin/admin.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        commonjsOptions: {
            transformMixedEsModules: true
        }
    }
});
