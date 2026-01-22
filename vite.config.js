import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ========== CSS ==========
                'resources/css/app.css',
                'resources/css/critical/guest.css',
                'resources/css/critical/admin.css',
                'resources/css/critical/organizer.css',

                // ========== JS Global ==========
                'resources/js/app.js',

                // ========== JS Admin ==========
                'resources/js/admin/dashboard.js',
                'resources/js/admin/reports.js',

                // ========== JS Organizer ==========
                'resources/js/organizer/event-form.js',
                'resources/js/organizer/ticket-scanner.js',
                'resources/js/organizer/analytics.js',

                // ========== JS Pages ==========
                'resources/js/pages/checkout.js',
                'resources/js/pages/event-detail.js',
                'resources/js/pages/event-search.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'chart': ['chart.js'],           // Separate Chart.js
                    'qrcode': ['qrcode'],            // Separate QR library
                    'dropzone': ['dropzone'],        // Separate Dropzone
                }
            }
        }
    }
});
