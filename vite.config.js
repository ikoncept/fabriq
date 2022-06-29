import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'
// import react from '@vitejs/plugin-react'
import vue from '@vitejs/plugin-vue2'

export default defineConfig({
    plugins: [
        laravel([
            // 'resources/css/app.css',
            'resources/js/main.js',
        ]),
        // react(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
})
