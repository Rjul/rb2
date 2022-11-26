import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import basicSsl from '@vitejs/plugin-basic-ssl'
import viteCommonjs from 'vite-plugin-commonjs' ;
import requireTransform from 'vite-plugin-require-transform';
const path = require('path')

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
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
