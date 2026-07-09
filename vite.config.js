import { defineConfig } from 'vite';

// Vite build pipeline for TAVP Core frontend assets.
export default defineConfig({
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: [
                'resources/assets/js/app.js',
                'resources/assets/css/app.css',
            ],
        },
    },
    server: {
        port: 5173,
    },
});
