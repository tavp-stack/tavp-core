import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [],
    server: {
        host: '0.0.0.0',
        port: 5173,
        proxy: {
            'http://localhost:9000': {
                target: 'http://0.0.0.0:8000',
                changeOrigin: true,
            },
        },
        hmr: {
            host: '0.0.0.0',
            port: 5173,
        },
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
        },
    },
});
