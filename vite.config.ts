import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    // Ensure the dev server listens on all interfaces inside Docker (0.0.0.0)
    // but advertise a browser-usable host (localhost) for the HMR/client URLs.
    server: {
        host: '0.0.0.0',
        port: Number(process.env.VITE_PORT ?? 5173),
        hmr: {
            host: process.env.VITE_HMR_HOST ?? 'localhost',
            protocol: 'ws',
            port: Number(process.env.VITE_PORT ?? 5173),
        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
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
