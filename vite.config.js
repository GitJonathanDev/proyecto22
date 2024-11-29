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
                    // Asegurándonos de que los recursos se resuelvan correctamente
                    base: '/inf513/grupo01cc/proyecto22/public', // La ruta base de tus assets
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // Configuración de la ruta base para todo el proyecto (también es útil para la generación de recursos)
    base: '/inf513/grupo01cc/proyecto22/public/', 
});
