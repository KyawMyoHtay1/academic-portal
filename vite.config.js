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
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes("node_modules")) {
                        return;
                    }

                    if (
                        id.includes("chart.js") ||
                        id.includes("vue-chartjs")
                    ) {
                        return "charts";
                    }

                    if (
                        id.includes("laravel-echo") ||
                        id.includes("pusher-js")
                    ) {
                        return "realtime";
                    }

                    if (
                        id.includes("@inertiajs") ||
                        id.includes("/vue/") ||
                        id.includes("/ziggy/")
                    ) {
                        return "framework";
                    }

                    return "vendor";
                },
            },
        },
    },
});
