import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: '/inf513/grupo01cc/proyecto22/public/',
                    includeAbsolute: false,
                },
            },
        }),
    ],
    base: '/inf513/grupo01cc/proyecto22/public/',
    server: {
        proxy: {
            // Redirige las peticiones a tu servidor si es necesario
            '/inf513': {
                target: 'http://mail.tecnoweb.org.bo',
                changeOrigin: true,
                secure: false,
                rewrite: (path) => path.replace(/^\/inf513/, ''),
            },
        },
        cors: true, // Habilita CORS
    },
});
