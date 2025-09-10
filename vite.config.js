import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/styles.css",
                "resources/css/backdrop.css",
                "resources/js/animations.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        minify: "esbuild",
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["gsap", "alpinejs"],
                },
            },
        },
    },
});
