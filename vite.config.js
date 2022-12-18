import vue from '@vitejs/plugin-vue2'
import laravel from 'laravel-vite-plugin'
import { defineConfig, splitVendorChunkPlugin } from 'vite'

export default defineConfig({
    plugins: [
        laravel([
            // 'resources/css/app.css',
            'resources/js/fabriq.js',
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
        splitVendorChunkPlugin()
    ],
})
