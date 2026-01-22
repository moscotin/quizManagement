import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        proxy: {
            // все запросы к 5173/img/... уйдут на Laravel
            '/img': 'http://127.0.0.1:8000',
            '/images': 'http://127.0.0.1:8000',
            '/storage': 'http://127.0.0.1:8000',
        },
    },
});
