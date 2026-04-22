import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',

                // Pages publiques
                'resources/css/pages/about.css',
                'resources/css/pages/legal.css',
                'resources/css/pages/faq.css',
                'resources/css/pages/home.css',
                'resources/css/pages/city.css',
                'resources/css/pages/contact.css',
                'resources/css/pages/destinations.css',
                'resources/css/pages/country.css',
                'resources/css/pages/offers/index.css',
                'resources/css/pages/offers/show.css',

                // Auth
                'resources/css/pages/auth/auth.css',

                // Blog
                'resources/css/pages/blog/index.css',
                'resources/css/pages/blog/show.css',

                // Bookings
                'resources/css/pages/bookings/create.css',
                'resources/css/pages/bookings/index.css',
                'resources/css/pages/bookings/show.css',
                'resources/css/pages/bookings/payment.css',

                // Espace client
                'resources/css/pages/account/dashboard.css',
                'resources/css/pages/account/layout.css',
                'resources/css/pages/account/bookings.css',
                'resources/css/pages/account/profile.css',
                'resources/css/pages/account/security.css',
                'resources/css/pages/account/wishlist.css',

                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        manifest: 'manifest.json',
        outDir: 'public/build',

        // Minification agressive en production
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,   // Supprime console.log en prod
                drop_debugger: true,
                pure_funcs: ['console.info', 'console.debug', 'console.warn'],
            },
        },

        // CSS minifiée
        cssMinify: true,

        // Seuil d'avertissement chunk (500KB)
        chunkSizeWarningLimit: 500,

        rollupOptions: {
            output: {
                // Séparer Alpine.js dans son propre chunk
                // → chargé en parallèle, mis en cache indépendamment
                manualChunks: {
                    alpine: ['alpinejs'],
                },

                // Nommage avec hash pour le cache busting
                entryFileNames:  'js/[name]-[hash].js',
                chunkFileNames:  'js/[name]-[hash].js',
                assetFileNames:  'assets/[name]-[hash][extname]',
            },
        },
    },

    server: {
        hmr: {
            host: 'localhost',
        },
    },

    // Optimisation des dépendances en dev
    optimizeDeps: {
        include: ['alpinejs', 'axios'],
    },
});