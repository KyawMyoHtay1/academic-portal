import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: "127.0.0.1",
        port: 5173,
        strictPort: true,
        origin: "http://127.0.0.1:5173",
        cors: {
            origin: [
                "http://127.0.0.1:8000",
                "http://localhost:8000",
                "http://127.0.0.1",
                "http://localhost",
            ],
        },
        hmr: {
            host: "127.0.0.1",
            port: 5173,
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
